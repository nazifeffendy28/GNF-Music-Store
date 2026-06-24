<?php
/** admin/logout.php */
require_once __DIR__ . '/../config.php';
unset($_SESSION['admin']);
redirect('admin/login.php');
