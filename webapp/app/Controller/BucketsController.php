<?php
App::uses('AppController', 'Controller');

class BucketsController extends AppController {

	public $uses = array();
	
	public function get_transactions($bucketId)
	{
	  $this->autoRender = false;	//Don't even hit a view/layout
	  header('HTTP/1.0 200 OK');
	  header('Content-type: application/json');

	  $accountIds = $this->getAccountIds();
	  $debug = array('accounts' => $accountIds,'bucket' => $bucketId);
	  $this->loadModel('Bucket');
	  $buckets = $this->Bucket->getBucketsForAccounts($accountIds,array('Bucket.id'));
	  $bucketIds = Set::extract('/Bucket/id',$buckets);
	  if (!in_array($bucketId,$bucketIds))
	  {
	    header('HTTP/1.0 400 OK');
	    echo json_encode(array('status' => 'error','message' => 'Not authorized to access bucket'));
	    exit();
	  }
	  $bucket = $this->Bucket->getTransactionEntries($bucketId);
	  echo json_encode(compact('debug','bucket'));
	  exit();
	}
	
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
