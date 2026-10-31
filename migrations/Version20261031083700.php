<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261031083700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align user zipcode and order total price columns with entity mappings.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user MODIFY zipcode VARCHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` MODIFY total_price NUMERIC(10, 2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user MODIFY zipcode INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` MODIFY total_price DOUBLE PRECISION DEFAULT NULL');
    }
}
