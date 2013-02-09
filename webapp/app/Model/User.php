<?php
App::uses('AppModel', 'Model');
class User extends AppModel {
  
  public $belongsTo = array(
      'Account' => array(
          'className' => 'Account',
          'foreignKey' => 'account_id',
          'conditions' => '',
          'fields' => '',
          'order' => ''
      ),
  );
  
  public function afterSave($created)
  {
  	if ($created)
  	{
  		$account = array(
  				'Account' => array('name' => 'Main Account','user_id' => $this->data['User']['id']),
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
  
}
