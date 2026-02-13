<?php

require_once __DIR__ . '/../includes/common.php';

session_unset();
session_destroy();

redirect('../index.php');
?>
