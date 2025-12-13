<?php
/**
 * Main functionality class
 *
 * @package WooCommerce Quote System
 * @version 1.5.1
 */

if (!defined('ABSPATH')) {
    exit;
}

class WCQS_Main {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    public function init() {
        // Load text domain
        load_plugin_textdomain('woocommerce-quote-system', false, dirname(WCQS_PLUGIN_BASENAME) . '/languages');
        
        // Initialize frontend and backend functionality
        if (is_admin()) {
            new WCQS_Admin();
        } else {
            new WCQS_Frontend();
        }
    }
    
    public function enqueue_scripts() {
        // Load frontend styles and scripts
        wp_enqueue_style('wcqs-style', WCQS_PLUGIN_URL . 'assets/css/style.css', array(), WCQS_VERSION);
        wp_enqueue_script('wcqs-script', WCQS_PLUGIN_URL . 'assets/js/script.js', array('jquery'), WCQS_VERSION, true);
    }
}