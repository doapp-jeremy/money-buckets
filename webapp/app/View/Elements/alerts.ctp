<div class="alert alert-success ajax-alert" id="successMsg" style="display: none"><button class="close" data-dismiss="alert">x</button> <span></span></div>
<div class="alert alert-error ajax-alert" id="errorMsg" style="display: none"><button class="close" data-dismiss="alert">x</button> <span></span></div>
<div class="alert alert-info ajax-alert" id="infoMsg" style="display: none"><button class="close" data-dismiss="alert">x</button> <span></span></div>
<?php 
echo $this->Session->flash();
echo $this->Session->flash('auth');
?>