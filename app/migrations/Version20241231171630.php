<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241231171630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add file to the Social entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE social ADD file_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE social ADD CONSTRAINT FK_7161E18793CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7161E18793CB796C ON social (file_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE social DROP CONSTRAINT FK_7161E18793CB796C');
        $this->addSql('DROP INDEX UNIQ_7161E18793CB796C');
        $this->addSql('ALTER TABLE social DROP file_id');
    }
}
