<?php
App::uses('AppModel', 'Model');
class BankAccount extends AppModel {
  
  public $belongsTo = array(
      'Account' => array(
          'className' => 'Account',
          'foreignKey' => 'account_id',
          'conditions' => '',
          'fields' => '',
          'order' => ''
      ),
  );
  
  public function getBankAccountsForAccounts($accountIds)
  {
    $conditions = array($this->alias . '.account_id' => $accountIds);
    return $this->find('all',compact('conditions'));
  }
  
}
