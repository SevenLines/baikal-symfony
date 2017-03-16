<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170316075815 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE portfolio_image_product_category (portfolio_image_id INT NOT NULL, product_category_id INT NOT NULL, PRIMARY KEY(portfolio_image_id, product_category_id))');
        $this->addSql('CREATE INDEX IDX_D1DCFBC0412F7FF5 ON portfolio_image_product_category (portfolio_image_id)');
        $this->addSql('CREATE INDEX IDX_D1DCFBC0BE6903FD ON portfolio_image_product_category (product_category_id)');
        $this->addSql('ALTER TABLE portfolio_image_product_category ADD CONSTRAINT FK_D1DCFBC0412F7FF5 FOREIGN KEY (portfolio_image_id) REFERENCES portfolio_image (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE portfolio_image_product_category ADD CONSTRAINT FK_D1DCFBC0BE6903FD FOREIGN KEY (product_category_id) REFERENCES product_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE portfolio_image_product_category');
    }
}
