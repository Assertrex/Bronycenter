<?php
// Include system initialization code
require('../application/partials/init.php');

// Destroy a session to log out a user
$session->destroy();

// Redirect user into the homepage after log out
header('Location: index.php');
die();
