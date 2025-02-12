<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\ApplicationService\SampleUsers\ListGetApplicationService;
use App\Controller\AppController;
use App\Domain\Shared\Exception\ExceptionItem;
use App\Domain\Shared\Exception\ValidateException;

class SampleUserListGetController extends AppController
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
            'filterName' => $this->request->getQuery('filter.name'),
            'sort' => $this->request->getQuery('sort'),
            'pageNumber' => $this->request->getQuery('page.number') ?? 1,
            'pageSize' => $this->request->getQuery('page.size') ?? 10,
        ];

        try {
            $this->validate($params);
        } catch (ValidateException $e) {
            $this->set($e->format());
            $this->response = $this->response->withStatus(400);

            return;
        }

        $command = [
            'filterName' => empty($params['filterName']) ? null : (string)$params['filterName'],
            'sort' => empty($params['sort']) ? null : (string)$params['sort'],
            'pageNumber' => (int)$params['pageNumber'],
            'pageSize' => (int)$params['pageSize'],
        ];

        $service = new ListGetApplicationService();
        $data = $service->handle($command);
        $result = $this->format($command, $data);

        $this->set($result);
    }

    /**
     * パラメータのバリデーションする
     *
     * @param array{filterName: mixed, sort: mixed, pageNumber: mixed, pageSize: mixed} $params
     * @return void
     * @throws \App\Domain\Shared\Exception\ValidateException
     * @psalm-suppress PrivateFinalMethod なぜか private メソッドに final 修飾子をつけるなエラーが出るため無視する
     */
    private function validate(array $params): void
    {
        $errors = [];
        if (is_array($params['filterName'])) {
            $errors[] = new ExceptionItem('filterName', 'Invalid Query Parameter', '不正な値です。');
        }

        $sortKeys = [
            'name', // 昇順
            '-name', // 降順
            'birth_day',
            '-birth_day',
            'height',
            '-height',
            'gender',
            '-gender',
        ];
        if (
            is_array($params['sort'])
            || !empty($params['sort'])
            && !in_array($params['sort'], $sortKeys, true)
        ) {
            $errors[] = new ExceptionItem('sort', 'Invalid Query Parameter', '不正な値です。');
        }

        if (!is_numeric($params['pageNumber'])) {
            $errors[] = new ExceptionItem('pageNumber', 'Invalid Query Parameter', '不正な値です。');
        }

        if (!is_numeric($params['pageSize']) || empty($params['pageSize'])) {
            $errors[] = new ExceptionItem('pageSize', 'Invalid Query Parameter', '不正な値です。');
        }

        if (!empty($errors)) {
            throw new ValidateException($errors);
        }
    }

    /**
     * JSON レスポンス用にフォーマットする
     *
     * @param array{filterName: null|string, sort: null|string, pageNumber: int, pageSize: int} $command
     * @param array{count: int, users: array<string|int, array{id: int, name: string, birthDay: \DateTime, height: string, gender: string}>} $collection
     * @return array{data: array{type: string, id: int, attributes: array{name: string, birth_day: string, height: float, gender: string}}[], meta: array{total: int, page: array{number: int, size: int, total_pages: float}}, links: array{self: string, next: string|null, prev: string|null}}
     */
    private function format(array $command, array $collection): array
    {
        $data = [];
        foreach ($collection['users'] as $entity) {
            $data[] = [
                'type' => 'users',
                'id' => $entity['id'],
                'attributes' => [
                    'name' => $entity['name'],
                    'birth_day' => $entity['birthDay']->format('Y/m/d'),
                    'height' => (float)$entity['height'],
                    'gender' => $entity['gender'],
                ],
            ];
        }

        $totalPage = ceil($collection['count'] / $command['pageSize']);

        $parsedQuery = [];
        if (isset($command['filterName'])) {
            $parsedQuery['filter']['name'] = $command['filterName'];
        }

        if (isset($command['sort'])) {
            $parsedQuery['sort'] = $command['sort'];
        }

        $parsedQuery['page']['number'] = $command['pageNumber'];
        $parsedQuery['page']['size'] = $command['pageSize'];
        $self = $this->buildLink($parsedQuery);

        $next = null;
        if ($command['pageNumber'] < $totalPage) {
            $parsedQuery['page']['number'] = $command['pageNumber'] + 1;
            $next = $this->buildLink($parsedQuery);
        }

        $prev = null;
        if ($command['pageNumber'] > 1) {
            $parsedQuery['page']['number'] = $command['pageNumber'] - 1;
            $prev = $this->buildLink($parsedQuery);
        }

        $result = [
            'data' => $data,
            'meta' => [
                'total' => $collection['count'],
                'page' => [
                    'number' => $command['pageNumber'],
                    'size' => $command['pageSize'],
                    'total_pages' => $totalPage,
                ],
            ],
            'links' => [
                'self' => $self,
                'next' => $next,
                'prev' => $prev,
            ],
        ];

        return $result;
    }

    /**
     * リンクを生成する
     *
     * @param array<mixed> $queryParams
     * @return string
     */
    private function buildLink(array $queryParams): string
    {
        $queryString = http_build_query($queryParams);

        return '/api/v1/sample-users?' . $queryString;
    }
}
