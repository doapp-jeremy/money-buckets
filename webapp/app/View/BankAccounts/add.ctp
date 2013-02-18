<h3>Add Bank Account</h3>
<?php 
echo $this->Form->create('BankAccount', array(
		'id'=>'createBankAccountForm',
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
  $accountListDisabled = count($accountList) == 1;
  if ($accountListDisabled)
  {
  	$accountIds = array_keys($accountList);
  	echo $this->Form->input("account_id", array(
  			'type'=>'hidden',
  			'value'=>$accountIds[0]
  	));	
  }
  else
  {
  	echo $this->Form->input("account_id", array(
  	    'type' => 'select',
  			'label'=>array('text'=>__('Account'),'class'=>'control-label'),
  			'options' => $accountList,
  			'required',			//Set HTML5 required attribure
  	));
  }
	echo $this->Form->input("name", array(
			'label'=>array('text'=>__('Name'),'class'=>'control-label'),
			'required',			//Set HTML5 required attribure
	));	
	echo $this->Form->input("opening_balance", array(
			'label'=>array('text'=>__('Opening Balance'),'class'=>'control-label'),
			'required',			//Set HTML5 required attribure
	));	
	?>	
	<div class="form-actions">
	  <button type="submit" class="btn btn-primary">Add Bank Account</button>
	</div>
</fieldset>
<?php echo $this->Form->end();?>
