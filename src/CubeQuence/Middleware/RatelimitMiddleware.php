<?php

declare(strict_types=1);

namespace CQ\Middleware;

use Closure;
use CQ\DB\DB;
use CQ\Response\JsonResponse;
use CQ\Response\Respond;
use CQ\Ratelimit\Models\RateModel;
use CQ\Ratelimit\Ratelimit;
use CQ\Ratelimit\Storage\Providers\DatabaseProvider;

final class RatelimitMiddleware extends Middleware
{
    /**
     * Ratelimit routes
     */
    public function handleChild(Closure $next): Closure | JsonResponse // TODO: doesn't work
    {
        $test = RateModel::perMinute(100); // TODO: remove this after it works

        $databaseProvider = new DatabaseProvider(
            db: DB::class
        );

        $rateLimiter = new Ratelimit(
            storageProvider: $databaseProvider
        );

        $identifier = $this->requestHelper->ip();
        $rate = $this->getConfig(
            path: $this->route->getPath()
        );

        $status = $rateLimiter->limit(
            identifier: $identifier,
            rate: $rate
        );

        $headers = [
            'X-RateLimit-Limit' => $status->getLimit(),
            'X-RateLimit-Remaining' => $status->getRemainingAttempts(),
            'X-RateLimit-Reset' => $status->getResetAt(),
        ];

        if ($status->limitExceeded()) {
            return Respond::prettyJson(
                message: 'Ratelimit Exceeded',
                code: 429,
                headers: $headers
            );
        }

        $response = $next($this->request);

        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        return $response;
    }

    private function getConfig(string $path) : RateModel
    {
        // TODO: Get from config
        // $path = $this->route->getPath();

        return RateModel::perMinute(100);
    }
}
