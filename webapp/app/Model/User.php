<?php
App::uses('AppModel', 'Model');
class User extends AppModel {
  
  var $createAccount = true;
//   public $belongsTo = array(
//       'Account' => array(
//           'className' => 'Account',
//           'foreignKey' => 'account_id',
//           'conditions' => '',
//           'fields' => '',
//           'order' => ''
//       ),
//   );
  
  public $hasAndBelongsToMany = array(
      'Account' => array(
          'className' => 'Account',
          'joinTable' => 'accounts_users',
          'foreignKey' => 'user_id',
          'associationForeignKey' => 'account_id',
          'unique' => 'keepExisting',
          'conditions' => '',
          'fields' => '',
          'order' => '',
          'limit' => '',
          'offset' => '',
          'finderQuery' => '',
          'deleteQuery' => '',
          'insertQuery' => ''
      ),
//       'AccountViewer' => array(
//           'className' => 'Account',
//           'joinTable' => 'accounts_users',
//           'foreignKey' => 'facebook_id',
//           'associationForeignKey' => 'account_id',
//           'unique' => 'keepExisting',
//           'conditions' => '',
//           'fields' => '',
//           'order' => '',
//           'limit' => '',
//           'offset' => '',
//           'finderQuery' => '',
//           'deleteQuery' => '',
//           'insertQuery' => ''
//       )
  );
  
  public function afterSave($created)
  {
  	if ($created && $this->createAccount)
  	{
  		$account = array(
  				'Account' => array(
  				    'name' => 'Main Account','user_id' => $this->data['User']['id'],
  				    'User' => array('user_id' => $this->data['User']['id'])
  				),
  				'Bucket' => array(array('name' => 'Unallocated Bucket'))
  		);
  		if ($this->Account->saveAssociated($account, array('deep' => true)))
  		{
  			// set this newly created bucket as the unallocated one
  			if ($bucketId = $this->Account->Bucket->getLastInsertID())
  			{
  				$this->Account->saveField('unallocated_bucket_id', $bucketId);
  			}
  		}
  	}
  }
  
  public function getAccountsForUser($user)
  {
    $conditions = array($this->alias . '.id' => $user['id']);
    $contain = array('Account');
    $user = $this->find('first',compact('fields','conditions','contain'));
    debug($user);
    $accountList = Set::combine($user['Account'], '{n}.id','{n}.name');
    debug($accountList);
    return $accountList;
  }
}
