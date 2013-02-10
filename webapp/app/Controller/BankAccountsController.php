<?php
App::uses('AppController', 'Controller');

class BankAccountsController extends AppController {

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
			$this->request->data['BankAccount']['current_balance'] = $this->request->data['BankAccount']['opening_balance'];
			$this->request->data['BankAccount']['unallocated_balance'] = $this->request->data['BankAccount']['opening_balance'];
			$this->loadModel('BankAccount');
			$this->BankAccount->create();
			if ($this->BankAccount->save($this->request->data))
			{
				$this->Session->setFlash("Bank Account {$this->request->data['BankAccount']['name']} added!",'flash_success');
				$this->redirect(array('controller' => 'Users', 'action' => 'account'));
			}
			else
			{
				$this->Session->setFlash("There was an error trying to save Bank Account info!",'flash_error');
			}
		}
		$accountList = $this->getAccountList();
		$this->set(compact('accountList'));
	}
}
