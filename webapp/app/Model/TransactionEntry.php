<?php
App::uses('AppModel', 'Model');
class TransactionEntry extends AppModel {
  
  public $belongsTo = array(
      'Transaction' => array(
          'className' => 'Transaction',
          'foreignKey' => 'transaction_id',
          'conditions' => '',
          'fields' => '',
          'order' => ''
      ),
  		'Bucket' => array(
          'className' => 'Bucket',
          'foreignKey' => 'bucket_id',
          'conditions' => '',
          'fields' => '',
          'order' => ''
      ),
  );
  
  public function afterSave($created)
  {
  	if ($created && !empty($this->data['TransactionEntry']['amount']))
  	{
  		$fields = array('TransactionEntry.transaction_id','TransactionEntry.bucket_id','TransactionEntry.amount');
  		$contain = array(
  				'Transaction' => array('fields' => array('Transaction.transaction_type_id','Transaction.date')),
  				'Bucket' => array('fields' => array('Bucket.available_balance','Bucket.actual_balance'))
  		);
  		$conditions = array('TransactionEntry.id' => $this->data['TransactionEntry']['id']);
  		$transactionEntry = $this->find('first',compact('fields','conditions','contain'));
  		debug($transactionEntry);
  		$today = date_create('now');
  		$transactionDate = date_create($transactionEntry['Transaction']['date']);
  		
  		switch ($transactionEntry['Transaction']['transaction_type_id'])
  		{
  			case Transaction::DEPOSIT:
  				{
  					$transactionEntry['Bucket']['available_balance'] += $transactionEntry['TransactionEntry']['amount'];
  					if (true || $transactionDate > $today) // TODO: run cron job to update available balance on actual days
  					{
  						$transactionEntry['Bucket']['actual_balance'] += $transactionEntry['TransactionEntry']['amount'];
  					}
  					break;
  				}
  			case Transaction::PURCHASE:
  				{
  					$transactionEntry['Bucket']['available_balance'] -= $transactionEntry['TransactionEntry']['amount'];
  					if (true || $transactionDate > $today) // TODO: run cron job to update available balance on actual days
  					{
  						$transactionEntry['Bucket']['actual_balance'] -= $transactionEntry['TransactionEntry']['amount'];
  					}
  					break;
  				}
  		}
  		$this->Bucket->save($transactionEntry['Bucket']);
  	}
  }
  
}
