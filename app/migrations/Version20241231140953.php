<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241231140953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create setting table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE setting (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, slug VARCHAR(100) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F74B898989D9B62 ON setting (slug)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE setting');
    }
}
