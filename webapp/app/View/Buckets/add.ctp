<h3>Add Bucket</h3>
<?php 
echo $this->Form->create('Bucket', array(
		'id'=>'createBucketForm',
		'action' => 'add',
		'class' => 'form-vertical',
		'inputDefaults' => array(
				'between'=>'<div class="controls">',
				'after'=>'</div>',
				'class'=>'input-medium',
				'div'=>array('class'=>'control-group','style'=>'display: block; margin-left: 15px;'),
		)
));
?>
<fieldset id="accountFieldset">
  <?php
  $accountIds = array_keys($accountList);
	$accountOptions = array('type'=>'hidden','value'=>$accountIds[0]);
	if (count($accountList) != 1)
	{
		$accountOptions =  array(
				'type' => 'select',
  			'label'=>array('text'=>__('Account'),'class'=>'control-label'),
  			'options' => $accountList,
  			'required',			//Set HTML5 required attribure
  	);
	}
	echo $this->Form->input("account_id", $accountOptions);
	echo $this->Form->input("name", array(
			'label'=>array('text'=>__('Name'),'class'=>'control-label'),
			'required',			//Set HTML5 required attribure
	));
	echo $this->Form->input("description", array(
			'label'=>array('text'=>__('Description'),'class'=>'control-label'),
	));
	echo $this->Form->input("opening_balance", array(
			'label'=>array('text'=>__('Opening Balance'),'class'=>'control-label'),
			'required',			//Set HTML5 required attribure
	));
	?>
	<div class="form-actions">
	  <button type="submit" class="btn btn-primary">Add Bucket</button>
	</div>
</fieldset>
<?php echo $this->Form->end();?>
