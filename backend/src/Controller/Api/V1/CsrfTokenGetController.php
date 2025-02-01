<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\AppController;

class CsrfTokenGetController extends AppController
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
        $csrfToken = $this->request->getAttribute('csrfToken');
        $result = [
            'csrfToken' => $csrfToken,
        ];

        $this->set($result);
    }
}
