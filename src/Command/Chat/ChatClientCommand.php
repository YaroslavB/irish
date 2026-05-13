<?php

declare(strict_types=1);

namespace App\Command\Chat;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ChatClientCommand extends Command
{
    protected static $defaultName = 'app:chat:client';
    protected static $defaultDescription = 'Connect to the TCP chat server and start chatting';

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('nickname', InputArgument::REQUIRED, 'Your display name in the chat')
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'Server port', 1337)
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Server host', '127.0.0.1');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $nickname = (string) $input->getArgument('nickname');
        $port = (int) $input->getOption('port');
        $host = (string) $input->getOption('host');

        if ($nickname === '') {
            $io->error('Nickname cannot be empty.');

            return Command::FAILURE;
        }

        if (str_contains($nickname, ' ')) {
            $io->error('Nickname cannot contain spaces.');

            return Command::FAILURE;
        }

        if (strlen($nickname) > 30) {
            $io->error('Nickname cannot exceed 30 characters.');

            return Command::FAILURE;
        }

        if ($port < 1 || $port > 65535) {
            $io->error(sprintf('Invalid port: %d.', $port));

            return Command::FAILURE;
        }

        try {
            $socket = $this->connect($host, $port);
        } catch (\RuntimeException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        fwrite($socket, sprintf("NICK %s\n", $nickname));
        $this->runClientLoop($socket, $io);
        fclose($socket);

        return Command::SUCCESS;
    }

    /**
     * @return resource
     * @throws \RuntimeException
     */
    private function connect(string $host, int $port): mixed
    {
        $socket = stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, 5);

        if ($socket === false) {
            throw new \RuntimeException(
                sprintf('Cannot connect to %s:%d — %s (%d)', $host, $port, $errstr, $errno)
            );
        }

        stream_set_blocking($socket, false);

        return $socket;
    }

    /** @param resource $socket */
    private function runClientLoop(mixed $socket, SymfonyStyle $io): void
    {
        $io->writeln('<info>Connected. Type messages and press Enter. Ctrl+C to quit.</info>');

        $stdin = STDIN;

        while (true) {
            $read = [$socket, $stdin];
            $write = null;
            $except = null;

            $changed = stream_select($read, $write, $except, 0, 200000);

            if ($changed === false) {
                break;
            }

            if (in_array($socket, $read, true)) {
                $line = fgets($socket, 4096);
                if ($line === false || $line === '') {
                    $io->writeln('<comment>*** Disconnected from server ***</comment>');
                    break;
                }
                // \r overwrites the current input line for cleaner display
                echo "\r" . rtrim($line, "\r\n") . "\n";
            }

            if (in_array($stdin, $read, true)) {
                $line = fgets($stdin, 4096);
                if ($line === false) {
                    break;
                }
                $text = rtrim($line, "\r\n");
                if ($text !== '') {
                    fwrite($socket, sprintf("MSG %s\n", $text));
                }
            }
        }
    }
}
