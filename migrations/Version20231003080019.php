<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231003080019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE guests (user_id INT NOT NULL, guest_user_id INT NOT NULL, INDEX IDX_4D11BCB2A76ED395 (user_id), INDEX IDX_4D11BCB2E7AB17D9 (guest_user_id), PRIMARY KEY(user_id, guest_user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE guests ADD CONSTRAINT FK_4D11BCB2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE guests ADD CONSTRAINT FK_4D11BCB2E7AB17D9 FOREIGN KEY (guest_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE guests DROP FOREIGN KEY FK_4D11BCB2A76ED395');
        $this->addSql('ALTER TABLE guests DROP FOREIGN KEY FK_4D11BCB2E7AB17D9');
        $this->addSql('DROP TABLE guests');
    }
}
