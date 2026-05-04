<?php

declare(strict_types=1);

namespace Inuar\Bundle\AttributeGroupReadOnlyBundle\Updater;

use Inuar\Bundle\AttributeGroupReadOnlyBundle\Infrastructure\Persistence\GetReadOnlyAttributeGroupCodesQuery;
use Akeneo\Tool\Component\StorageUtils\Updater\ObjectUpdaterInterface;

class ReadOnlyAttributeGroupProductUpdaterDecorator implements ObjectUpdaterInterface
{
    /** @var string[]|null */
    private ?array $apiProtectedAttributeCodes = null;

    public function __construct(
        private readonly ObjectUpdaterInterface $inner,
        private readonly GetReadOnlyAttributeGroupCodesQuery $query,
    ) {
    }

    public function update($object, array $data, array $options = []): static
    {
        if (isset($data['values'])) {
            $data['values'] = $this->filterProtectedValues($data['values']);
        }

        $this->inner->update($object, $data, $options);

        return $this;
    }

    private function filterProtectedValues(array $values): array
    {
        $protectedCodes = $this->getApiProtectedAttributeCodes();

        if (empty($protectedCodes)) {
            return $values;
        }

        return array_diff_key($values, array_flip($protectedCodes));
    }

    /** @return string[] */
    private function getApiProtectedAttributeCodes(): array
    {
        if ($this->apiProtectedAttributeCodes === null) {
            $this->apiProtectedAttributeCodes = $this->query->getApiProtectedAttributeCodes();
        }

        return $this->apiProtectedAttributeCodes;
    }
}
