<?php 
$this->assign('datatables', '1');
$this->AssetCompress->addScript(array('Buckets/index.js'),'buckets_index');
// $html->script(array('buckets/index'),array('inline'=>false));
// $html->css(array('buckets/index'),NULL,array('inline'=>false));
?>
<table id="listTable" class="table table-striped table-bordered table-hover">
	<thead></thead>
	<tbody></tbody>
	<tfoot>
		<tr>
		</tr>
	</tfoot>
<!-- 	<tfoot> -->
<!-- 		<tr> -->
<!-- 			<th>Totals</th> -->
<!-- 			<th id="impMetricsTotal"></th> -->
<!-- 			<th id="requestsTotal"></th> -->
<!-- 			<th id="impressionsTotal"></th> -->
<!-- 			<th id="revenueTotal"></th> -->
<!-- 			<th id="cpmTotal"></th> -->
<!-- 			<th></th> -->
<!-- 		</tr> -->
<!-- 	</tfoot> -->
</table>
