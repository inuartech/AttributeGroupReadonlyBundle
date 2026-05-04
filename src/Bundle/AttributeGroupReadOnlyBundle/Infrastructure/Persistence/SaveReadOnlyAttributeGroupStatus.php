<?php

declare(strict_types=1);

namespace Inuar\Bundle\AttributeGroupReadOnlyBundle\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;

class SaveReadOnlyAttributeGroupStatus
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function save(string $attributeGroupCode, bool $frontendReadOnly, bool $apiEditable): void
    {
        if (!$frontendReadOnly && $apiEditable) {
            $this->remove($attributeGroupCode);
            return;
        }

        $this->connection->executeStatement(
            <<<'SQL'
            INSERT INTO inuar_readonly_attribute_group (attribute_group_code, frontend_readonly, api_editable)
            VALUES (:code, :frontendReadOnly, :apiEditable)
            ON DUPLICATE KEY UPDATE frontend_readonly = :frontendReadOnly, api_editable = :apiEditable
            SQL,
            [
                'code'            => $attributeGroupCode,
                'frontendReadOnly' => (int) $frontendReadOnly,
                'apiEditable'     => (int) $apiEditable,
            ]
        );
    }

    public function remove(string $attributeGroupCode): void
    {
        $this->connection->executeStatement(
            'DELETE FROM inuar_readonly_attribute_group WHERE attribute_group_code = :code',
            ['code' => $attributeGroupCode]
        );
    }
}
