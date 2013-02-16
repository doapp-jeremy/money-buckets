<?php
App::uses('AppController', 'Controller');

class AccountsController extends AppController {

	public $uses = array();
	
	function isAuthorized()
	{
	  return true;
	}

	public function index() {
	  $accountList = $this->getAccountList();
	  if (empty($accountList)) $this->redirect('/accounts/add');
	  $this->set(compact('accountList'));
	}

	public function add() {
	  if (!empty($this->request->data))
	  {
	    $this->loadModel('Account');
	    $this->Account->create();
	    if ($this->Account->saveAssociated($this->request->data))
	    {
	      $this->Session->delete(self::ACCOUNT_LIST);
	      $this->Session->setFlash("Account {$this->request->data['Account']['name']} added!",'flash_success');
	      $this->redirect('/accounts');
	    }
	    else
	    {
	      $this->Session->setFlash('Could not add acount. Please refresh page and try again.','flash_error');
	    }
	  }
	  
	  
	  $accountList = $this->getAccountList();
	  
	  $this->set(compact('accountList'));
	}
	
	public function add_user()
	{
	  $friendList = $this->getFriends();
	  if (!empty($this->request->data))
	  {
	    debug($this->request->data);
	    $this->loadModel('Account');
	    if ($this->Account->save($this->request->data))
	    {
	      $this->Session->setFlash("Added {$friendList[$this->request->data['Friend']['id']]} to your account",'flash_success');
	      $this->redirect(array('controller' => 'buckets','action'=>'index'));
	    }
	    else
	    {
	      $this->Session->setFlash('Could not add friend to your account. Please refresh page and try again.','flash_error');
	    }
	  }
	  
	  $accountList = $this->getAccountList();
	  if (empty($accountList)) $this->redirect('/accounts/add');
	  
	  if (empty($friendList))
	  {
	    $this->Session->setFlash('Could not find your friends, please try again.','flash_error');
	    $this->redirect(array('controller' => 'buckets','action' => 'index'));
	  }
	  
	  
	  
	  $this->set(compact('accountList','friendList'));
	}
}
