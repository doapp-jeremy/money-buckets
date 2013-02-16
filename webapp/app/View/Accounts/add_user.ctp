<h3>Add Friend To Account</h3>
<?php 
echo $this->Form->create('Account', array(
		'id'=>'createAccountForm',
		'action' => 'add_user',
		'class' => 'form-horizontal',
		'inputDefaults' => array(
				'between'=>'<div class="controls">',
				'after'=>'</div>',
				'class'=>'input-medium',
				'div'=>array('class'=>'control-group','style'=>'display: inline-block; margin-left: 15px;'),
		)
));
?>
<fieldset id="accountUserFieldset">
  <?php
	echo $this->Form->input("Friend.id", array(
			'label'=>array('text'=>__('Friend'),'class'=>'control-label'),
	    'options' => $friendList,
	    'class'=>'input-xlarge',
			'required',			//Set HTML5 required attribure
	));
	$accountOptions = array('label'=>array('text'=>__('Account'),'class'=>'control-label'),'required');
	if (count($accountList) > 1)
	{
	  $accountOptions['type'] = 'select';
	  $accountOptions['options'] = $accountList;
	}
	else
	{
	  $accountOptions['type'] = 'hidden';
	  $accountIds = array_keys($accountList);
	  $accountOptions['value'] = $accountIds[0];
	}
	echo $this->Form->input("id", $accountOptions);
	?>	
	<div class="form-actions">
	  <button type="submit" class="btn btn-primary">Add Friend To Account</button>
	</div>
</fieldset>
<?php echo $this->Form->end();?>
