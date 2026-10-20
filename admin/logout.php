<?php
require_once __DIR__ . '/_boot.php';
$_SESSION = []; session_destroy();
redirect(aurl('login.php'));
