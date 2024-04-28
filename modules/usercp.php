<?php
if(!isLoggedIn()) redirect(1,'login');

# redirect to my account
redirect(1, 'usercp/myaccount');