<?php

declare(strict_types=1);

namespace Inuar\Bundle\AttributeGroupReadOnlyBundle\Infrastructure\Controller;

use Inuar\Bundle\AttributeGroupReadOnlyBundle\Infrastructure\Persistence\GetReadOnlyAttributeGroupCodesQuery;
use Inuar\Bundle\AttributeGroupReadOnlyBundle\Infrastructure\Persistence\SaveReadOnlyAttributeGroupStatus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReadOnlyAttributeGroupController
{
    public function __construct(
        private readonly GetReadOnlyAttributeGroupCodesQuery $getReadOnlyCodes,
        private readonly SaveReadOnlyAttributeGroupStatus $saveStatus,
    ) {
    }

    public function getAction(string $code): JsonResponse
    {
        return new JsonResponse([
            'is_read_only' => $this->getReadOnlyCodes->isReadOnly($code),
        ]);
    }

    public function listAction(): JsonResponse
    {
        return new JsonResponse($this->getReadOnlyCodes->execute());
    }

    public function saveAction(Request $request): Response
    {
        $code = $request->request->get('attribute_group_code');
        $isReadOnly = $request->request->getBoolean('is_read_only');

        if (empty($code)) {
            return new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->saveStatus->save($code, $isReadOnly);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
