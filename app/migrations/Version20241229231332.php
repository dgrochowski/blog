<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241229231332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add directory and slug to file entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE file ADD directory VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE file ADD slug VARCHAR(100) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8C9F3610989D9B62 ON file (slug)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_8C9F3610989D9B62');
        $this->addSql('ALTER TABLE file DROP directory');
        $this->addSql('ALTER TABLE file DROP slug');
    }
}
