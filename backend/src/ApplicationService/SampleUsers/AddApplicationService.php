<?php
declare(strict_types=1);

namespace App\ApplicationService\SampleUsers;

use App\Infrastructure\SampleUsers\SampleUserRepository;

class AddApplicationService
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
     * @param array{type: string, name: string, birthDay: \DateTime, height: string, gender: string} $command
     * @return array{id: int, name: string, birthDay: \DateTime, height: string, gender: string}
     */
    public function handle(array $command): array
    {
        $result = $this->sampleUserRepository->saveUser($command);

        return $result;
    }
}
