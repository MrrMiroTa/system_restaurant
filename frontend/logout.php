<?php
session_start();
// Destroy all session data
session_unset();
session_destroy();
// Redirect to login page (index.php or login.php)
header('Location: index.php');
exit();
