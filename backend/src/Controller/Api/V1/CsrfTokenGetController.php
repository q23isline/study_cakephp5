<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AppApiController;

class CsrfTokenGetController extends AppApiController
{
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
