<?php

declare(strict_types=1);

namespace Inuar\Bundle\AttributeGroupReadOnlyBundle\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;

class SaveReadOnlyAttributeGroupStatus
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function save(string $attributeGroupCode, bool $isReadOnly): void
    {
        if ($isReadOnly) {
            $this->connection->executeStatement(
                'INSERT IGNORE INTO inuar_readonly_attribute_group (attribute_group_code) VALUES (:code)',
                ['code' => $attributeGroupCode]
            );
        } else {
            $this->connection->executeStatement(
                'DELETE FROM inuar_readonly_attribute_group WHERE attribute_group_code = :code',
                ['code' => $attributeGroupCode]
            );
        }
    }

    public function remove(string $attributeGroupCode): void
    {
        $this->connection->executeStatement(
            'DELETE FROM inuar_readonly_attribute_group WHERE attribute_group_code = :code',
            ['code' => $attributeGroupCode]
        );
    }
}
