<?php
App::uses('AppModel', 'Model');
class Transaction extends AppModel {
  const PURCHASE = 1;
  const DEPOSIT = 2;
  const TRANSFER = 3;
  
  public $order = array('Transaction.date' => 'ASC', 'Transaction.created' => 'ASC');
  
  public $belongsTo = array(
      'PreviousTransaction' => array(
          'className' => 'Transaction',
          'foreignKey' => 'previous_transaction_id',
          'conditions' => '',
          'fields' => '',
          'order' => ''
      ),
  		'BankAccount' => array(
          'className' => 'BankAccount',
          'foreignKey' => 'bank_account_id',
          'conditions' => '',
          'fields' => '',
          'order' => ''
      ),
  );
  
  public $hasMany = array(
  		'TransactionEntry' => array(
  				'className' => 'TransactionEntry',
  				'foreignKey' => 'transaction_id',
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
  
  public function beforeDelete($cascade = true)
  {
  	$fields = array('Transaction.bank_account_id','Transaction.amount','Transaction.unallocated_amount');
  	$conditions = array('Transaction.id' => $this->id);
  	$contain = array(
  			'BankAccount' => array('fields' => array('BankAccount.id','BankAccount.account_id','BankAccount.current_balance','BankAccount.unallocated_balance')),
  			'TransactionEntry' => array(
  					'fields' => array('TransactionEntry.bucket_id','TransactionEntry.amount'),
  					'conditions' => array('TransactionEntry.amount > 0'),
  					'Bucket' => array('fields' => array('Bucket.available_balance','Bucket.actual_balance'))
  			),
  	);
  	$this->data = $this->read(null, $this->id);
  	debug($this->data);
  	return parent::beforeDelete($cascade);
  }
  
  public function afterDelete()
  {
  	$this->BankAccount->reprocessTransactions($this->data['Transaction']['bank_account_id']);
  }
  
  public function updateAfterSave($transaction)
  {
  	debug($transaction);
  	if ((self::TRANSFER != $transaction['Transaction']['transaction_type_id']))
  	{
  		$contain = array(
  				'BankAccount' => array('fields' => array('BankAccount.id','BankAccount.account_id','BankAccount.current_balance','BankAccount.unallocated_balance'),
  						'Account' => array('fields' => array('Account.unallocated_bucket_id'))
  				),
  		);
  		$fields = array('Transaction.bank_account_id','Transaction.unallocated_amount');
  		$conditions = array('Transaction.id' => $transaction['Transaction']['id']);
  		$bankAccount = $this->find('first',compact('fields','conditions','contain'));
  		debug($bankAccount);
  		$unallocatedAmount = 0;
  		$unallocatedBucketEntry = Set::extract("/TransactionEntry[bucket_id={$bankAccount['BankAccount']['Account']['unallocated_bucket_id']}]",$transaction);
  		debug($unallocatedBucketEntry);
  		if (!empty($unallocatedBucketEntry[0]['TransactionEntry']['amount']) && ($bankAccount['BankAccount']['Account']['unallocated_bucket_id'] == $unallocatedBucketEntry[0]['TransactionEntry']['bucket_id']))
  		{
  			$unallocatedAmount = $unallocatedBucketEntry[0]['TransactionEntry']['amount'];
  		}
  		switch ($transaction['Transaction']['transaction_type_id'])
  		{
  			case self::DEPOSIT:
  				{
  					$bankAccount['BankAccount']['current_balance'] += $transaction['Transaction']['amount'];
  					$bankAccount['BankAccount']['unallocated_balance'] += $unallocatedAmount;
  					$bankAccount['Transaction']['unallocated_amount'] += $unallocatedAmount;
  					break;
  				}
  			case self::PURCHASE:
  				{
  					$bankAccount['BankAccount']['current_balance'] -= $transaction['Transaction']['amount'];
  					$bankAccount['BankAccount']['unallocated_balance'] -= $unallocatedAmount;
  					$bankAccount['Transaction']['unallocated_amount'] -= $unallocatedAmount;
  					break;
  				}
  		}
  		$this->BankAccount->save($bankAccount['BankAccount']);
  		$this->saveField('unallocated_amount',$bankAccount['Transaction']['unallocated_amount']);
  	}
  }
  
  public function getTransactionsForBankAccount($bankAccountIds, $fields = array(), $contain = array())
  {
    $conditions = array('Transaction.bank_account_id' => $bankAccountIds);
    return $this->find('all',compact('fields','conditions','contain'));
  }
}
