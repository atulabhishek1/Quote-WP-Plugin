<?php

if (!defined('ABSPATH')) {
    exit;
}

class ACP_Admin_Menu {

    public function __construct() {

        add_action('admin_menu', array($this,'menu'));

        add_action('admin_enqueue_scripts', array($this,'assets'));

    }

    public function assets(){

        wp_enqueue_style(
            'ac-admin',
            ACP_URL.'admin/assets/css/admin.css',
            array(),
            ACP_VERSION
        );

        wp_enqueue_script(
            'ac-admin',
            ACP_URL.'admin/assets/js/admin.js',
            array('jquery'),
            ACP_VERSION,
            true
        );

    }

    public function menu(){

        add_menu_page(

            'Adorable CRM',
            'Adorable CRM',
            'manage_options',
            'adorable-dashboard',
            array('ACP_Dashboard','dashboard'),
            'dashicons-admin-home',
            3

        );

        add_submenu_page(
            'adorable-dashboard',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'adorable-dashboard',
            array('ACP_Dashboard','dashboard')
        );

        add_submenu_page(
            'adorable-dashboard',
            'Clients',
            'Clients',
            'manage_options',
            'adorable-clients',
            array('ACP_Dashboard','clients')
        );

        add_submenu_page(
            'adorable-dashboard',
            'Projects',
            'Projects',
            'manage_options',
            'adorable-projects',
            array('ACP_Dashboard','projects')
        );

        add_submenu_page(
            'adorable-dashboard',
            'Quotes',
            'Quotes',
            'manage_options',
            'adorable-quotes',
            array('ACP_Dashboard','quotes')
        );

        add_submenu_page(
            'adorable-dashboard',
            'Settings',
            'Settings',
            'manage_options',
            'adorable-settings',
            array('ACP_Dashboard','settings')
        );

    }

}