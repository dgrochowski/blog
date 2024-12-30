<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241230092807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add author and published date to the post';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD published_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN post.published_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DF675F31B ON post (author_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8DF675F31B');
        $this->addSql('DROP INDEX IDX_5A8A6C8DF675F31B');
        $this->addSql('ALTER TABLE post DROP author_id');
        $this->addSql('ALTER TABLE post DROP published_at');
    }
}
