<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231113144506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add product uuid to product table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'ALTER TABLE product ADD uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\''
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_1C1B038BD17F50A6 ON product (uuid)'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP uuid');
        $this->addSql('DROP INDEX UNIQ_1C1B038BD17F50A6 ON product');
    }
}
