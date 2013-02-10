<?php
App::uses('AppController', 'Controller');

class BucketsController extends AppController {

	public $uses = array();
	
	public function index() {
	  $accountList = $this->getAccountList();
	  if (empty($accountList)) $this->redirect('/accounts/add');
	}
	
	public function get_list() {
	  $this->autoRender = false;	//Don't even hit a view/layout
	  header('HTTP/1.0 200 OK');
	  header('Content-type: application/json');
	  
	  $accountList = $this->getAccountList();
	  $debug = array('accounts' => $accountList); 
	  $buckets = array();
	  if (!empty($accountList))
	  {
  	  $this->loadModel('Bucket');
  	  $buckets = $this->Bucket->getBucketsForAccounts(array_keys($accountList));
	  }
	  echo json_encode(compact('debug','buckets'));
	  exit();
	}

	public function add() {
		
		if (!empty($this->request->data))
		{
			$this->request->data['Bucket']['available_balance'] = $this->request->data['Bucket']['opening_balance'];
			$this->request->data['Bucket']['actual_balance'] = $this->request->data['Bucket']['opening_balance'];
			$this->loadModel('Bucket');
			$this->Bucket->create();
			if ($this->Bucket->save($this->request->data))
			{
				$this->Session->setFlash("Bucket {$this->request->data['Bucket']['name']} added!",'flash_success');
				$this->redirect(array('controller' => 'Users', 'action' => 'account'));
			}
			else
			{
				$this->Session->setFlash("There was an error trying to save Bucket info!",'flash_error');
			}
		}
		$accountList = $this->getAccountList();
		$this->set(compact('accountList'));
	}
}
