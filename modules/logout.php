<?php
if(!isLoggedIn()) { redirect(); }

logOutUser();

redirect();