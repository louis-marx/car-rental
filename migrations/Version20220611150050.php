<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220611150050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE voiture ADD parking_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810FF17B2DD FOREIGN KEY (parking_id) REFERENCES parking (id)');
        $this->addSql('CREATE INDEX IDX_E9E2810FF17B2DD ON voiture (parking_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810FF17B2DD');
        $this->addSql('DROP INDEX IDX_E9E2810FF17B2DD ON voiture');
        $this->addSql('ALTER TABLE voiture DROP parking_id');
    }
}
