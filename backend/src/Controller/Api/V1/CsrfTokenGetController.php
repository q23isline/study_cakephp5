<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppApiController;
use Cake\Event\EventInterface;

class CsrfTokenGetController extends AppApiController
{
    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
     * @return void
     * @link https://book.cakephp.org/5/en/tutorials-and-examples/cms/authentication.html
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->Authentication->addUnauthenticatedActions(['invoke']);
    }

    /**
     * @return void
     */
    public function invoke(): void
    {
        $csrfToken = $this->request->getAttribute('csrfToken');
        $result = [
            'csrfToken' => $csrfToken,
        ];

        $this->set($result);
    }
}
