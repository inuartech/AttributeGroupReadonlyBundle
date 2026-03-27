<?php

declare(strict_types=1);

namespace Inuar\Bundle\AttributeGroupReadOnlyBundle\Updater;

use Inuar\Bundle\AttributeGroupReadOnlyBundle\Infrastructure\Persistence\GetReadOnlyAttributeGroupCodesQuery;
use Akeneo\Tool\Component\StorageUtils\Updater\ObjectUpdaterInterface;

class ReadOnlyAttributeGroupProductUpdaterDecorator implements ObjectUpdaterInterface
{
    /** @var string[]|null */
    private ?array $readOnlyAttributeCodes = null;

    public function __construct(
        private readonly ObjectUpdaterInterface $inner,
        private readonly GetReadOnlyAttributeGroupCodesQuery $query,
    ) {
    }

    public function update($object, array $data, array $options = []): static
    {
        if (isset($data['values'])) {
            $data['values'] = $this->filterReadOnlyValues($data['values']);
        }

        $this->inner->update($object, $data, $options);

        return $this;
    }

    private function filterReadOnlyValues(array $values): array
    {
        $readOnlyCodes = $this->getReadOnlyAttributeCodes();

        if (empty($readOnlyCodes)) {
            return $values;
        }

        return array_diff_key($values, array_flip($readOnlyCodes));
    }

    /** @return string[] */
    private function getReadOnlyAttributeCodes(): array
    {
        if ($this->readOnlyAttributeCodes === null) {
            $this->readOnlyAttributeCodes = $this->query->getReadOnlyAttributeCodes();
        }

        return $this->readOnlyAttributeCodes;
    }
}
