<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241231160302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create social table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE social (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, slug VARCHAR(100) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7161E187989D9B62 ON social (slug)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE social');
    }
}
