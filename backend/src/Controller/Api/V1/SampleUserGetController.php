<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\ApplicationService\SampleUsers\GetApplicationService;
use App\Controller\Api\AppApiController;
use App\Domain\Shared\Exception\NotFoundException;

class SampleUserGetController extends AppApiController
{
    /**
     * @return void
     */
    public function invoke(): void
    {
        $command = [
            'id' => (int)$this->request->getParam('id'),
        ];

        $service = new GetApplicationService();

        try {
            $data = $service->handle($command);
        } catch (NotFoundException $e) {
            $this->set($e->format());
            $this->response = $this->response->withStatus(404);

            return;
        }

        $result = $this->format($data);

        $this->set($result);
    }

    /**
     * JSON レスポンス用にフォーマットする
     *
     * @param array{id: int, name: string, birthDay: \DateTime, height: string, gender: string} $data
     * @return array{data: array{type: string, id: int, attributes: array{name: string, birth_day: string, height: float, gender: string}}}
     */
    private function format(array $data): array
    {
        $result = [
            'data' => [
                'type' => 'users',
                'id' => $data['id'],
                'attributes' => [
                    'name' => $data['name'],
                    'birth_day' => $data['birthDay']->format('Y/m/d'),
                    'height' => (float)$data['height'],
                    'gender' => $data['gender'],
                ],
            ],
        ];

        return $result;
    }
}
