<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170409075622 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE portfolio_image ADD job_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE portfolio_image ADD CONSTRAINT FK_98652E1ABE04EA9 FOREIGN KEY (job_id) REFERENCES jobs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_98652E1ABE04EA9 ON portfolio_image (job_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE portfolio_image DROP CONSTRAINT FK_98652E1ABE04EA9');
        $this->addSql('DROP INDEX IDX_98652E1ABE04EA9');
        $this->addSql('ALTER TABLE portfolio_image DROP job_id');
    }
}
