<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;

class AppApiController extends AppController
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->viewBuilder()->setClassName('Json')->setOption('serialize', true);
    }
}
