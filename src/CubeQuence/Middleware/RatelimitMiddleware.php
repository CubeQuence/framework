<?php

declare(strict_types=1);

namespace CQ\Middleware;

use Closure;
use CQ\DB\DB;
use CQ\Helpers\ConfigHelper;
use CQ\Ratelimit\Models\RateModel;
use CQ\Ratelimit\Ratelimit;
use CQ\Ratelimit\Storage\Providers\DatabaseProvider;
use CQ\Response\JsonResponse;
use CQ\Response\Respond;

final class RatelimitMiddleware extends Middleware
{
    /**
     * Ratelimit routes
     */
    public function handleChild(Closure $next): Closure | JsonResponse
    {
        $databaseProvider = new DatabaseProvider(
            db: DB::class
        );

        $rateLimiter = new Ratelimit(
            storageProvider: $databaseProvider
        );

        $identifier = $this->requestHelper->ip();
        $rate = $this->getRate(
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
                data: $headers,
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

    private function getRate(string $path): RateModel
    {
        $path = $this->route->getPath();
        $pathConfig = ConfigHelper::get(key: "ratelimit.{$path}");
        $defaultConfig = ConfigHelper::get(
            key: 'ratelimit.default',
            fallback: '60:60'
        );

        $config = $pathConfig ? $pathConfig : $defaultConfig;

        [$operations, $interval] = explode(
            ':',
            string: $config,
            limit: 2
        );

        return RateModel::custom(
            operations: (int) $operations,
            interval: (int) $interval
        );
    }
}
