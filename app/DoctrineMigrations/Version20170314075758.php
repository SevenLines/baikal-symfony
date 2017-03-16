<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170314075758 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE portfolio_image_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE portfolio_set_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE portfolio_image (id INT NOT NULL, portfolio_set_id INT DEFAULT NULL, title TEXT DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_98652E1A1610E02D ON portfolio_image (portfolio_set_id)');
        $this->addSql('CREATE TABLE portfolio_set (id INT NOT NULL, product_category_id INT DEFAULT NULL, title TEXT DEFAULT NULL, location TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_62AC3633BE6903FD ON portfolio_set (product_category_id)');
        $this->addSql('ALTER TABLE portfolio_image ADD CONSTRAINT FK_98652E1A1610E02D FOREIGN KEY (portfolio_set_id) REFERENCES portfolio_set (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE portfolio_set ADD CONSTRAINT FK_62AC3633BE6903FD FOREIGN KEY (product_category_id) REFERENCES product_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE portfolio_image DROP CONSTRAINT FK_98652E1A1610E02D');
        $this->addSql('DROP SEQUENCE portfolio_image_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE portfolio_set_id_seq CASCADE');
        $this->addSql('DROP TABLE portfolio_image');
        $this->addSql('DROP TABLE portfolio_set');
    }
}
