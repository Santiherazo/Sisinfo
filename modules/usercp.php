<?php
if(!isLoggedIn()) redirect(1,'login');
redirect(1, 'usercp/myaccount');
?>