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
  
  
  public function getBankAccountsForAccounts($accountIds)
  {
    $conditions = array($this->alias . '.account_id' => $accountIds);
    return $this->find('all',compact('conditions'));
  }
  
  public function getBankAccountListForAccounts($accountIds)
  {
    $conditions = array($this->alias . '.account_id' => $accountIds);
    return $this->find('list',compact('conditions'));
  }
  
  public function editTransaction($transaction)
  {
  	debug($transaction);
  	$transactionBefore = $this->Transaction->read(null,$transaction['Transaction']['id']);
  	debug($transactionBefore);
  	exit();
  	$dateBefore = date_create($transactionBefore['Transaction']['date']);
  	$transactionDate = $transaction['Transaction']['date']['year'] . '-' . $transaction['Transaction']['date']['month'] . '-' . $transaction['Transaction']['date']['day'];
  	$dateAfter = date_create($transactionDate);
  	debug($dateBefore);
  	debug($dateAfter);
  	$bankAccountBefore = 0;
  	if ($this->processTransactionsAfter($transaction['Transaction']['bank_account_id'], $transactionDate, $bankAccountBefore))
  	{
  		if ($dateBefore < $dateAfter)
  		{
  			// save new transaction and then process transactions after what it was before
  			return $this->processTransactionsAfter($transactionBefore);
  		}
  		else
  		{
  			//return $this->processTransactionsAfter($transaction);
  			return $this->addTransaction($transaction);
  		}
  	}
  	return false;
  }
  
  public function processTransactionsAfter($bankAccountId, $previousTransactionId, $transactionDate, $bankAccountBefore)
  {
  	//$transactionDate = $transaction['Transaction']['date']['year'] . '-' . $transaction['Transaction']['date']['month'] . '-' . $transaction['Transaction']['date']['day'];
  	$fields = array('BankAccount.id','BankAccount.account_id','BankAccount.current_balance','BankAccount.unallocated_balance');
  	//$bankAccountId = $transaction['Transaction']['bank_account_id'];
  	$conditions = array('BankAccount.id' => $bankAccountId);
  	$contain = array(
  			'Account' => array('fields' => array('Account.unallocated_bucket_id')),
  			'Transaction' => array(
  					'fields' => array('Transaction.id','Transaction.transaction_type_id','Transaction.amount','Transaction.bank_account_after'),
  					'conditions' => array("Transaction.date > '$transactionDate'"),
  					'order' => array('Transaction.date' => 'ASC', 'Transaction.created' => 'ASC')
  			)
  	);
  	$bankAccount = $this->find('first',compact('fields','conditions','contain'));
  	debug($bankAccount);
  	if (!empty($bankAccount['Transaction']))
  	{
  		//$bankAccountBefore = $transaction['Transaction']['bank_account_after'];
  		foreach ($bankAccount['Transaction'] as &$transactionAfter)
  		{
  			$transactionAfter['bank_account_before'] = $bankAccountBefore;
  			$transactionAfter['previous_transaction_id'] = $previousTransactionId;
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
  			$previousTransactionId = $transactionAfter['id'];
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
  
  public function addTransaction($transaction)
  {
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
  	
  	if (empty($transaction['Transaction']['id']))
  	{
  		$this->Transaction->create();
  	}
  	debug($transaction);
  	if ($this->Transaction->saveAll($transaction) && $this->save($bankAccount))
  	{
  		$transactionDate = $transaction['Transaction']['date']['year'] . '-' . $transaction['Transaction']['date']['month'] . '-' . $transaction['Transaction']['date']['day'];
  		return $this->processTransactionsAfter($transaction['Transaction']['bank_account_id'], $this->Transaction->getLastInsertID(), $transactionDate, $transaction['Transaction']['bank_account_after']);
  	}
  	return false;
  }
  
}
