<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250101215333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add cached tags to the post entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post ADD cached_tags TEXT');
        $this->addSql('COMMENT ON COLUMN post.cached_tags IS \'(DC2Type:simple_array)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post DROP cached_tags');
    }
}
