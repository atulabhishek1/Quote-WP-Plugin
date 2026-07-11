<?php

if (!defined('ABSPATH')) {
    exit;
}

class ACP_Dashboard {

    public static function dashboard(){

        include ACP_PATH.'admin/dashboard.php';

    }

    public static function clients(){

        echo "<h1>Clients</h1>";

    }

    public static function projects(){

        echo "<h1>Projects</h1>";

    }

    public static function quotes(){

        echo "<h1>Quotes</h1>";

    }

    public static function settings(){

        echo "<h1>Settings</h1>";

    }

}