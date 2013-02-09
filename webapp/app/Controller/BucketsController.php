<?php
App::uses('AppController', 'Controller');

class BucketsController extends AppController {

	public $uses = array();
	
	function isAuthorized()
	{
	  return true;
	}

	public function index() {
	  $accountList = $this->getAccountList();
	  if (empty($accountList)) $this->redirect('/accounts/add');
	}
}
