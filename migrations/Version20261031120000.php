<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261031120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'order.total_price float -> decimal(10,2); user.zipcode int -> varchar(10)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `order` CHANGE total_price total_price NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE zipcode zipcode VARCHAR(10) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `order` CHANGE total_price total_price DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE zipcode zipcode INT DEFAULT NULL');
    }
}
