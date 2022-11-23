<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221122190918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE send_resume (id INT AUTO_INCREMENT NOT NULL, resume_id INT NOT NULL, company_id INT NOT NULL, date_create DATETIME DEFAULT NULL, INDEX IDX_FF175600D262AF09 (resume_id), INDEX IDX_FF175600979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE send_resume ADD CONSTRAINT FK_FF175600D262AF09 FOREIGN KEY (resume_id) REFERENCES resume (id)');
        $this->addSql('ALTER TABLE send_resume ADD CONSTRAINT FK_FF175600979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE send_resume DROP FOREIGN KEY FK_FF175600D262AF09');
        $this->addSql('ALTER TABLE send_resume DROP FOREIGN KEY FK_FF175600979B1AD6');
        $this->addSql('DROP TABLE send_resume');
    }
}
