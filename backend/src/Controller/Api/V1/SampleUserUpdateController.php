<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\ApplicationService\SampleUsers\UpdateApplicationService;
use App\ApplicationService\Shared\DateValidator;
use App\Controller\AppController;
use App\Domain\Shared\Exception\ExceptionItem;
use App\Domain\Shared\Exception\NotFoundException;
use App\Domain\Shared\Exception\ValidateException;
use DateTime;

class SampleUserUpdateController extends AppController
{
    /**
     * initialize
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->viewBuilder()->setClassName('Json')->setOption('serialize', true);
    }

    /**
     * @return void
     */
    public function invoke(): void
    {
        $params = [
            'pathId' => $this->request->getParam('id'),
            'type' => $this->request->getData('data.type'),
            'id' => $this->request->getData('data.id'),
            'name' => $this->request->getData('data.attributes.name'),
            'birth_day' => $this->request->getData('data.attributes.birth_day'),
            'height' => $this->request->getData('data.attributes.height'),
            'gender' => $this->request->getData('data.attributes.gender'),
        ];

        try {
            $this->validate($params);
        } catch (ValidateException $e) {
            $this->set($e->format());
            $this->response = $this->response->withStatus(400);

            return;
        }

        try {
            $this->isConflict($params);
        } catch (ValidateException $e) {
            $this->set($e->format());
            $this->response = $this->response->withStatus(409);

            return;
        }

        $command = [
            'type' => (string)$params['type'],
            'id' => (int)$params['id'],
            'name' => (string)$params['name'],
            'birthDay' => new DateTime($params['birth_day']),
            'height' => (string)$params['height'],
            'gender' => (string)$params['gender'],
        ];

        $service = new UpdateApplicationService();

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
     * パラメータのバリデーションする
     *
     * @param array{pathId: mixed, type: mixed, id: mixed, name: mixed, birth_day: mixed, height: mixed, gender: mixed} $params
     * @return void
     * @throws \App\Domain\Shared\Exception\ValidateException
     */
    private function validate(array $params): void
    {
        $errors = [];
        if ($params['type'] !== 'users') {
            $errors[] = new ExceptionItem('/data/type', 'Invalid Type', '不正な値です。');
        }

        if (!is_numeric($params['id'])) {
            $errors[] = new ExceptionItem('/data/id', 'Invalid Id', '不正な値です。');
        }

        $maxLengthName = 100;
        if (is_array($params['name']) || empty($params['name']) || mb_strlen($params['name']) > $maxLengthName) {
            $errors[] = new ExceptionItem('/data/attributes/name', 'Invalid Attribute', '不正な値です。');
        }

        $birthDayFormat = 'Y/m/d';
        if (
            empty($params['birth_day'])
            || is_array($params['birth_day'])
            || !DateValidator::isCorrectDate((string)$params['birth_day'], $birthDayFormat)
        ) {
            $errors[] = new ExceptionItem('/data/attributes/birth_day', 'Invalid Attribute', '不正な値です。');
        }

        if (!is_numeric($params['height'])) {
            $errors[] = new ExceptionItem('/data/attributes/height', 'Invalid Attribute', '不正な値です。');
        }

        $genderKeys = [
            '1', // 男性
            '2', // 女性
        ];
        if (is_array($params['gender']) || !in_array($params['gender'], $genderKeys, true)) {
            $errors[] = new ExceptionItem('/data/attributes/gender', 'Invalid Attribute', '不正な値です。');
        }

        if (!empty($errors)) {
            throw new ValidateException($errors);
        }
    }

    /**
     * パスパラメータとリクエストボディの競合チェック
     *
     * @param array{pathId: mixed, type: mixed, id: mixed, name: mixed, birth_day: mixed, height: mixed, gender: mixed} $params
     * @return void
     * @throws \App\Domain\Shared\Exception\ValidateException
     */
    private function isConflict(array $params): void
    {
        $errors = [];
        if ((int)$params['pathId'] !== (int)$params['id']) {
            $errors[] = new ExceptionItem('/data/id', 'Invalid Id', '不正な値です。');
        }

        if (!empty($errors)) {
            throw new ValidateException($errors);
        }
    }

    /**
     * JSON レスポンス用にフォーマットする
     *
     * @param array{id: int, name: string, birthDay: \DateTime, height: string, gender: string} $data
     * @return array{data: array{type: string, id: int, attributes: array{name: string, birth_day: string, height: float, gender: string}}, links: array{self: string}}
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
            'links' => [
                'self' => '/api/v1/sample-users/' . $data['id'],
            ],
        ];

        return $result;
    }
}
