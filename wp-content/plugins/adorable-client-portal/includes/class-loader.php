<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once ACP_PATH . 'admin/class-admin-menu.php';
require_once ACP_PATH . 'admin/class-dashboard.php';

new ACP_Admin_Menu();