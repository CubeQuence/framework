<?php

declare(strict_types=1);

namespace CQ\Response;

final class Respond
{
    /**
     * Html response
     */
    public static function html(
        string $data,
        int $code = 200,
        array $headers = []
    ): HtmlResponse {
        return new HtmlResponse(
            data: $data,
            code: $code,
            headers: $headers
        );
    }

    /**
     * JSON response
     */
    public static function json(
        $data,
        int $code = 200,
        array $headers = []
    ): JsonResponse {
        return new JsonResponse(
            data: $data,
            code: $code,
            headers: $headers
        );
    }

    /**
     * JSON response
     */
    public static function prettyJson(
        string $message,
        $data = [],
        int $code = 200,
        array $headers = []
    ): JsonResponse {
        return self::json([
            'success' => $code === 200,
            'message' => $message,
            'data' => $data,
        ], $code, $headers);
    }

    /**
     * NoContent response
     */
    public static function noContent(
        int $code = 204,
        array $headers = []
    ): NoContentResponse {
        return new NoContentResponse(
            code: $code,
            headers: $headers
        );
    }

    /**
     * Redirect response
     */
    public static function redirect(
        string $url,
        int $code = 302,
        array $headers = []
    ): RedirectResponse {
        return new RedirectResponse(
            url: $url,
            code: $code,
            headers: $headers
        );
    }

    /**
     * Twig response
     */
    public static function twig(
        string $view,
        array $parameters = [],
        int $code = 200,
        array $headers = []
    ): HtmlResponse {
        $twig = new Twig();

        return new HtmlResponse(
            data: $twig->render(view: $view, parameters: $parameters),
            code: $code,
            headers: $headers
        );
    }

    /**
     * XML response
     */
    public static function xml(
        $data,
        int $code = 200,
        array $headers = []
    ): XmlResponse {
        return new XmlResponse(
            data: $data,
            code: $code,
            headers: $headers
        );
    }
}
