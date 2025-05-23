<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\ApplicationService\SampleUsers\DeleteApplicationService;
use App\Controller\Api\AppApiController;
use App\Domain\Shared\Exception\NotFoundException;

class SampleUserDeleteController extends AppApiController
{
    /**
     * @return void
     */
    public function invoke(): void
    {
        $command = [
            'id' => (int)$this->request->getParam('id'),
        ];

        $service = new DeleteApplicationService();

        try {
            $service->handle($command);
        } catch (NotFoundException $e) {
            $this->set($e->format());
            $this->response = $this->response->withStatus(404);

            return;
        }

        $this->response = $this->response->withStatus(204);
    }
}
