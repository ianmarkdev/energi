<?php

$today = date('Y-m-d');

$SQL = new mysqli("localhost", "region", "%km2d4EpzM*llKz6", "region");

$reset_stmt = $SQL->prepare("UPDATE bd_auswahl SET daily_used = 0, last_reset = ? WHERE last_reset IS NULL OR last_reset < ?");
$reset_stmt->bind_param("ss", $today, $today);
$reset_stmt->execute();

?>