<?php


namespace Middleware;

use Slim\Http\Request;
use Slim\Http\Response;

class JsonResponseMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        /** @var $response Response */
        $response = $next($request, $response);

        /** check if body has valid json. */
        $body = json_decode($response->getBody(), true);

        if (! is_null($body)) {
            $response = $response->withJson(
                [
                    'status' => $response->getStatusCode(),
                    'data' => $body,
                ]
            );
        } else {
            $status = $response->getStatusCode();
            $status = $status == 404 ? $status : 204;
            $response = $response->withStatus($status);
        }

        $response = $response->withAddedHeader('Content-Type','application/json');
        return $response;
    }
}
