<?php
echo $this->Form->input("user_id", array(
    'type' => 'hidden',
    'value' => $this->Session->read('Auth.User.id')
));
?>
