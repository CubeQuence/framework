<?php

namespace CQ\Response;

class Respond
{
    /**
     * Html response
     *
     * @param string $content
     * @param int $code optional
     * @param array $headers optional
     *
     * @return Html
     */
    public function html(string $data, int $code = 200, array $headers = []): Html
    {
        return new Html(
            data: $data,
            code: $code,
            headers: $headers
        );
    }

    /**
     * JSON response
     *
     * @param mixed $data
     * @param int $code optional
     * @param array $headers optional
     *
     * @return Json
     */
    public function json($data, int $code = 200, array $headers = []) : Json
    {
        return new Json(
            data: $data,
            code: $code,
            headers: $headers
        );
    }

    /**
     * JSON response
     *
     * @param string $message
     * @param mixed $data optional
     * @param int $code optional
     * @param array $headers optional
     *
     * @return Json
     */
    public function prettyJson(string $message, $data = [], int $code = 200, array $headers = []) : Json
    {
        return $this->json([
            'success' => 200 === $code,
            'message' => $message,
            'data' => $data,
        ], $code, $headers);
    }

    /**
     * NoContent response
     *
     * @param int $code optional
     * @param array $headers optional
     *
     * @return NoContent
     */
    public function noContent(int $code = 204, array $headers = []) : NoContent
    {
        return new NoContent(
            code: $code,
            headers: $headers
        );
    }

    /**
     * Redirect response
     *
     * @param string $url
     * @param int $code optional
     * @param array $headers optional
     *
     * @return Redirect
     */
    public function redirect(string $url, int $code = 302, array $headers = []) : Redirect
    {
        return new Redirect(
            url: $url,
            code: $code,
            headers: $headers
        );
    }

    /**
     * Twig response
     *
     * @param string $view
     * @param array $parameters
     * @param int $code optional
     * @param array $headers optional
     *
     * @return Html
     */
    public function twig(string $view, array $parameters = [], int $code = 200, array $headers = []): Html
    {
        $twig = new Twig();

        return new Html(
            data: $twig->render(view: $view, parameters: $parameters),
            code: $code,
            headers: $headers
        );
    }

    /**
     * XML response
     *
     * @param string $content
     * @param int $code optional
     * @param array $headers optional
     *
     * @return Xml
     */
    public function xml($data, $code = 200, $headers = []): Xml
    {
        return new Xml(
            data: $data,
            code: $code,
            headers: $headers
        );
    }
}
