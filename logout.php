<?php

// Start session and clear content of it.
session_start();
session_destroy();

// Redirect user as guest into homepage.
header('Location: index.php');
die();
