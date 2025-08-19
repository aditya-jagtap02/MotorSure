<?php
session_start();
session_destroy();
setcookie('email', '', time() - 3600, "/", "", true, true);
header("Location: signin.html");
exit();
?> 