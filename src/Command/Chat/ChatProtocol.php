<?php

declare(strict_types=1);

namespace App\Command\Chat;

final class ChatProtocol
{
    public static function parseNick(string $line): ?string
    {
        $line = rtrim($line, "\r\n");
        if (!str_starts_with($line, 'NICK ')) {
            return null;
        }
        $nick = substr($line, 5);
        if ($nick === '' || str_contains($nick, ' ')) {
            return null;
        }

        return $nick;
    }

    public static function parseMsg(string $line): ?string
    {
        $line = rtrim($line, "\r\n");
        if (!str_starts_with($line, 'MSG ')) {
            return null;
        }
        $text = substr($line, 4);
        if (trim($text) === '') {
            return null;
        }

        return $text;
    }

    public static function formatBroadcast(string $nick, string $text, \DateTimeImmutable $time): string
    {
        return sprintf("[%s] %s: %s\n", $time->format('H:i:s'), $nick, $text);
    }

    public static function formatJoin(string $nick): string
    {
        return sprintf("*** %s joined the chat ***\n", $nick);
    }

    public static function formatLeave(string $nick): string
    {
        return sprintf("*** %s left the chat ***\n", $nick);
    }
}
