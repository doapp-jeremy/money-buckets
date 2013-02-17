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
  
  public $hasMany = array(
  		'Transaction' => array(
  				'className' => 'Transaction',
  				'foreignKey' => 'bank_account_id',
  				'dependent' => true,
  				'conditions' => '',
  				'fields' => '',
  				'order' => array('Transaction.date' => 'ASC', 'Transaction.created' => 'ASC'),
  				'limit' => '',
  				'offset' => '',
  				'exclusive' => '',
  				'finderQuery' => '',
  				'counterQuery' => ''
  		)
  );
  
  
  public function getBankAccountsForAccounts($accountIds, $fields = array(), $contain = array())
  {
    $conditions = array($this->alias . '.account_id' => $accountIds);
    return $this->find('all',compact('fields','conditions','contain'));
  }
  
  public function getBankAccountListForAccounts($accountIds)
  {
    $conditions = array($this->alias . '.account_id' => $accountIds);
    return $this->find('list',compact('conditions'));
  }
  
  public function editTransaction($transaction)
  {
  	$this->updateTransactionEntries($transaction);
  	debug($transaction);
  	if ($this->Transaction->saveAssociated($transaction))
  	{
  		return $this->reprocessTransactions($transaction['Transaction']['bank_account_id']);
  	}
  	echo "\nedit";
  	exit();
  	
  	$fields = array('Transaction.id','Transaction.date');
  	$conditions = array('Transaction.id' => $transaction['Transaction']['id']);
  	$contain = array(
  			'PreviousTransaction' => array('fields' => array('PreviousTransaction.id','PreviousTransaction.bank_account_after','PreviousTransaction.bank_account_id','PreviousTransaction.date')) 
  	);
  	$transactionBefore = $this->Transaction->find('first',compact('fields','conditions','contain'));
  	debug($transactionBefore);
  	$bankAccountId = $transaction['Transaction']['bank_account_id'];
  	if (!$this->Transaction->saveAssociated($transaction))
  	{
  		return false;
  	}
  	$dateBefore = date_create($transactionBefore['Transaction']['date']);
  	$transactionDate = $transaction['Transaction']['date']['year'] . '-' . $transaction['Transaction']['date']['month'] . '-' . $transaction['Transaction']['date']['day'];
  	$dateAfter = date_create($transactionDate);
  	debug($dateBefore);
  	debug($dateAfter);
  	if ($dateBefore < $dateAfter)
  	{
  		$previousTransactionId = $transactionBefore['Transaction']['previous_transaction_id'];
  		$bankAccountBefore = !empty($transactionBefore['PreviousTransaction']['bank_account_after']) ? $transactionBefore['PreviousTransaction']['bank_account_after'] : 0;
  		$date = !empty($transactionBefore['PreviousTransaction']['date']) ? $transactionBefore['PreviousTransaction']['date'] : $transactionBefore['Transaction']['date'];
  		debug($previousTransactionId);
  		debug($date);
  		debug($bankAccountBefore);
  		$this->processTransactionsAfter($bankAccountId, $previousTransactionId, $date, $bankAccountBefore);
  	}
  	else
  	{
  		$fields = array('Transaction.id','Transaction.date','Transaction.previous_transaction_id','Transaction.bank_account_after');
  		$conditions = array("Transaction.date <= '{$transactionDate}'","Transaction.id !={$transaction['Transaction']['id']}");
  		$order = array('Transaction.date' => 'DESC', 'Transaction.created' => 'DESC');
  		$transactionBefore = $this->Transaction->find('first',compact('fields','conditions','order'));
  		debug($transactionBefore);
  		$previousTransactionId = $transactionBefore['Transaction']['id'];
  		$bankAccountBefore = !empty($transactionBefore['Transaction']['bank_account_after']) ? $transactionBefore['Transaction']['bank_account_after'] : 0;
  		debug($previousTransactionId);
  		debug($bankAccountBefore);
  		$this->processTransactionsAfter($bankAccountId, $previousTransactionId, $transactionDate, $bankAccountBefore);
  	}
  	echo "wtf";
  	exit();
  	return false;
  }
  
  private function updateTransactionEntries(&$transaction)
  {
  	$transactionEntries = array();
  	$entryAmount = 0;
  	foreach ($transaction['TransactionEntry'] as $transactionEntry)
  	{
  		if (!empty($transactionEntry['amount']))
  		{
  			$entryAmount += $transactionEntry['amount'];
  			$transactionEntry['date'] = $transaction['Transaction']['date'];
  			$transactionEntry['label'] = $transaction['Transaction']['label'];
  			$transactionEntry['notes'] = $transaction['Transaction']['notes'];
  			$transactionEntries[] = $transactionEntry;
  		}
  	}
  	$unallocattedAmount = $transaction['Transaction']['amount'] - $entryAmount;
  	if (!empty($unallocattedAmount))
  	{
  		$fields = array('BankAccount.id');
  		$conditions = array('BankAccount.id' => $transaction['Transaction']['bank_account_id']);
  		$contain = array(
  				'Account' => array('fields' => array('Account.id','Account.unallocated_bucket_id'))
  		);
  		$bankAccount = $this->BankAccount->find('first',compact('fields','conditions','contain'));
  		$unallocattedBucketId = $bankAccount['Account']['unallocated_bucket_id'];
  		$newTransactionEntry = array(
  				'user_id' => $transaction['Transaction']['user_id'],
  				'date' => $transaction['Transaction']['date'],
  				'bucket_id' => $unallocattedBucketId,
  				'amount' => $unallocattedAmount
  		);
  		if (!empty($transactionEntry['id']))
  		{
  			$newTransactionEntry['id'] = $transactionEntry['id'];
  		}
  		$transactionEntries[] = $newTransactionEntry;
  	}
  	$transaction['TransactionEntry'] = $transactionEntries;
  }
  
  public function addTransaction($transaction)
  {
  	$this->updateTransactionEntries($transaction);
  	debug($transaction);
  	$transactionDate = $transaction['Transaction']['date']['year'] . '-' . $transaction['Transaction']['date']['month'] . '-' . $transaction['Transaction']['date']['day'];
  	// find transaction before this one to get amount before
  	$fields = array('BankAccount.id','BankAccount.account_id','BankAccount.opening_balance','BankAccount.current_balance','BankAccount.unallocated_balance');
  	$conditions = array('BankAccount.id' => $transaction['Transaction']['bank_account_id']);
  	$contain = array(
  			'Account' => array('fields' => array('Account.unallocated_bucket_id')),
  			'Transaction' => array(
  					'fields' => array('Transaction.id','Transaction.bank_account_after'),
  					'conditions' => array("Transaction.date <= '$transactionDate'"),
  					'order' => array('Transaction.date' => 'DESC', 'Transaction.created' => 'DESC'),
  					'limit' => 1
  			)
  	);
  	$bankAccount = $this->find('first',compact('fields','conditions','contain'));
  	debug($bankAccount);
  	if (!empty($bankAccount['Transaction']))
  	{
  		$transaction['Transaction']['previous_transaction_id'] = $bankAccount['Transaction'][0]['id'];
  		//exit();
  	}
  	$bankAccountBefore = !empty($bankAccount['Transaction']) ? $bankAccount['Transaction'][0]['bank_account_after'] : $bankAccount['BankAccount']['opening_balance'];
  	debug($bankAccountBefore);
  	$transaction['Transaction']['bank_account_before'] = $bankAccountBefore;
  	$amount = $transaction['Transaction']['amount'];
  	
  	$unallocatedAmount = 0;
  	$unallocatedBucketEntry = Set::extract("/TransactionEntry[bucket_id={$bankAccount['Account']['unallocated_bucket_id']}]",$transaction);
  	debug($unallocatedBucketEntry);
  	if (!empty($unallocatedBucketEntry[0]['TransactionEntry']['amount']) && ($bankAccount['Account']['unallocated_bucket_id'] == $unallocatedBucketEntry[0]['TransactionEntry']['bucket_id']))
  	{
  		$unallocatedAmount = $unallocatedBucketEntry[0]['TransactionEntry']['amount'];
  	}
  	$transaction['Transaction']['unallocated_amount'] = $unallocatedAmount;
  	switch ($transaction['Transaction']['transaction_type_id'])
  	{
  		case Transaction::DEPOSIT:
  			{
  				$transaction['Transaction']['bank_account_after'] = $bankAccountBefore + $amount;
  				$bankAccount['BankAccount']['current_balance'] += $amount;
  				$bankAccount['BankAccount']['unallocated_balance'] += $unallocatedAmount;
  				break;
  			}
  		case Transaction::PURCHASE:
  			{
  				$transaction['Transaction']['bank_account_after'] = $bankAccountBefore - $amount;
  				$bankAccount['BankAccount']['current_balance'] -= $amount;
  				$bankAccount['BankAccount']['unallocated_balance'] -= $unallocatedAmount;
  				break;
  			}
  	}
  	
  	$bucketsChanged = Set::extract('/TransactionEntry[amount!=0]/bucket_id',$transaction);
  	
  	if (empty($transaction['Transaction']['id']))
  	{
  		$this->Transaction->create();
  	}
  	debug($transaction);
  	if ($this->Transaction->saveAll($transaction) && $this->save($bankAccount))
  	{
//   		$transactionDate = $transaction['Transaction']['date']['year'] . '-' . $transaction['Transaction']['date']['month'] . '-' . $transaction['Transaction']['date']['day'];
//   		return $this->processTransactionsAfter($transaction['Transaction']['bank_account_id'], $this->Transaction->getLastInsertID(), $transactionDate, $transaction['Transaction']['bank_account_before'], $bucketsChanged);
			return $this->reprocessTransactions($transaction['Transaction']['bank_account_id']);
  	}
  	return false;
  }
  
  public function processTransactionsAfter($bankAccountId, $transactionId, $transactionDate, $bankAccountBefore, $bucketsChanged)
  {
//   	$fields = array('Bucket.id','Bucket.name');
//   	$conditions = array('Bucket.id' => $bucketsChanged);
//   	$contain = array(
//   		'TransactionEntry' => array(
//   				'fields' => array('TransactionEntry.id','TransactionEntry.bucket_before','TransactionEntry.amount','TransactionEntry.after'),
//   				'Transaction' => array('Transaction')
//   	);
//   	$bucketsChanged = $this->Bucket->find('all',compact('fields','conditions','contain'));
  	 
  	$fields = array('BankAccount.id','BankAccount.account_id','BankAccount.current_balance','BankAccount.unallocated_balance');
  	$conditions = array('BankAccount.id' => $bankAccountId);
  	$contain = array(
  			'Account' => array('fields' => array('Account.unallocated_bucket_id')),
  			'Transaction' => array(
  					'fields' => array('Transaction.id','Transaction.transaction_type_id','Transaction.amount','Transaction.bank_account_after'),
  					//'conditions' => array("Transaction.date > '$transactionDate'"),
  					//'conditions' => array("Transaction.id > $transactionId"),
  					'order' => array('Transaction.date' => 'ASC', 'Transaction.created' => 'ASC'),
  					'TransactionEntry' => array('fields' => array('TransactionEntry.id','TransactionEntry.amount'))
  			)
  	);
  	
  	if (!empty($transactionId))
  	{
  		$contain['Transaction']['conditions'] = array("Transaction.id >= $transactionId");
  	}
  	$bankAccount = $this->find('first',compact('fields','conditions','contain'));
  	debug($bankAccount);
  	if (!empty($bankAccount['Transaction']))
  	{
  		foreach ($bankAccount['Transaction'] as &$transactionAfter)
  		{
  			debug($transactionAfter);exit();
  			$transactionAfter['bank_account_before'] = $bankAccountBefore;
  			$transactionAfter['previous_transaction_id'] = $transactionId;
  			switch ($transactionAfter['transaction_type_id'])
  			{
  				case Transaction::DEPOSIT:
  					{
  						$transactionAfter['bank_account_after'] = $bankAccountBefore + $transactionAfter['amount'];
  						break;
  					}
  				case Transaction::PURCHASE:
  					{
  						$transactionAfter['bank_account_after'] = $bankAccountBefore - $transactionAfter['amount'];
  						break;
  					}
  			}
  			foreach ($transactionAfter['TransactionEntry'] as &$transactionEntry)
  			{
  				
  			}
  			
  			$transactionId = $transactionAfter['id'];
  			$bankAccountBefore = $transactionAfter['bank_account_after'];
  			$bankAccount['BankAccount']['current_balance'] = $bankAccountBefore;
  			debug($bankAccount);
  		}
  		debug($bankAccount);
  		$this->id = $bankAccount['BankAccount']['id'];
  		return ($this->saveField('current_balance',$bankAccount['BankAccount']['current_balance']) && $this->Transaction->saveAll($bankAccount['Transaction'])); 
  	}
  	return true;
  }
  
  private function reprocessTransactions($bankAccountId)
  {
  	$fields = array('BankAccount.id','BankAccount.account_id','BankAccount.opening_balance','BankAccount.current_balance');
  	$conditions = array('BankAccount.id' => $bankAccountId);
  	$contain = array(
//   			'Bucket' => array(
//   					'fields' => array('Bucket.id','Bucket.name','Bucket.opening_balance','Bucket.available_balance','Bucket.actual_balance'),
//   					'TransactionEntry' => array(
//   							'fields' => array('TransactionEntry.id','TransactionEntry.bucket_id','TransactionEntry.bucket_before','TransactionEntry.amount','TransactionEntry.bucket_after'),
//   					)
//   			),
  			'Transaction' => array(
  					'fields' => array('Transaction.id','Transaction.transaction_type_id','Transaction.bank_account_before','Transaction.amount','Transaction.bank_account_after'),
  					'order' => array('Transaction.date' => 'ASC',' Transaction.created' => 'ASC'),
  			)
  	);
  	$bankAccount = $this->find('first',compact('fields','conditions','contain'));
  	debug($bankAccount);
  	$bankAccountBefore = $bankAccount['BankAccount']['opening_balance'];
  	$previousTransactionId = null;
  	foreach ($bankAccount['Transaction'] as &$transaction)
  	{
  		$transaction['bank_account_before'] = $bankAccountBefore;
  		$transaction['previous_transaction_id'] = $previousTransactionId;
  		switch ($transaction['transaction_type_id'])
  		{
  			case Transaction::DEPOSIT:
  				{
  					$transaction['bank_account_after'] = $bankAccountBefore + $transaction['amount'];
  					break;
  				}
  			case Transaction::PURCHASE:
  				{
  					$transaction['bank_account_after'] = $bankAccountBefore - $transaction['amount'];
  					break;
  				}
  		}
  		$previousTransactionId = $transaction['id'];
  		$bankAccountBefore = $transaction['bank_account_after'];
  		$bankAccount['BankAccount']['current_balance'] = $bankAccountBefore;
  		debug($bankAccount);
  	}
  	debug($bankAccount);
  	$this->id = $bankAccount['BankAccount']['id'];
  	if ($this->saveField('current_balance',$bankAccount['BankAccount']['current_balance']) && $this->Transaction->saveAll($bankAccount['Transaction']))
  	{
  		return $this->reprocessBucketsForAccount($bankAccount['BankAccount']['account_id']);
  	}
  	return false;
  }
  
  private function reprocessBucketsForAccount($accountId)
  {
  	$fields = array('Bucket.id','Bucket.opening_balance','Bucket.available_balance','Bucket.actual_balance');
  	$conditions = array('Bucket.account_id' => $accountId);
  	$contain = array(
  			'TransactionEntry' => array(
  					'fields' => array('TransactionEntry.id','TransactionEntry.bucket_before','TransactionEntry.bucket_before','TransactionEntry.bucket_before','TransactionEntry.amount','TransactionEntry.bucket_after'),
  					'order' => array('TransactionEntry.date' => 'ASC', 'TransactionEntry.created' => 'ASC'),
  					'Transaction' => array('fields' => array('Transaction.id','Transaction.transaction_type_id'))
  			)
  	);
  	$buckets = $this->Transaction->TransactionEntry->Bucket->find('all',compact('fields','conditions','contain'));
  	debug($buckets);
  	foreach ($buckets as &$bucket)
  	{
  		$availableBalance = $actualBalance = $bucketBefore = $bucket['Bucket']['opening_balance'];
  		foreach ($bucket['TransactionEntry'] as &$transactionEntry)
  		{
  			$amount = $transactionEntry['amount'];
  			$transactionEntry['bucket_before'] = $bucketBefore;
  			switch($transactionEntry['Transaction']['transaction_type_id'])
  			{
  				case Transaction::DEPOSIT:
  					{
  						$availableBalance += $amount;
  						$actualBalance += $amount;
  						$transactionEntry['bucket_after'] = $bucketBefore + $amount;
  						break;
  					}
  				case Transaction::PURCHASE:
  					{
  						$availableBalance -= $amount;
  						$actualBalance -= $amount;
  						$transactionEntry['bucket_after'] = $bucketBefore - $amount;
  						break;
  					}
  			}
  			$bucketBefore = $transactionEntry['bucket_after'];
  		}
  		$bucket['Bucket']['available_balance'] = $availableBalance;
  		$bucket['Bucket']['actual_balance'] = $actualBalance;
  	}
  	debug($buckets);
  	return $this->Transaction->TransactionEntry->Bucket->saveAll($buckets, array('deep' => true));
  }
}
