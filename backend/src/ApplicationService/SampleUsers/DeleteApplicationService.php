<?php
declare(strict_types=1);

namespace App\ApplicationService\SampleUsers;

use App\Domain\Shared\Exception\ExceptionItem;
use App\Domain\Shared\Exception\NotFoundException;
use App\Infrastructure\SampleUsers\SampleUserRepository;

class DeleteApplicationService
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
     * @param array{id: int} $command
     * @return void
     * @throws \App\Domain\Shared\Exception\NotFoundException
     */
    public function handle(array $command): void
    {
        if (!$this->sampleUserRepository->isExistUser($command['id'])) {
            throw new NotFoundException([new ExceptionItem('', '', 'サンプルユーザーが存在しません。')]);
        }

        $this->sampleUserRepository->deleteUser($command['id']);
    }
}
