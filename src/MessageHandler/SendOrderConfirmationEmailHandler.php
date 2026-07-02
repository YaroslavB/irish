<?php

namespace App\MessageHandler;

use App\Message\SendOrderConfirmationEmail;
use App\Repository\OrderRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;

#[AsMessageHandler]
class SendOrderConfirmationEmailHandler
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly MailerInterface $mailer,
        private readonly string $mailerFrom,
    ) {
    }

    public function __invoke(SendOrderConfirmationEmail $message): void
    {
        $order = $this->orderRepository->find($message->getOrderId());

        if ($order === null) {
            return;
        }

        $user = $order->getOwner();
        if ($user === null) {
            return;
        }

        $email = (new TemplatedEmail())
            ->from(new Address($this->mailerFrom, 'Irish Shop'))
            ->to((string) $user->getEmail())
            ->subject(sprintf('Order #%d confirmed', (int) $order->getId()))
            ->htmlTemplate('email/order/confirmation.html.twig')
            ->context(['order' => $order]);

        $this->mailer->send($email);
    }
}
