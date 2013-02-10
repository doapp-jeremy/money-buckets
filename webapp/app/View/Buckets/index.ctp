<?php 
$this->assign('datatables', '1');
$this->AssetCompress->addScript(array('Buckets/index.js'),'buckets_index');
?>
<table id="listTable" class="table table-striped table-bordered table-hover">
	<thead></thead>
	<tbody></tbody>
	<tfoot>
		<tr>
		</tr>
	</tfoot>
</table>
