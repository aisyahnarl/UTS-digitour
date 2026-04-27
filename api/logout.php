<?php
setcookie('user_id',  '', time()-3600, '/', '', true, false);
setcookie('username', '', time()-3600, '/', '', true, false);
setcookie('userrole', '', time()-3600, '/', '', true, false);
header("Location: /login.php");
exit;
?>