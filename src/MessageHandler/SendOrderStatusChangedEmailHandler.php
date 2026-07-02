<?php

namespace App\MessageHandler;

use App\Entity\StaticStorage\OrderStaticStorage;
use App\Message\SendOrderStatusChangedEmail;
use App\Repository\OrderRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;

#[AsMessageHandler]
class SendOrderStatusChangedEmailHandler
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly MailerInterface $mailer,
        private readonly string $mailerFrom,
    ) {
    }

    public function __invoke(SendOrderStatusChangedEmail $message): void
    {
        $order = $this->orderRepository->find($message->getOrderId());

        if ($order === null) {
            return;
        }

        $user = $order->getOwner();
        if ($user === null) {
            return;
        }

        $statusList = OrderStaticStorage::getOrderStatusList();
        $statusLabel = $statusList[$message->getNewStatus()] ?? 'Unknown';

        $email = (new TemplatedEmail())
            ->from(new Address($this->mailerFrom, 'Irish Shop'))
            ->to((string) $user->getEmail())
            ->subject(sprintf('Order #%d status updated: %s', (int) $order->getId(), $statusLabel))
            ->htmlTemplate('email/order/status_changed.html.twig')
            ->context([
                'order' => $order,
                'statusLabel' => $statusLabel,
            ]);

        $this->mailer->send($email);
    }
}