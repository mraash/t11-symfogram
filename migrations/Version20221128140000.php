<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221128140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8D7E3C61F9');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post_image DROP CONSTRAINT FK_522688B04B89032C');
        $this->addSql('ALTER TABLE post_image ADD CONSTRAINT FK_522688B04B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE post_image DROP CONSTRAINT fk_522688b04b89032c');
        $this->addSql('ALTER TABLE post_image ADD CONSTRAINT fk_522688b04b89032c FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT fk_5a8a6c8d7e3c61f9');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT fk_5a8a6c8d7e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
