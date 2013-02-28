<?php
App::uses('AppController', 'Controller');

class TransactionsController extends AppController {

	public $uses = array();

	public function get_list() {
	  $this->autoRender = false;	//Don't even hit a view/layout
	  header('HTTP/1.0 200 OK');
	  header('Content-type: application/json');
	  
	  $accountIds = $this->getAccountIds();
	  $this->loadModel('BankAccount');
		$bankAccounts = $this->BankAccount->getBankAccountsForAccounts($accountIds);
		$bankAccountIds = Set::extract('/BankAccount/id',$bankAccounts);
		
		$fields = array('Transaction.id','Transaction.bank_account_id','Transaction.transaction_type_id','Transaction.date','Transaction.label','Transaction.amount','Transaction.bank_account_after','Transaction.unallocated_amount');
		$contain = array(
		    'BankAccount' => array('fields' => array('BankAccount.name'))
		);
		$transactions = $this->BankAccount->Transaction->getTransactionsForBankAccount($bankAccountIds,$fields,$contain);
		
		$debug = array('bank_account_ids' => $bankAccountIds);
		echo json_encode(compact('debug','transactions'));
	  exit();
	}
	
	public function edit($transactionId) {
	  $accountIds = $this->getAccountIds();
	  		
		if (!empty($this->request->data))
		{
			//debug($this->request->data);exit();
			$this->loadModel('BankAccount');
			if ($this->BankAccount->editTransaction($this->request->data))
			{
				$this->Session->setFlash("Transaction {$this->request->data['Transaction']['label']} for {$this->request->data['Transaction']['amount']} on {$this->request->data['Transaction']['date']['month']}/{$this->request->data['Transaction']['date']['day']} saved!",'flash_success');
				$this->redirect(array('controller' => 'accounts', 'action' => 'home'));
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
		
		$this->loadModel('BankAccount');
		$bankAccountList = $this->BankAccount->getBankAccountListForAccounts($accountIds);

		$this->loadModel('Bucket');
		$buckets = $this->Bucket->getBucketsForAccounts($accountIds);
		
		$this->loadModel('TransactionType');
		$transactionTypes = $this->TransactionType->find('list', array('order' => array('id' => 'ASC')));
		$this->set(compact('user','userList','bankAccountList','buckets','transactionTypes'));
	}
	
	private function clearCacheAfterTransactionSave($accountIds)
	{
	  $this->loadModel('Bucket');
	  $accountId = $this->getAccountId();
	  $bucketIds = array();
	  foreach ($this->request->data['TransactionEntry'] as $transactionEntry)
	  {
	    if (!empty($transactionEntry['amount']))
	    {
	      $bucketIds[] = $transactionEntry['bucket_id'];
	    }
	  }
	  $this->Bucket->clearBucketCache($accountIds,$bucketIds);
	}
	
	public function add() {
		Configure::write('debug',0);
		$accountIds = $this->getAccountIds();
		
		if (!empty($this->request->data))
		{
			debug($this->request->data);
			
			$this->loadModel('BankAccount');

			if ($this->BankAccount->addTransaction($this->request->data))
			{
				$this->Session->setFlash("Transaction {$this->request->data['Transaction']['label']} for {$this->request->data['Transaction']['amount']} on {$this->request->data['Transaction']['date']['month']}/{$this->request->data['Transaction']['date']['day']} added!",'flash_success');
				$this->redirect(array('controller' => 'accounts', 'action' => 'home'));
			}
			else
			{
				$this->Session->setFlash("There was an error trying to save Transaction info!",'flash_error');
			}
		}
		$user = $this->Auth->user();
		$userList = array($user['id'] => 'You');
		
		
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
		Configure::write('debug',2);
		// TODO: verify user has access to delete transaction
		$this->loadModel('Transaction');
		$this->Transaction->delete($transactionId);
		$this->redirect(array('controller' => 'accounts', 'action' => 'home'));
	}
}
