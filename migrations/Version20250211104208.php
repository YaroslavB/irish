<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250211104208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status INT NOT NULL, total_price DOUBLE PRECISION DEFAULT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_deleted TINYINT(1) NOT NULL, INDEX IDX_F52993987E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE order_product (id INT AUTO_INCREMENT NOT NULL, app_order_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, price_per_one NUMERIC(6, 2) NOT NULL, INDEX IDX_2530ADE6851F0D95 (app_order_id), INDEX IDX_2530ADE64584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE `order` ADD CONSTRAINT FK_F52993987E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)'
        );
        $this->addSql(
            'ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE6851F0D95 FOREIGN KEY (app_order_id) REFERENCES `order` (id)'
        );
        $this->addSql(
            'ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE64584665A FOREIGN KEY (product_id) REFERENCES product (id)'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'ALTER TABLE `order` DROP FOREIGN KEY FK_F52993987E3C61F9'
        );
        $this->addSql(
            'ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE6851F0D95'
        );
        $this->addSql(
            'ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE64584665A'
        );
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_product');
    }
}
