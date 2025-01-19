<?php
declare(strict_types=1);

namespace App\ApplicationService\SampleUsers;

use App\Infrastructure\SampleUsers\SampleUserRepository;

class ListGetApplicationService
{
    /**
     * @var \App\Infrastructure\SampleUsers\SampleUserRepository
     */
    private SampleUserRepository $sampleUserRepository;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->sampleUserRepository = new SampleUserRepository();
    }

    /**
     * ユースケースを表現する
     *
     * @param array{filterName: null|string, sort: null|string, pageNumber: int, pageSize: int} $command
     * @return array{count: int, users: array<string|int, array{id: int, name: string, birthDay: \DateTime, height: string, gender: string}>}
     */
    public function handle(array $command): array
    {
        $count = $this->sampleUserRepository->countUsers($command);
        $users = $this->sampleUserRepository->findUsers($command);

        $result = [
            'count' => $count,
            'users' => $users,
        ];

        return $result;
    }
}
