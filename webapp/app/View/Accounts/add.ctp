<h3>Add Account</h3>
<?php 
echo $this->Form->create('Account', array(
		'id'=>'createAccountForm',
		'action' => 'add',
		'class' => 'form-horizontal',
		'inputDefaults' => array(
				'between'=>'<div class="controls">',
				'after'=>'</div>',
				'class'=>'input-medium',
				'div'=>array('class'=>'control-group','style'=>'display: inline-block; margin-left: 15px;'),
		)
));
?>
<fieldset id="accountFieldset">
  <?php echo $this->element('user_id_input'); ?>
  <?php
	echo $this->Form->input("name", array(
			'label'=>array('text'=>__('Name'),'class'=>'control-label'),
			'required',			//Set HTML5 required attribure
	));	
	echo $this->Form->input("Bucket.0.name", array(
	    'type' => 'hidden',
	    'value' => 'Unallocated'
	));
	?>	
	<div class="form-actions">
	  <button type="submit" class="btn btn-primary">Add Account</button>
	</div>
</fieldset>
<?php echo $this->Form->end();?>

<h3>Your accounts</h3>

<ul>
<?php foreach($accountList as $accountId => $accountName):?>
<li><?php echo $accountName?></li>
<?php endforeach;?>
</ul>
