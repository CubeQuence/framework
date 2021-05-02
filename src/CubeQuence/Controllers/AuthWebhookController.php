<?php

declare(strict_types=1);

namespace CQ\Controllers;

use CQ\Controllers\Controller;
use CQ\Helpers\ConfigHelper;
use CQ\Response\JsonResponse;
use CQ\Response\Respond;

abstract class AuthWebhookController extends Controller
{
    /**
     * Delete user webhook auth
     */
    final public function delete(): JsonResponse
    {
        if (ConfigHelper::get('auth.client_secret') !== $this->requestHelper->getAuthorization()) {
            return Respond::prettyJson(
                message: 'Invalid authorization header',
                code: 403
            );
        }

        try {
            $type = $this->request->data->event->type;
            $userId = $this->request->data->event->user->id;
        } catch (\Throwable) {
            return Respond::prettyJson(
                message: 'Provided data was malformed',
                code: 400
            );
        }

        if (!in_array(
            needle: $type,
            haystack: [
                'user.delete',
                'user.registration.delete',
            ]
        )) {
            return Respond::prettyJson(
                message: 'Invalid webhook type',
                code: 400
            );
        }

        // Delete user webhook app specific
        $this->deleteSteps(
            userId: $userId
        );

        return Respond::prettyJson(
            message: 'Webhook Received'
        );
    }

    /**
     * Delete user webhook app specific steps
     */
    abstract protected function deleteSteps(string $userId): void;
}
