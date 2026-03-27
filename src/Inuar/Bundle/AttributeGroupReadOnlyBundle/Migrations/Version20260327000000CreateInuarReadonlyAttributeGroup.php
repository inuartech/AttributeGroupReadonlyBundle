<?php

declare(strict_types=1);

namespace Inuar\Bundle\AttributeGroupReadOnlyBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260327000000CreateInuarReadonlyAttributeGroup extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create inuar_readonly_attribute_group table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                CREATE TABLE IF NOT EXISTS inuar_readonly_attribute_group (
                    attribute_group_code VARCHAR(100) NOT NULL,
                    PRIMARY KEY (attribute_group_code)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS inuar_readonly_attribute_group');
    }
}
