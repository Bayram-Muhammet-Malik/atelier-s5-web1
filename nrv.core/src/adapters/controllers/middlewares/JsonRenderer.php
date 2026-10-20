<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\middlewares;

use Psr\Http\Message\ResponseInterface as Response;

class JsonRenderer {
    public static function render(Response $rs, int $code, mixed $data=null) : Response {
        $rs = $rs->withStatus($code)
                 ->withHeader('Content-Type', 'application/json;charset=utf-8');
        if (!is_null($data)) {
            $rs->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
        return $rs;
    }
}
