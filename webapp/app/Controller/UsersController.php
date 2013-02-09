<?php
App::uses('AppController', 'Controller');

class UsersController extends AppController {

  public $helpers = array('Facebook.Facebook');
  
/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Users';

	public $uses = array();

	//Add an email field to be saved along with creation.
	function beforeFacebookSave(){
	  debug($this->Connect->authUser);
	  debug($this->Connect->user('email'));
	  $this->Connect->authUser['User']['email'] = $this->Connect->user('email');
	  debug($this->Connect->authUser);
	  return true; //Must return true or will not save.
	}
	
	function beforeFacebookLogin($user){
	  //Logic to happen before a facebook login
	}
	
	function afterFacebookLogin(){
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
	}
}
