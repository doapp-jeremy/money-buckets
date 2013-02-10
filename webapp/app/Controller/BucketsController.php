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
}
