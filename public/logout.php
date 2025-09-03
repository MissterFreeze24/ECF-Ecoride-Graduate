<?php
require_once __DIR__ . '/../src/db.php';
session_destroy();
header('Location: /login.php');
