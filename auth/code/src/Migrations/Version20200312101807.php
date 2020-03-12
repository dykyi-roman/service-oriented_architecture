<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20200312101807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        if ($schema->hasTable('users')) {
            return;
        }

        $users = $schema->createTable('users');
        $users->addColumn('id', Types::INTEGER)->setAutoincrement(true);
        $users->addColumn('username', Types::STRING, ['length' => 25]);
        $users->addColumn('password', Types::STRING, ['length' => 500]);
        $users->addColumn('email', Types::STRING, ['length' => 80]);
        $users->addColumn('is_active', Types::BOOLEAN);

        $users->setPrimaryKey(['id']);
        $users->addUniqueIndex(['email']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('users');
    }
}
