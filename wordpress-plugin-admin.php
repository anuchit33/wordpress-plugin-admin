<?php
/*
Plugin Name: WordPress Admin
Plugin URI: https://github.com/anuchit33/wordpress-plugin-admin
Description: wordpress-plugin-admin
Author: Anuchit Yai-in
Version: 0.0.1
Author URI: https://github.com/anuchit33/wordpress-plugin-admin
*/

class WordPressPluginAdmin {
    function __construct() {
        # Activation / Deactivation Hooks
        register_activation_hook(__FILE__, array($this, 'wp_activation'));
        register_deactivation_hook(__FILE__, array($this, 'wp_deactivation'));

        # add_action admin_menu
        add_action('admin_menu', array($this, 'wp_add_menu'));
    }


    public function init(){
        
    }

    function wp_activation(){
    }

    function wp_deactivation(){
    }

    function wp_add_menu(){


        $page_title = 'Contact US';
        $menu_title = 'Contact US';
        $capability = 'read'; // manage_options , read
        $menu_slug = 'wp-contact-list';
        $function = '';
        $icon_url = 'dashicons-email-alt';
        $position = '2.2.10';

        add_menu_page($page_title , $menu_title, $capability, $menu_slug ,$function , $icon_url, $position);

        // add sub menu 1
        $sub_parent_slug = $menu_slug;
        $sub_page_title =  $menu_title.'- รายชื่อผู้ติดต่อ';
        $sub_menu_title = 'รายชื่อผู้ติดต่อ';
        $sub_menu_slug = 'wp-contact-list';
        $sub_capability = 'read';

        add_submenu_page($sub_parent_slug, $sub_page_title, $sub_menu_title , $sub_capability, $sub_menu_slug , array(__CLASS__, 'wp_page_contact_list'));

        // add sub menu 2
        $sub_parent_slug = $menu_slug;
        $sub_page_title =  $menu_title.' - รายชื่อผู้รับเมล';
        $sub_menu_title = 'รายชื่อผู้รับเมล';
        $sub_menu_slug = 'wp-contact-email';
        $sub_capability = 'read';

        add_submenu_page($sub_parent_slug, $sub_page_title, $sub_menu_title , $sub_capability, $sub_menu_slug , array(__CLASS__, 'wp_page_contact_email'));

    }

    function wp_page_contact_list(){
        include( dirname(__FILE__) . '/templates/admin/contact-list.php' );
    }

    function wp_page_contact_email(){

        # add email
        if (isset($_POST['email'])) {
            if (!isset($_POST['add_email']) || !wp_verify_nonce($_POST['add_email'], 'post_add_email')) {
               echo 'Sorry, your nonce did not verify.';
               exit();
            }
            global $wpdb;
            $tablename = $wpdb->prefix . 'contact_email';
            $wpdb->insert($tablename, array(
                'email' => sanitize_text_field($_POST['email']),
                'name' => sanitize_text_field($_POST['email'])
                    )
            );
        }

        # delete email
        if (isset($_GET['action']) && $_GET['action'] == 'delete') {
            if (!isset($_GET['csrf']) || !wp_verify_nonce($_GET['csrf'], 'action_delete')) {
                echo 'Sorry, your nonce did not verify.';
                exit();
             }
             
            global $wpdb;
            $tablename = $wpdb->prefix . 'contact_email';
            $wpdb->delete($tablename, array('id' => intval($_GET['id'])));
        }
        
        include( dirname(__FILE__) . '/templates/admin/contact-email.php' );
    }

}

new WordPressPluginAdmin();