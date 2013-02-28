<?php
App::uses('AppController', 'Controller');

class UsersController extends AppController {

	public $uses = array();

	//Add an email field to be saved along with creation.
	function beforeFacebookSave(){
	  //debug('beforeFacebookSave');
	  $email = trim($this->Connect->user('email'));
	  debug($email);
	  if (empty($email))
	  {
	  	debug("empty email");
	  	exit();
	  	return false;
	  }
	  $this->Connect->authUser['User']['email'] = $email;
	  return !empty($this->Connect->authUser['User']['email']);
	}
	
	function beforeFacebookLogin($user){
	  //Logic to happen before a facebook login
// 	  debug('beforeFacebookLogin');
// 	  debug($user);
// 	  exit();
	}
	
	function afterFacebookLogin(){
// 	  debug('afterFacebookLogin');
// 	  debug($this->Connect->user());
// 	  debug($this->Auth->user());
// 	  exit();
	  //Logic to happen after successful facebook login.
	  $this->redirect('/');
	}
		
	public function login() {
	}
	
	public function logout(){
	  $this->Auth->logout();
	  $this->Session->destroy();
	  $this->redirect('login');
	}
	
	public function account(){
		$user = $this->Auth->user();
		$accountIds = $this->getAccountIds();
		
		$this->loadModel('BankAccount');
		$bankAccounts = $this->BankAccount->getBankAccountsForAccounts($accountIds);
		
		$this->set(compact('user','bankAccounts'));
	}
}
