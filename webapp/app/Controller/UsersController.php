<?php
App::uses('AppController', 'Controller');

class UsersController extends AppController {

	public $uses = array();

	function isAuthorized()
	{
	  return true;
	}
	
	//Add an email field to be saved along with creation.
	function beforeFacebookSave(){
	  //debug('beforeFacebookSave');
	  $this->Connect->authUser['User']['email'] = $this->Connect->user('email');
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
	  $this->redirect('/buckets');
	}
		
	public function login() {
	  if($user = $this->Connect->registrationData()){
	    debug($user);
	  }
	}
	
	public function logout(){
	  $this->Auth->logout();
	  $this->Session->destroy();
	  $this->redirect('login');
	}
}
