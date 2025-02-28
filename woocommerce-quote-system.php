<?php
/**
 * Plugin Name: WooCommerce Quote System
 * Description: Add quote functionality to WooCommerce
 * Version: 2.0.7
 * Author: huwencai.com
 * Text Domain: wqs
 */

defined('ABSPATH') || exit;

// 定义插件常量
define('WQS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WQS_PLUGIN_URL', plugin_dir_url(__FILE__));

class WooCommerce_Quote_System {

    public function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action('plugins_loaded', array($this, 'check_dependencies'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
    }

    public function check_dependencies() {
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
        
        require_once WQS_PLUGIN_PATH . 'includes/admin-settings.php';
        require_once WQS_PLUGIN_PATH . 'includes/frontend-handler.php';
        
        new WQS_Admin_Settings();
        new WQS_Frontend_Handler();
    }

    public function admin_scripts($hook) {
        if ('woocommerce_page_wqs-settings' !== $hook) return;
        
        wp_enqueue_script(
            'wqs-admin',
            WQS_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            '1.0',
            true
        );
    }

    public function woocommerce_missing_notice() {
        echo '<div class="error"><p>';
        printf(__('WooCommerce Quote System requires WooCommerce to be installed and active.'));
        echo '</p></div>';
    }
}

new WooCommerce_Quote_System();