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
      'DebugKit.Toolbar',
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
  
  const ACCOUNT_LIST = 'account_list';
  protected function getAccountList($refresh = false)
  {
    $accounts = array();
    if ($refresh || (null === ($accounts = $this->Session->read(self::ACCOUNT_LIST))))
    {
      $this->loadModel('Account');
      if ($user = $this->Auth->user())
      {
        $accounts = $this->Account->getAccountsForUser($user);
        $this->Session->write(self::ACCOUNT_LIST, $accounts);
      }
    }
    return $accounts;
  }
}
