<?php

declare(strict_types=1);

namespace Inuar\Bundle\AttributeGroupReadOnlyBundle\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;

class GetReadOnlyAttributeGroupCodesQuery
{
    public function __construct(private readonly Connection $connection)
    {
    }

    /** @return string[] */
    public function execute(): array
    {
        return $this->connection->fetchFirstColumn(
            'SELECT attribute_group_code FROM inuar_readonly_attribute_group'
        );
    }

    public function isReadOnly(string $attributeGroupCode): bool
    {
        return (bool) $this->connection->fetchOne(
            'SELECT 1 FROM inuar_readonly_attribute_group WHERE attribute_group_code = :code',
            ['code' => $attributeGroupCode]
        );
    }

    /** @return string[] */
    public function getReadOnlyAttributeCodes(): array
    {
        return $this->connection->fetchFirstColumn(
            <<<'SQL'
            SELECT a.code
            FROM pim_catalog_attribute a
            INNER JOIN pim_catalog_attribute_group ag ON ag.id = a.group_id
            INNER JOIN inuar_readonly_attribute_group r ON r.attribute_group_code = ag.code
            SQL
        );
    }
}
