<?php
session_start();
session_unset();
session_destroy();
header("location: ./views/index_view.php");
exit;
?>
