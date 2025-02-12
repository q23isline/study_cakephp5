<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
     * @return void
     * @link https://book.cakephp.org/5/en/tutorials-and-examples/cms/authentication.html
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        // Configure the login action to not require authentication, preventing
        // the infinite redirect loop issue
        $this->Authentication->addUnauthenticatedActions(['login']);
    }

    /**
     * Login method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     */
    public function login()
    {
        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();
        if ($result?->isValid()) {
            $this->Flash->success(__('Login successful'));
            $redirect = $this->Authentication->getLoginRedirect();
            if ($redirect !== null && $redirect !== '') {
                return $this->redirect($redirect);
            }
        }

        // Display error if user submitted and authentication failed
        if ($this->request->is('post') && !$result?->isValid()) {
            $this->Flash->error(__('Invalid username or password'));
        }
    }

    /**
     * ログアウトする
     *
     * @return \Cake\Http\Response|null|void
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     */
    public function logout()
    {
        $result = $this->Authentication->getResult();
        // regardless of POST or GET, redirect if user is logged in
        if ($result && $result->isValid()) {
            $this->Authentication->logout();

            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
    }
}
