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
        private readonly GetReadOnlyAttributeGroupCodesQuery $getQuery,
        private readonly SaveReadOnlyAttributeGroupStatus $saveStatus,
    ) {
    }

    public function getAction(string $code): JsonResponse
    {
        return new JsonResponse($this->getQuery->getStatus($code));
    }

    public function listAction(): JsonResponse
    {
        return new JsonResponse($this->getQuery->getFrontendReadOnlyGroupCodes());
    }

    public function saveAction(Request $request): Response
    {
        $code = $request->request->get('attribute_group_code');

        if (empty($code)) {
            return new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $frontendReadOnly = $request->request->getBoolean('frontend_readonly');
        $apiEditable      = $request->request->getBoolean('api_editable', true);

        $this->saveStatus->save($code, $frontendReadOnly, $apiEditable);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
