<?php
App::uses('AppModel', 'Model');
class Bucket extends AppModel {
  
  public $belongsTo = array(
      'Account' => array(
          'className' => 'Account',
          'foreignKey' => 'account_id',
          'conditions' => '',
          'fields' => '',
          'order' => ''
      ),
  );
  
  public $hasMany = array(
  		'TransactionEntry' => array(
  				'className' => 'TransactionEntry',
  				'foreignKey' => 'bucket_id',
  				'dependent' => true,
  				'conditions' => '',
  				'fields' => '',
  				'order' => '',
  				'limit' => '',
  				'offset' => '',
  				'exclusive' => '',
  				'finderQuery' => '',
  				'counterQuery' => ''
  		)
  );
    
  public function getBucketsForAccounts($accountIds,$fields=array(),$contains=array())
  {
    $conditions = array($this->alias . '.account_id' => $accountIds);
    return $this->find('all',compact('fields','conditions','contain'));
  }
  
}
