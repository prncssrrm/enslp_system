<?php
session_start();
session_destroy(); // Dito natin buburahin ang "pagkakakilanlan" mo
header("Location: login.php"); // Ngayon, papayagan ka na niyang makita ang login.php
exit();
?>