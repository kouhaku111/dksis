<?php
session_start();
session_unset($_SESSION['client_id']);
header('location:index.php');
?>
