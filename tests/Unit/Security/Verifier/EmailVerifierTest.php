<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security\Verifier;

use App\Entity\User;
use App\Security\Verifier\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifierTest extends TestCase
{
    private EmailVerifier $emailVerifier;
    private MockObject&VerifyEmailHelperInterface $verifyEmailHelper;
    private MockObject&MailerInterface $mailer;
    private MockObject&EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->verifyEmailHelper = $this->createMock(VerifyEmailHelperInterface::class);
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->emailVerifier = new EmailVerifier(
            $this->verifyEmailHelper,
            $this->mailer,
            $this->entityManager
        );
    }


    public function testHandleEmailConfirmation(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);
        $user->method('getEmail')->willReturn('test@example.com');
        
        $user->expects($this->once())
            ->method('setIsVerified')
            ->with(true);

        $request = $this->createMock(Request::class);
        $request->method('getUri')->willReturn('https://example.com/verify?token=abc');

        $this->verifyEmailHelper
            ->expects($this->once())
            ->method('validateEmailConfirmation')
            ->with('https://example.com/verify?token=abc', '1', 'test@example.com');

        $this->entityManager->expects($this->once())->method('persist')->with($user);
        $this->entityManager->expects($this->once())->method('flush');

        $this->emailVerifier->handleEmailConfirmation($request, $user);
    }
}

