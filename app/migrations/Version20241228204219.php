<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241228204219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add relation between post and file';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post ADD file_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post DROP image_filename');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D93CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A8A6C8D93CB796C ON post (file_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8D93CB796C');
        $this->addSql('DROP INDEX UNIQ_5A8A6C8D93CB796C');
        $this->addSql('ALTER TABLE post ADD image_filename VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE post DROP file_id');
    }
}
