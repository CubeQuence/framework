<?php

declare(strict_types=1);

namespace CQ\Response;

final class Respond
{
    /**
     * Html response
     */
    public function html(
        string $data,
        int $code = 200,
        array $headers = []
    ): Html {
        return new Html(
            data: $data,
            code: $code,
            headers: $headers
        );
    }

    /**
     * JSON response
     */
    public function json(
        $data,
        int $code = 200,
        array $headers = []
    ): Json {
        return new Json(
            data: $data,
            code: $code,
            headers: $headers
        );
    }

    /**
     * JSON response
     */
    public function prettyJson(
        string $message,
        $data = [],
        int $code = 200,
        array $headers = []
    ): Json {
        return $this->json([
            'success' => $code === 200,
            'message' => $message,
            'data' => $data,
        ], $code, $headers);
    }

    /**
     * NoContent response
     */
    public function noContent(
        int $code = 204,
        array $headers = []
    ): NoContent {
        return new NoContent(
            code: $code,
            headers: $headers
        );
    }

    /**
     * Redirect response
     */
    public function redirect(
        string $url,
        int $code = 302,
        array $headers = []
    ): Redirect {
        return new Redirect(
            url: $url,
            code: $code,
            headers: $headers
        );
    }

    /**
     * Twig response
     */
    public function twig(
        string $view,
        array $parameters = [],
        int $code = 200,
        array $headers = []
    ): Html {
        $twig = new Twig();

        return new Html(
            data: $twig->render(view: $view, parameters: $parameters),
            code: $code,
            headers: $headers
        );
    }

    /**
     * XML response
     */
    public function xml(
        $data,
        int $code = 200,
        array $headers = []
    ): Xml {
        return new Xml(
            data: $data,
            code: $code,
            headers: $headers
        );
    }
}
