<?php
$this->assign('datatables', '1');
$this->AssetCompress->addScript(array('Accounts/home.js'),'accounts_home');
?>

<ul class="nav nav-tabs" id="myTab">
  <?php foreach ($buckets as $bucket): ?>
    <li><a href="#<?= $bucket['Bucket']['name']; ?>"><?= $bucket['Bucket']['name'] . ' (' . $bucket['Bucket']['available_balance'] . ')' ?></a></li>
  <?php endforeach; ?>
</ul>
 
<div class="tab-content">
  <?php foreach ($buckets as $bucket): ?>
  <div class="tab-pane" id="<?= $bucket['Bucket']['name']; ?>"><?= $bucket['Bucket']['available_balance']; ?></div>
  <?php endforeach; ?>
</div>

<table id="listTable" class="table table-striped table-bordered table-hover">
	<thead></thead>
	<tbody></tbody>
	<tfoot>
		<tr>
		</tr>
	</tfoot>
</table>

