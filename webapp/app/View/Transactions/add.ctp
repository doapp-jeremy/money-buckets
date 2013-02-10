<h3>Add Transaction</h3>
<?php 
echo $this->Form->create('Transaction', array(
		'id'=>'createTransactionForm',
		'action' => 'add',
		'class' => 'form-vertical',
		'inputDefaults' => array(
				'between'=>'<div class="controls">',
				'after'=>'</div>',
				'class'=>'input-medium',
				'div'=>array('class'=>'control-group','style'=>'display: inline-block; margin-left: 15px;'),
		)
));
?>
<fieldset id="accountFieldset">
  <?php
 	echo $this->Form->input("creator_id", array(
 			'type'=>'hidden',
 			'value'=>$user['id']
 	));
	$userOptions = array('type'=>'hidden','value'=>$user['id']);
	if (count($userList) != 1)
	{
		$userOptions =  array(
				'type' => 'select',
  			'label'=>array('text'=>__('Person'),'class'=>'control-label'),
  			'options' => $userList,
  			'required',			//Set HTML5 required attribure
  	);
	}
	echo $this->Form->input("user_id", $userOptions);
  $bankAccountIds = array_keys($bankAccountList);
  $bankAccountListDisabled = count($bankAccountList) == 1;
  $bankAccountOptions = array('type'=>'hidden','value'=>$bankAccountIds[0]);
  if (count($bankAccountList) != 1)
  {
  	$bankAccountOptions = array(
  	    'type' => 'select',
  			'label'=>array('text'=>__('Bank Account'),'class'=>'control-label'),
  			'options' => $bankAccountList,
  			'required',			//Set HTML5 required attribure
  	);
  }
  echo $this->Form->input("bank_account_id", $bankAccountOptions);
  echo $this->Form->input("transaction_type_id", array(
			'label'=>array('text'=>__('Type'),'class'=>'control-label'),
	    'options' => $transactionTypes
	));	
  echo $this->Form->input("label", array(
			'label'=>array('text'=>__('Label'),'class'=>'control-label'),
	));	
  echo $this->Form->input("amount", array(
			'label'=>array('text'=>__('Amount'),'class'=>'control-label'),
			'required',			//Set HTML5 required attribure
	));
  echo $this->Form->input("date", array(
      'label'=>array('text'=>__('Date'),'class'=>'control-label'),
      'div'=>array('class'=>'control-group','style'=>'display: block; margin-left: 15px;')
  ));
  
	$i = 0;
	foreach ($buckets as $bucket)
	{	
	  echo $this->Form->input("TransactionEntry.{$i}.user_id", array('type' => 'hidden','value' => $user['id']));
	  echo $this->Form->input("TransactionEntry.{$i}.bucket_id", array('type' => 'hidden','value' => $bucket['Bucket']['id']));
	  echo $this->Form->input("TransactionEntry.{$i}.amount", array('value' => 0,'label'=>array('text'=>__($bucket['Bucket']['name'] . ' Amount'),'class'=>'control-label')));
	  $i++;
	}
	?>
	<div class="form-actions">
	  <button type="submit" class="btn btn-primary">Add Transaction</button>
	</div>
</fieldset>
<?php echo $this->Form->end();?>
