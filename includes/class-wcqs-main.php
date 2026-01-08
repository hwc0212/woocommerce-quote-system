<?php
/**
 * Main functionality class
 *
 * @package WooCommerce Quote System
 * @version 1.5.2
 */

if (!defined('ABSPATH')) {
    exit;
}

class WCQS_Main {
    
    /**
     * Plugin settings cache
     *
     * @var array
     */
    private static $settings_cache = null;
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Performance optimization - preload settings
        add_action('plugins_loaded', array($this, 'preload_settings'), 5);
    }
    
    /**
     * Initialize plugin
     */
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
    
    /**
     * Preload settings for performance
     */
    public function preload_settings() {
        self::get_settings();
    }
    
    /**
     * Get plugin settings with caching
     *
     * @return array Plugin settings
     */
    public static function get_settings() {
        if (self::$settings_cache === null) {
            $defaults = array(
                'enable_plugin' => 'yes',
                'hide_price' => 'yes',
                'hide_add_to_cart' => 'yes',
                'hide_shopping_elements' => 'yes',
                'enable_quote_modal' => 'yes',
                'enable_whatsapp' => 'no',
                'contact_form_shortcode' => '',
                'whatsapp_number' => '',
                'quote_button_text' => __('Get Quote', 'woocommerce-quote-system'),
                'whatsapp_button_text' => __('WhatsApp', 'woocommerce-quote-system'),
                'modal_title' => __('Product Quote', 'woocommerce-quote-system'),
                'modal_subtitle' => ''
            );
            
            self::$settings_cache = wp_parse_args(get_option('wcqs_settings', array()), $defaults);
        }
        
        return self::$settings_cache;
    }
    
    /**
     * Clear settings cache
     */
    public static function clear_settings_cache() {
        self::$settings_cache = null;
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        // Only load on frontend and if plugin is enabled
        if (is_admin()) {
            return;
        }
        
        $settings = self::get_settings();
        if (empty($settings['enable_plugin']) || $settings['enable_plugin'] !== 'yes') {
            return;
        }
        
        // Load frontend styles and scripts
        wp_enqueue_style('wcqs-style', WCQS_PLUGIN_URL . 'assets/css/style.css', array(), WCQS_VERSION);
        wp_enqueue_script('wcqs-script', WCQS_PLUGIN_URL . 'assets/js/script.js', array('jquery'), WCQS_VERSION, true);
        
        // Localize script for AJAX and settings
        wp_localize_script('wcqs-script', 'wcqs_vars', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wcqs_nonce'),
            'is_mobile' => wp_is_mobile(),
            'modal_title' => $settings['modal_title'],
            'close_text' => __('Close', 'woocommerce-quote-system')
        ));
    }
}