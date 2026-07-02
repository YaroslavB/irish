<?php

declare(strict_types=1);

namespace App\Command\Chat;

use DateTimeImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ChatServerCommand extends Command
{
    protected static $defaultName = 'app:chat:server';
    protected static $defaultDescription = 'Start the TCP chat server (multi-client, non-blocking)';

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'TCP port to listen on', 1337)
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Host/IP to bind', '0.0.0.0');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $port = (int) $input->getOption('port');
        $host = (string) $input->getOption('host');

        if ($port < 1 || $port > 65535) {
            $io->error(sprintf('Invalid port: %d. Must be between 1 and 65535.', $port));

            return Command::FAILURE;
        }

        $io->title(sprintf('Chat server on %s:%d', $host, $port));
        $this->startServer($host, $port, $io);

        return Command::SUCCESS;
    }

    private function startServer(string $host, int $port, SymfonyStyle $io): void
    {
        $serverSocket = stream_socket_server("tcp://{$host}:{$port}", $errno, $errstr);
        if ($serverSocket === false) {
            $io->error(sprintf('Cannot bind to %s:%d — %s (%d)', $host, $port, $errstr, $errno));

            return;
        }

        stream_set_blocking($serverSocket, false);
        $io->success('Server ready. Waiting for connections. Press Ctrl+C to stop.');

        /** @var array<int, resource> $clients */
        $clients = [];
        /** @var array<int, string> $nicknames */
        $nicknames = [];
        /** @var array<int, resource> $pending */
        $pending = [];

        $this->runLoop($serverSocket, $clients, $nicknames, $pending, $io);

        fclose($serverSocket);
    }

    /**
     * @param resource $serverSocket
     * @param array<int, resource> $clients
     * @param array<int, string> $nicknames
     * @param array<int, resource> $pending
     */
    private function runLoop(
        mixed $serverSocket,
        array &$clients,
        array &$nicknames,
        array &$pending,
        SymfonyStyle $io
    ): void {
        while (true) {
            $read = array_merge([$serverSocket], array_values($pending), array_values($clients));
            $write = null;
            $except = null;

            $changed = stream_select($read, $write, $except, 0, 200000);

            if ($changed === false) {
                $io->error('stream_select() failed — stopping server.');
                break;
            }

            if ($changed === 0) {
                continue;
            }

            if (in_array($serverSocket, $read, true)) {
                $newSocket = stream_socket_accept($serverSocket, 0);
                if ($newSocket !== false) {
                    stream_set_blocking($newSocket, false);
                    $id = (int) $newSocket;
                    $pending[$id] = $newSocket;
                    @fwrite($newSocket, "*** Send NICK <username> to join ***\n");
                    $io->writeln(sprintf('New connection pending: %d', $id));
                }
            }

            // Snapshot before processing pending: sockets promoted to $clients this
            // cycle must not be iterated as clients until the next cycle (their
            // readable data was already consumed by handlePending).
            $clientIdsThisCycle = array_keys($clients);

            foreach ($pending as $id => $socket) {
                if (!in_array($socket, $read, true)) {
                    continue;
                }
                $this->handlePending($id, $socket, $pending, $clients, $nicknames, $io);
            }

            foreach ($clientIdsThisCycle as $id) {
                if (!isset($clients[$id])) {
                    continue;
                }
                $socket = $clients[$id];
                if (!in_array($socket, $read, true)) {
                    continue;
                }
                $this->handleClient($id, $socket, $clients, $nicknames, $io);
            }
        }
    }

    /**
     * @param resource $socket
     * @param array<int, resource> $pending
     * @param array<int, resource> $clients
     * @param array<int, string> $nicknames
     */
    private function handlePending(
        int $id,
        mixed $socket,
        array &$pending,
        array &$clients,
        array &$nicknames,
        SymfonyStyle $io
    ): void {
        $line = fgets($socket, 4096);

        if ($line === false || $line === '') {
            if ($line === false && !feof($socket)) {
                return; // EAGAIN — no data yet, not a real disconnect
            }
            fclose($socket);
            unset($pending[$id]);

            return;
        }

        $nick = ChatProtocol::parseNick($line);

        if ($nick === null) {
            @fwrite($socket, "*** Invalid nickname. Send: NICK <username> ***\n");
            fclose($socket);
            unset($pending[$id]);

            return;
        }

        if (strlen($nick) > 30) {
            @fwrite($socket, "*** Nickname too long (max 30 chars). Disconnecting. ***\n");
            fclose($socket);
            unset($pending[$id]);

            return;
        }

        unset($pending[$id]);
        $clients[$id] = $socket;
        $nicknames[$id] = $nick;

        $joinMsg = ChatProtocol::formatJoin($nick);
        $this->broadcast($clients, $joinMsg);
        $io->writeln(sprintf("Client %d joined as '%s'", $id, $nick));
    }

    /**
     * @param resource $socket
     * @param array<int, resource> $clients
     * @param array<int, string> $nicknames
     */
    private function handleClient(
        int $id,
        mixed $socket,
        array &$clients,
        array &$nicknames,
        SymfonyStyle $io
    ): void {
        $line = fgets($socket, 4096);

        if ($line === false || $line === '') {
            if ($line === false && !feof($socket)) {
                return; // EAGAIN — no data yet, not a real disconnect
            }
            $nick = $nicknames[$id] ?? 'unknown';
            fclose($socket);
            unset($clients[$id], $nicknames[$id]);

            $leaveMsg = ChatProtocol::formatLeave($nick);
            $this->broadcast($clients, $leaveMsg);
            $io->writeln(sprintf("Client %d ('%s') disconnected", $id, $nick));

            return;
        }

        $text = ChatProtocol::parseMsg($line);
        if ($text === null) {
            return;
        }

        $nick = $nicknames[$id];
        $message = ChatProtocol::formatBroadcast($nick, $text, new DateTimeImmutable());
        $this->broadcast($clients, $message);
        $io->write($message);
    }

    /**
     * @param array<int, resource> $clients
     */
    private function broadcast(array $clients, string $message): void
    {
        foreach ($clients as $socket) {
            @fwrite($socket, $message);
        }
    }
}
