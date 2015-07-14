<h3>Hi <?php echo $full_name; ?></h3>
<br>
Please use the following link to change your password
<br>
Key: <?php echo \Request::root(); ?>/cms/password_reset/<?php echo $reset_code; ?>