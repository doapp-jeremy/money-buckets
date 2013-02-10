<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
$this->AssetCompress->autoInclude = false;
?>
<!DOCTYPE html>
<?php echo $this->Facebook->html(); ?>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	
  <?php if (Configure::read('debug') >= 1): ?>
  	<link rel="stylesheet" href="/css/cake.debug.css">
  <?php endif; ?>
  
  <? // TODO: remove once asset compress is working ?>
  <link rel="stylesheet" href="/css/common/common.css">
  <link rel="stylesheet" href="/css/common/bootstrap.css">
  
  
	<?php
	  if (false):
		echo $this->Html->meta('icon');

		echo $this->Html->css('cake.generic');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
		endif;
	?>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<?php if ($this->fetch('datatables')):?> 
	<link rel="stylesheet" type="text/css" href="//ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.1/css/jquery.dataTables.css">
	<?php endif;?>
	<?php if($this->fetch('jqueryui')):?>
	<link rel="stylesheet" type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/themes/ui-darkness/jquery-ui.css">
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js"></script>
	<?php endif;?>
	<?php 
		echo $this->AssetCompress->css('common.css');
		echo $this->fetch('viewIncludedBuildCss');
		echo $this->AssetCompress->includeCss();
	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<h1>header</h1>
		</div>
		<div id="content">
		  <?php echo $this->Facebook->logout(array('redirect' => array('controller' => 'users', 'action' => 'logout'))); ?>
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->element('alerts');?>

			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
		footer
		</div>
	</div>
	<?php //echo $this->element('sql_dump'); ?>
	<?php 
		if ($this->fetch('datatables')) 
		{
			echo '<script src="//ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.3/jquery.dataTables.min.js"></script>';
			echo $this->AssetCompress->script('custom_datatables');
		}
		
		if($this->fetch('googlejsapi')) echo '<script type="text/javascript" src="https://www.google.com/jsapi"></script>';
		
		//echo $this->AssetCompress->script('common');

		echo $this->fetch('viewIncludedBuildScripts');
		echo $this->AssetCompress->includeJs();
		echo $this->fetch('script');
		echo $this->Js->writeBuffer(); // Any Buffered Scripts
	?>	
  <script src="/js/<?php echo $controller; ?>/<?php echo $action; ?>.js"></script>
  <script src="/js/common/custom_datatables.js"></script>
  <script src="/js/common/plugins.js"></script>
  <script src="/js/jquery-plugins/bootstrap.js"></script>
  <script src="/js/jquery-plugins/spin.js"></script>
</body>
<?php echo $this->Facebook->init(); ?>
</html>
