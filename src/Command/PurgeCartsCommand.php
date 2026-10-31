<?php

declare(strict_types=1);

namespace App\Command;

use App\Utils\Manager\CartManager;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:cart:purge', description: 'Delete abandoned carts older than N days')]
class PurgeCartsCommand extends Command
{
    public function __construct(private readonly CartManager $cartManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('days', 'd', InputOption::VALUE_REQUIRED, 'Age in days', '30');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $days = (int) $input->getOption('days');
        if ($days < 1) {
            $io->error('--days must be a positive integer');

            return Command::INVALID;
        }

        $removed = $this->cartManager->purgeCreatedBefore(new DateTimeImmutable(sprintf('-%d days', $days)));
        $io->success(sprintf('Removed %d cart(s) older than %d day(s).', $removed, $days));

        return Command::SUCCESS;
    }
}
