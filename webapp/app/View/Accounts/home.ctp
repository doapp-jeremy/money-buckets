<?php
$this->assign('datatables', '1');
$this->AssetCompress->addScript(array('Accounts/home.js'),'accounts_home');
?>

<script>
var bucketIds = <?= json_encode($bucketIds); ?>
</script>

<ul class="nav nav-tabs" id="myTab">
  <li><a data-toggle="tab" href="#addTransaction">Add Transaction</a></li>
  <?php foreach ($buckets as $bucket): ?>
    <li><a data-toggle="tab" href="#<?= $bucket['Bucket']['id']; ?>"><?= $bucket['Bucket']['name'] . ' (' . $bucket['Bucket']['available_balance'] . ')' ?></a></li>
  <?php endforeach; ?>
</ul>
 
<div class="tab-content">
  <div class="tab-pane" id="addTransaction">
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
  	  //echo $this->Form->input("TransactionEntry.{$i}.label", array('label'=>array('text'=>__('Label'),'class'=>'control-label'),'div'=>array('class'=>'control-group','style'=>'display: block; margin-left: 15px;')));
  	  //echo $this->Form->input("TransactionEntry.{$i}.notes", array('label'=>array('text'=>__('Notes'),'class'=>'control-label'),'div'=>array('class'=>'control-group','style'=>'display: block; margin-left: 15px;')));
  	  $i++;
  	}
    echo $this->Form->input("notes", array(
        'label'=>array('text'=>__('Notes'),'class'=>'control-label'),
        'div'=>array('class'=>'control-group','style'=>'display: block; margin-left: 15px;')
    ));
  	?>
  	<div class="form-actions">
  	  <button type="submit" class="btn btn-primary">Add Transaction</button>
  	</div>
  </fieldset>
  <?php echo $this->Form->end();?>
  </div>
  <?php foreach ($buckets as $bucket): ?>
  <div class="tab-pane" id="<?= $bucket['Bucket']['id']; ?>">
    <table id="bucketTable<?= $bucket['Bucket']['id']; ?>" class="table table-striped table-bordered table-hover">
    	<thead></thead>
    	<tbody></tbody>
    	<tfoot>
    		<tr>
    		</tr>
    	</tfoot>
    </table>
  </div>
  <?php endforeach; ?>
</div>
<?php $bankAccountIds = array_keys($bankAccountList); ?>
<div>
  <h1><?php echo $bankAccountList[$bankAccountIds[0]]; ?></h1>
</div>

<table id="listTable" class="table table-striped table-bordered table-hover">
	<thead></thead>
	<tbody></tbody>
	<tfoot>
		<tr>
		</tr>
	</tfoot>
</table>

