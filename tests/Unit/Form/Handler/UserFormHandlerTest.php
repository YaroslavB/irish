<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form\Handler;

use App\Entity\User;
use App\Form\Handler\UserFormHandler;
use App\Utils\Manager\UserManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFormHandlerTest extends TestCase
{
    private UserFormHandler $handler;
    private MockObject&UserManager $userManager;
    private MockObject&UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        $this->userManager = $this->createMock(UserManager::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->handler = new UserFormHandler($this->userManager, $this->passwordHasher);
    }

    public function testProcessEditFormSavesUser(): void
    {
        $user = new User();
        $form = $this->createMock(FormInterface::class);
        
        $form->method('getData')->willReturn($user);
        $form->method('has')
            ->willReturnCallback(fn($field) => false);

        $this->userManager
            ->expects($this->once())
            ->method('save')
            ->with($user);

        $result = $this->handler->processEditForm($form);

        $this->assertSame($user, $result);
    }

    public function testProcessEditFormSetsEmailForNewUser(): void
    {
        $user = new User();
        $form = $this->createMock(FormInterface::class);
        $emailField = $this->createMock(FormInterface::class);
        
        $form->method('getData')->willReturn($user);
        $form->method('has')
            ->willReturnCallback(fn($field) => $field === 'newEmail');
        $form->method('get')
            ->with('newEmail')
            ->willReturn($emailField);
        $emailField->method('getData')->willReturn('new@example.com');

        $this->userManager->expects($this->once())->method('save');

        $result = $this->handler->processEditForm($form);

        $this->assertEquals('new@example.com', $result->getEmail());
    }

    public function testProcessEditFormHashesPassword(): void
    {
        $user = new User();
        $form = $this->createMock(FormInterface::class);
        $passwordField = $this->createMock(FormInterface::class);
        
        $form->method('getData')->willReturn($user);
        $form->method('has')
            ->willReturnCallback(fn($field) => $field === 'planePassword');
        $form->method('get')
            ->with('planePassword')
            ->willReturn($passwordField);
        $passwordField->method('getData')->willReturn('plainPassword123');

        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($user, 'plainPassword123')
            ->willReturn('hashedPassword123');

        $this->userManager->expects($this->once())->method('save');

        $result = $this->handler->processEditForm($form);

        $this->assertEquals('hashedPassword123', $result->getPassword());
    }
}

