<?php
App::uses('AppModel', 'Model');
class Bucket extends AppModel {
  
  public $order = array('Bucket.name' => 'ASC');
  
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
  
  public function getTransactionEntries($bucketId)
  {
    $conditions = array('Bucket.id' => $bucketId);
    $fields = array('Bucket.id','Bucket.name');
    $contain = array(
        'TransactionEntry' => array(
            'fields' => array('TransactionEntry.id','TransactionEntry.date','TransactionEntry.label','TransactionEntry.notes','TransactionEntry.bucket_before','TransactionEntry.amount','TransactionEntry.bucket_after'),
            'Transaction' => array('fields' => array('Transaction.id','Transaction.transaction_type_id','Transaction.amount'))
        )
    );
    return $this->find('first',compact('fields','conditions','contain'));
  }
  
}
