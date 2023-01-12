<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221127190052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE email_verification_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE post_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE post_image_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE post (id INT NOT NULL, owner_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D7E3C61F9 ON post (owner_id)');
        $this->addSql('CREATE TABLE post_image (id INT NOT NULL, post_id INT DEFAULT NULL, uri VARCHAR(500) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_522688B04B89032C ON post_image (post_id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post_image ADD CONSTRAINT FK_522688B04B89032C FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD avatar_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D64986383B10 FOREIGN KEY (avatar_id) REFERENCES post_image (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64986383B10 ON "user" (avatar_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D64986383B10');
        $this->addSql('DROP SEQUENCE email_verification_token_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE post_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE post_image_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8D7E3C61F9');
        $this->addSql('ALTER TABLE post_image DROP CONSTRAINT FK_522688B04B89032C');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_image');
        $this->addSql('DROP INDEX UNIQ_8D93D64986383B10');
        $this->addSql('ALTER TABLE "user" DROP avatar_id');
    }
}
