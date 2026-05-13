<?php

declare(strict_types=1);

namespace App\Tests\Unit\Command\Chat;

use App\Command\Chat\ChatProtocol;
use PHPUnit\Framework\TestCase;

class ChatProtocolTest extends TestCase
{
    public function testParseNickValid(): void
    {
        self::assertSame('alice', ChatProtocol::parseNick("NICK alice\n"));
    }

    public function testParseNickWithoutNewline(): void
    {
        self::assertSame('alice', ChatProtocol::parseNick('NICK alice'));
    }

    public function testParseNickWithCrlf(): void
    {
        self::assertSame('bob', ChatProtocol::parseNick("NICK bob\r\n"));
    }

    public function testParseNickContainingSpaceReturnsNull(): void
    {
        self::assertNull(ChatProtocol::parseNick("NICK alice bob\n"));
    }

    public function testParseNickEmptyReturnsNull(): void
    {
        self::assertNull(ChatProtocol::parseNick("NICK \n"));
    }

    public function testParseNickWrongPrefixReturnsNull(): void
    {
        self::assertNull(ChatProtocol::parseNick("MSG hello\n"));
    }

    public function testParseMsgValid(): void
    {
        self::assertSame('hello world', ChatProtocol::parseMsg("MSG hello world\n"));
    }

    public function testParseMsgEmptyReturnsNull(): void
    {
        self::assertNull(ChatProtocol::parseMsg("MSG \n"));
    }

    public function testParseMsgWhitespaceOnlyReturnsNull(): void
    {
        self::assertNull(ChatProtocol::parseMsg("MSG    \n"));
    }

    public function testParseMsgWrongPrefixReturnsNull(): void
    {
        self::assertNull(ChatProtocol::parseMsg("NICK foo\n"));
    }

    public function testFormatBroadcast(): void
    {
        $time = new \DateTimeImmutable('2024-01-01 14:32:01');
        self::assertSame("[14:32:01] alice: hi there\n", ChatProtocol::formatBroadcast('alice', 'hi there', $time));
    }

    public function testFormatJoin(): void
    {
        self::assertSame("*** alice joined the chat ***\n", ChatProtocol::formatJoin('alice'));
    }

    public function testFormatLeave(): void
    {
        self::assertSame("*** alice left the chat ***\n", ChatProtocol::formatLeave('alice'));
    }
}