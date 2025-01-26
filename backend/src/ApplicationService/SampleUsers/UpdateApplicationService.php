<?php
declare(strict_types=1);

namespace App\ApplicationService\SampleUsers;

use App\Domain\Shared\Exception\ExceptionItem;
use App\Domain\Shared\Exception\NotFoundException;
use App\Infrastructure\SampleUsers\SampleUserRepository;

class UpdateApplicationService
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
     * @param array{type: string, id: int, name: string, birthDay: \DateTime, height: string, gender: string} $command
     * @return array{id: int, name: string, birthDay: \DateTime, height: string, gender: string}
     * @throws \App\Domain\Shared\Exception\NotFoundException
     */
    public function handle(array $command): array
    {
        if (!$this->sampleUserRepository->isExistUser($command['id'])) {
            throw new NotFoundException([new ExceptionItem('', '', 'サンプルユーザーが存在しません。')]);
        }

        $this->sampleUserRepository->updateUser($command);
        $result = $this->sampleUserRepository->findUser($command['id']);

        return $result;
    }
}
