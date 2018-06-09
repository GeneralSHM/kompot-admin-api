<?php

namespace Middleware;

use Middleware\BaseMiddleware;
use Slim\Http\Request;
use Slim\Http\Response;

class ListMiddleware extends BaseMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        $_maxLimitOnSearch = 100;

        $offset = (int) $request->getParam('offset', null);
        $limit = (int) $request->getParam('limit', null);

        if ($limit <= 0 || $offset < 0) {
            throw new \InvalidArgumentException('Limit and offset are incorrect.', 422);
        }

        if ($limit > $_maxLimitOnSearch) {
            throw new \InvalidArgumentException('Maximum limit is 100.', 422);
        }

        return $next($request, $response);
    }
}
