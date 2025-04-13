<?php
declare(strict_types=1);

namespace App\Middleware;

use Cake\Core\Configure;
use Cake\Http\Client\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     * @see https://book.cakephp.org/5/en/controllers/middleware.html#creating-middleware
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Debug モードの場合のみ CORS ヘッダーを追加（ローカル環境用）
        // プリフライトでルーティングがないことによる 404エラーを防ぐために handle メソッド前にやる
        if (Configure::read('debug') && $request->getMethod() === 'OPTIONS') {
            $response = new Response();

            return $response
                ->withStatus(200)
                ->withHeader('Access-Control-Allow-Origin', 'http://localhost:5173')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->withHeader(
                    'Access-Control-Allow-Headers',
                    'Content-Type, Authorization, X-Requested-With, X-CSRF-Token'
                )
                ->withHeader('Access-Control-Allow-Credentials', 'true');
        }

        // Calling $handler->handle() delegates control to the *next* middleware
        // In your application's queue.
        $response = $handler->handle($request);

        // Debug モードの場合のみ CORS ヘッダーを追加（ローカル環境用）
        if (Configure::read('debug')) {
            return $response
                ->withHeader('Access-Control-Allow-Origin', 'http://localhost:5173')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->withHeader(
                    'Access-Control-Allow-Headers',
                    'Content-Type, Authorization, X-Requested-With, X-CSRF-Token'
                )
                ->withHeader('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}
