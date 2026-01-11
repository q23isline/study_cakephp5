<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Application;
use Cake\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LogRequestMiddleware implements MiddlewareInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->setClientId($request);
        $this->setUserId($request);

        return $handler->handle($request);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return void
     */
    private function setClientId(ServerRequestInterface $request): void
    {
        if ($request instanceof ServerRequest) {
            Application::setCurrentClientId($request->clientIp());
        } else {
            Application::setCurrentClientId(null);
        }
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return void
     */
    private function setUserId(ServerRequestInterface $request): void
    {
        /** @var \Authentication\Identity $identity */
        $identity = $request->getAttribute('identity');
        if ($identity) {
            $id = $identity->getIdentifier();
            $userId = null;
            if ($id !== null && !is_array($id)) {
                $userId = (string)$id;
            }
            Application::setCurrentUserId($userId);
        } else {
            Application::setCurrentUserId(null);
        }
    }
}
