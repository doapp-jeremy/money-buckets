<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
  public $helpers = array(
      'Html','Form','Js','Session',
      'Facebook.Facebook',
      'AssetCompress.AssetCompress',
      'Bootstrap' => array('className' => 'TwitterBootstrap.TwitterBootstrap'));
  public $components = array('Session',
      //'DebugKit.Toolbar',
      'Auth' => array(
          'authenticate' => array(
              'Form' => array(
                  'fields' => array('username' => 'email')
              )
          ),
          'authorize' => 'Controller'
      ),
      'Facebook.Connect' => array('model' => 'User')
  );
  
  function isAuthorized()
  {
  	return true;
  }
  
  function beforeRender()
  {
  	$this->set(array('controller' => $this->name));
  	$this->set(array('action' => $this->action));
  	return parent::beforeRender();
  }
  
  const FRIEND_LIST = 'friends';
  protected function getFriends($refresh = false)
  {
    if ($refresh || (null === ($friendList = $this->Session->read(self::FRIEND_LIST))))
    {
      $user = $this->Auth->user();
      debug($user);
      App::uses('FB', 'Facebook.Lib');
      if (!empty($user['facebook_id']))
      {
        $friends = FB::api("/{$user['facebook_id']}/friends");
        debug($friends);
        $friendList = array();
        foreach ($friends['data'] as $friend)
        {
          $friendList[$friend['id']] = $friend['name'];
        }
        $this->Session->write(self::FRIEND_LIST, $friendList);
      }
    }
    return $friendList;
  }
  
  const ACCOUNT_LIST = 'account_list';
  protected function getAccountList($refresh = false, $redirectIfEmpty = true)
  {
    $accounts = array();
    if ($refresh || (null === ($accounts = $this->Session->read(self::ACCOUNT_LIST))))
    {
      if ($user = $this->Auth->user())
      {
        $this->loadModel('User');
        $accounts = $this->User->getAccountsForUser($user);
        $this->Session->write(self::ACCOUNT_LIST, $accounts);
      }
    }
    if ($redirectIfEmpty && empty($accounts))
    {
    	$this->redirect(array('controller' => 'Accounts','action' => 'add'));
    }
    return $accounts;
  }
  
  protected function getAccountIds($refresh = false, $redirectIfEmpty = true)
  {
  	$accountList = $this->getAccountList($refresh, $redirectIfEmpty);
  	return array_keys($accountList);
  }

  protected function getAccountId($refresh = false, $redirectIfEmpty = true)
  {
  	$accountIds = $this->getAccountIds($refresh,$redirectIfEmpty);
  	return $accountIds[0];
  }
}
