<?php 
$user = $this->Session->read('Auth.User');
$adminUser = $this->Session->read('Admin.User');
?>
<div class="navbar navbar-fixed-top navbar-inverse" id="topNav">
	<div class="navbar-inner">		
		<div class="container">
			<a class="brand" href="/accounts/home/">Money Buckets</a>
			
			<?php if(!empty($user['g_user_email'])):?>
			<ul id="mainNav" class="nav">											
				<li id="advertisingNav" class="dropdown">
				  <a data-toggle="dropdown" class="dropdown-toggle" href="#">Advertising <b class="caret"></b></a>
				  <ul class="dropdown-menu">				    
				  <?php if($isAdCreator):?>
				  <li><a href="/accounts/ads/">Ads</a></li>
				    
				    <li><a href="/Advertisers">Advertisers</a></li>
				  <?php endif;?>
				    
				    <li><a href="/Forecasts/">Forecasting</a></li>

				    <?php if($isAccountAdmin):?>
				    <li><a href="/accounts/">Accounts</a></li>
				  	<?php endif;?>				  
				 </ul>				
				</li>
				
				<?php if($isAdmin): ?>
				<li id="nationalNav" class="dropdown">
					<a data-toggle="dropdown" class="dropdown-toggle" href="#">National <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><?php echo $html->link('Ads', array('controller' => 'Accounts', 'action' => 'ads', 'national' => '1')); ?></li>
						<li><?php echo $html->link('Advertisers', array('controller' => 'Advertisers', 'action' => 'index', 'national' => '1')); ?></li>
						<li><?php echo $html->link('Accounts', array('controller' => 'Accounts', 'action' => 'national')); ?></li>
						<li><?php echo $html->link('Reporting', array('controller' => 'AdData', 'action' => 'national')); ?></li>
						<li><?php echo $html->link('Users', array('controller' => 'Users', 'action' => 'national')); ?></li>					
					</ul>
				</li>
				<?php endif;?>
				
				<li id="helpNav" class="dropdown">
					<a data-toggle="dropdown" class="dropdown-toggle" href="#">Help <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="/HelpVideos/">Help Videos</a></li>
						<li><a href="http://support.mobilelocalnews.com/index.php" target="_blank">Contact Support</a></li>
						<li><a target="_blank" href="/help/Adagogo101.pdf">Download Overview PDF</a></li>
						<li><a target="_blank" href="/help/Adagogo_How_To.pdf">Download How To PDF</a></li>											
					</ul>
				</li>
							
				<li><div class="globalSpinner" class="pull-right" style="width: 19px; height: 19px; padding: 10px 10px 11px; color: white;"></div></li>		
			</ul>
		
			<ul class="nav pull-right">
                      <li class="divider-vertical"></li>
                      <?php
                      	$email = trim($user['g_user_email']);
						$grav_url = "https://secure.gravatar.com/avatar/" . md5( strtolower( $email ) ) . "?s=40";
					  ?>		                      
                      <li class="dropdown">				 						
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $email?><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                          <li class="nav-header" style="text-transform: none; width: 200px;">
                          	  <div style="width: 40px; height: 40px; background-image: url('<?php echo $grav_url; ?>'); float: left; margin: 5px 10px 0 0;"></div>
                          	  <div><?php echo $user['g_user_fname']." ".$user['g_user_lname']?></div>
                          	  <div>
                          	  	<?php echo $email;?>
		                        <?php if($adminUser):?> 
		                        <div style="color: red">DoAppAdmin: <?php echo $adminUser['g_user_email']; ?></div>  
		                        <?php endif;?>     
		                          
		                      </div>                     
                          </li>                          
                          <li class="divider"></li>
                          <?php if($isAdmin):?>
                          <li><a href="#">Is Admin: yes</a></li>
                          <?php endif;?>
                          <li><a href="/Users/account">my account</a></li>
                          <li><a href="/users/logout">logout</a></li>                          
                        </ul>
                      </li>
            </ul>
            <?php elseif (false):?>
            <ul class="nav pull-right">
            	<li id="publisherNav"><a href="/users/login">login</a></li>
            </ul>
            <?php endif;?> 
		</div>
	</div>                      
</div>