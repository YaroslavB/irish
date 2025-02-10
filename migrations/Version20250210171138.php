<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210171138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'ALTER TABLE cart CHANGE created_ad created_at DATETIME NOT NULL'
        );
        $this->addSql(
            'ALTER TABLE cart_product CHANGE quatity quantity INT NOT NULL'
        );
        $this->addSql('DROP INDEX UNIQ_1C1B038BD17F50A6 ON product');
        $this->addSql(
            'ALTER TABLE product CHANGE created_at created_at DATETIME NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE uuid uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\''
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_D34A04AD989D9B62 ON product (slug)'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'ALTER TABLE cart CHANGE created_at created_ad DATETIME NOT NULL'
        );
        $this->addSql('DROP INDEX UNIQ_D34A04AD989D9B62 ON product');
        $this->addSql(
            'ALTER TABLE product CHANGE uuid uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE description description LONGTEXT NOT NULL'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_1C1B038BD17F50A6 ON product (uuid)'
        );
        $this->addSql(
            'ALTER TABLE cart_product CHANGE quantity quatity INT NOT NULL'
        );
    }
}
