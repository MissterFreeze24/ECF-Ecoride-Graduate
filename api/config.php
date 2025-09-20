<?php
define('DB_HOST', getenv('MYSQL_HOST') ?: 'mysql');
define('DB_NAME', getenv('MYSQL_DATABASE') ?: 'ecoride');
define('DB_USER', getenv('MYSQL_USER') ?: 'ecoride_user');
define('DB_PASS', getenv('MYSQL_PASSWORD') ?: 'ecoride_pass');
define('AUTH_SECRET', getenv('AUTH_SECRET') ?: 'change_this_secret');
?>