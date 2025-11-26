<?php
/* ========================================
   LIBRENEWS - LOGOUT
   ======================================== */

session_start();
session_unset();
session_destroy();

// Redirecionar para a home
header('Location: index.php');
exit();
?>
