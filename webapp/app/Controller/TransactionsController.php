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
	
	public function add() {
		
		if (!empty($this->request->data))
		{
			//debug($this->request->data);exit();
			$this->loadModel('Transaction');
			$this->Transaction->create();
			if ($this->Transaction->saveAll($this->request->data))
			{
				$this->request->data['Transaction']['id'] = $this->Transaction->getLastInsertID();
				$this->Transaction->updateAfterSave($this->request->data);
				$this->Session->setFlash("Transaction added!",'flash_success');
				$this->redirect(array('controller' => 'Buckets', 'action' => 'index'));
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
}
