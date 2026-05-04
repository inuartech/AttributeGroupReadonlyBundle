<?php

declare(strict_types=1);

namespace Inuar\Bundle\AttributeGroupReadOnlyBundle\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;

class GetReadOnlyAttributeGroupCodesQuery
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function getStatus(string $attributeGroupCode): array
    {
        $row = $this->connection->fetchAssociative(
            'SELECT frontend_readonly, api_editable FROM inuar_readonly_attribute_group WHERE attribute_group_code = :code',
            ['code' => $attributeGroupCode]
        );

        if (!$row) {
            return ['frontend_readonly' => false, 'api_editable' => true];
        }

        return [
            'frontend_readonly' => (bool) $row['frontend_readonly'],
            'api_editable'      => (bool) $row['api_editable'],
        ];
    }

    /** @return string[] - group codes where frontend_readonly = 1 */
    public function getFrontendReadOnlyGroupCodes(): array
    {
        return $this->connection->fetchFirstColumn(
            'SELECT attribute_group_code FROM inuar_readonly_attribute_group WHERE frontend_readonly = 1'
        );
    }

    /** @return string[] - attribute codes belonging to groups where api_editable = 0 */
    public function getApiProtectedAttributeCodes(): array
    {
        return $this->connection->fetchFirstColumn(
            <<<'SQL'
            SELECT a.code
            FROM pim_catalog_attribute a
            INNER JOIN pim_catalog_attribute_group ag ON ag.id = a.group_id
            INNER JOIN inuar_readonly_attribute_group r ON r.attribute_group_code = ag.code
            WHERE r.api_editable = 0
            SQL
        );
    }
}
