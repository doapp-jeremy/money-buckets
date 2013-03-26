<?php
App::uses('AppController', 'Controller');

class AccountsController extends AppController {

	public $uses = array();
	
	function isAuthorized()
	{
	  return true;
	}
	
	public function home()
	{
	  $accountIds = $this->getAccountIds();
	  $this->loadModel('BankAccount');
		$bankAccounts = $this->BankAccount->getBankAccountsForAccounts($accountIds);
		if (empty($bankAccounts))
		{
		  $this->Session->setFlash("Please add a bank account!",'flash_success');
		  $this->redirect('/bank_accounts/add');
		}
		$bankAccountList = Set::combine($bankAccounts, '{n}.BankAccount.id', '{n}.BankAccount.name');
	  //$bankAccountList = $this->BankAccount->getBankAccountListForAccounts($accountId);

		$this->loadModel('Bucket');
		$buckets = $this->Bucket->getBucketsForAccounts($accountIds, array('Bucket.id','Bucket.name','Bucket.available_balance'));
		$bucketIds = Set::extract('/Bucket/id',$buckets);
		
		$this->loadModel('TransactionType');
		$transactionTypes = $this->TransactionType->find('list', array('order' => array('id' => 'ASC')));
		$this->set(compact('user','userList','bankAccountList','buckets','transactionTypes'));
		
		$user = $this->Auth->user();
		$userList = array($user['id'] => 'You');
		
		$allocated = 0;
		foreach ($buckets as $bucket)
		{
			$allocated += $bucket['Bucket']['available_balance'];
		}
		$bankAccountAmount = 0;
		foreach ($bankAccounts as $bankAccount)
		{
			$bankAccountAmount += $bankAccount['BankAccount']['current_balance'];
		}
		$unallocatedAmount = $bankAccountAmount - $allocated;
		if ($unallocatedAmount >= 0.01)
		{
			$this->Session->setFlash("Unallocated amount: {$unallocatedAmount}",'flash_error');
		}
		
		$this->set(compact('bankAccountList','buckets','bucketIds','user','userList','transactionTypes','unallocatedAmount'));
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
	    $this->loadModel('Account');
	    debug($this->request->data);
	    // see if user with this facebook id exists, it not create it
	    $user = $this->Account->User->find('first',array('fields' => 'id','conditions' => array('User.facebook_id' => $this->request->data['User']['facebook_id'])));
	    debug($user);
	    if (empty($user['User']['id']))
	    {
	      $this->Account->User->create();
	      $this->Account->User->createAccount = false;
	      if ($this->Account->User->save(array('User' => array('facebook_id' => $this->request->data['User']['facebook_id']))))
	      {
	        $this->request->data['User']['id'] = $this->Account->User->getLastInsertID();
	      }
	    }
	    else
	    {
	      $this->request->data['User']['id'] = $user['User']['id'];
	    }
	    $facebookId = $this->request->data['User']['facebook_id'];
	    unset($this->request->data['User']['facebook_id']);
	    debug($this->request->data['User']['id']);
	    debug($this->request->data);
	    if ($this->Account->save($this->request->data))
	    {
	      $this->Session->setFlash("Added {$friendList[$facebookId]} to your account",'flash_success');
	      $this->redirect(array('controller' => 'accounts','action'=>'home'));
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
