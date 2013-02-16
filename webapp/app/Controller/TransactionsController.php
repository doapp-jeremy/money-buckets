<?php
App::uses('AppController', 'Controller');

class TransactionsController extends AppController {

	public $uses = array();

	public function get_list() {
	  $this->autoRender = false;	//Don't even hit a view/layout
	  header('HTTP/1.0 200 OK');
	  header('Content-type: application/json');
	  
	  $accountIds = $this->getAccountIds();
	  $debug = array('accounts' => $accountIds); 
	  $this->loadModel('BankAccount');
	  $bank_accounts = $this->BankAccount->getBankAccountsForAccounts($accountIds);
	  echo json_encode(compact('debug','bank_accounts'));
	  exit();
	}
	
	public function edit($transactionId) {
		
		if (!empty($this->request->data))
		{
			//debug($this->request->data);exit();
			$this->loadModel('BankAccount');
			if ($this->BankAccount->editTransaction($this->request->data))
			{
				$this->Session->setFlash("Transaction added!",'flash_success');
				$this->redirect(array('controller' => 'Transactions', 'action' => 'add'));
				exit();
			}
			else
			{
				$this->Session->setFlash("There was an error trying to save Transaction info!",'flash_error');
			}
		}
		else
		{
			$contain = array(
				'TransactionEntry'	
			);
			$conditions = array('Transaction.id' => $transactionId);
			$this->request->data = $this->Transaction->find('first', compact('conditions','contain'));
		}
		$user = $this->Auth->user();
		$userList = array($user['id'] => 'You');
		
		$accountIds = $this->getAccountIds();
		
		$this->loadModel('BankAccount');
		$bankAccountList = $this->BankAccount->getBankAccountListForAccounts($accountIds);

		$this->loadModel('Bucket');
		$buckets = $this->Bucket->getBucketsForAccounts($accountIds);
		
		$this->loadModel('TransactionType');
		$transactionTypes = $this->TransactionType->find('list', array('order' => array('id' => 'ASC')));
		$this->set(compact('user','userList','bankAccountList','buckets','transactionTypes'));
	}
	
	public function add() {
		
		if (!empty($this->request->data))
		{
			debug($this->request->data);
			
			$this->loadModel('BankAccount');

			if ($this->BankAccount->addTransaction($this->request->data))
			{
				$this->Session->setFlash("Transaction added!",'flash_success');
				$this->redirect(array('controller' => 'Transactions', 'action' => 'add'));
				exit();
			}
			else
			{
				$this->Session->setFlash("There was an error trying to save Transaction info!",'flash_error');
			}
		}
		$user = $this->Auth->user();
		$userList = array($user['id'] => 'You');
		
		$accountIds = $this->getAccountIds();
		
		$this->loadModel('BankAccount');
		$bankAccountList = $this->BankAccount->getBankAccountListForAccounts($accountIds);

		$this->loadModel('Bucket');
		$buckets = $this->Bucket->getBucketsForAccounts($accountIds);
		
		$this->loadModel('TransactionType');
		$transactionTypes = $this->TransactionType->find('list', array('order' => array('id' => 'ASC')));
		$this->set(compact('user','userList','bankAccountList','buckets','transactionTypes'));
	}
	
	public function delete($transactionId)
	{
		// TODO: verify user has access to delete transaction
		$this->loadModel('Transaction');
		$this->Transaction->delete($transactionId);
		$this->redirect(array('controller' => 'Transactions', 'action' => 'add'));
	}
}
