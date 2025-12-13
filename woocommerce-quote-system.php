<?php
/**
 * Plugin Name: WooCommerce Quote System
 * Plugin URI: https://github.com/hwc0212/woocommerce-quote-system
 * Description: Transform WooCommerce into a simple quote request system by hiding shopping features and adding quote forms.
 * Version: 1.5.1
 * Author: huwencai.com
 * Author URI: https://huwencai.com
 * Text Domain: woocommerce-quote-system
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * WC requires at least: 5.0
 * WC tested up to: 8.5
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Network: false
 * GitHub Plugin URI: hwc0212/woocommerce-quote-system
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WCQS_VERSION', '1.5.1');
define('WCQS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WCQS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WCQS_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Check if WooCommerce is active and initialize plugin
 */
add_action('plugins_loaded', 'wcqs_check_woocommerce');

function wcqs_check_woocommerce() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'wcqs_woocommerce_missing_notice');
        return;
    }
    
    // Initialize plugin
    wcqs_init();
}

/**
 * Display admin notice when WooCommerce is not active
 */
function wcqs_woocommerce_missing_notice() {
    $message = sprintf(
        /* translators: %s: WooCommerce plugin name */
        __('WooCommerce Quote System requires %s plugin to work properly.', 'woocommerce-quote-system'),
        '<strong>WooCommerce</strong>'
    );
    
    echo '<div class="notice notice-error"><p>' . wp_kses_post($message) . '</p></div>';
}

/**
 * Initialize plugin classes
 */
function wcqs_init() {
    // Load plugin classes
    require_once WCQS_PLUGIN_PATH . 'includes/class-wcqs-main.php';
    require_once WCQS_PLUGIN_PATH . 'includes/class-wcqs-admin.php';
    require_once WCQS_PLUGIN_PATH . 'includes/class-wcqs-frontend.php';
    
    // Initialize main class
    new WCQS_Main();
}

/**
 * Plugin activation hook
 */
register_activation_hook(__FILE__, 'wcqs_activate');
function wcqs_activate() {
    // Create default settings
    $default_options = array(
        'enable_plugin' => 'yes',
        'hide_price' => 'yes',
        'hide_add_to_cart' => 'yes',
        'hide_shopping_elements' => 'yes',
        'enable_quote_modal' => 'yes',
        'enable_whatsapp' => 'no',
        'contact_form_shortcode' => '',
        'whatsapp_number' => '',
        'quote_button_text' => 'Get Quote',
        'whatsapp_button_text' => 'WhatsApp',
        'modal_title' => 'Product Quote',
        'modal_subtitle' => ''
    );
    
    // Only add options if they don't exist
    if (!get_option('wcqs_settings')) {
        add_option('wcqs_settings', $default_options);
    }
}

/**
 * Plugin deactivation hook
 */
register_deactivation_hook(__FILE__, 'wcqs_deactivate');
function wcqs_deactivate() {
    // Clean up if needed
    // Note: We don't delete settings on deactivation to preserve user configuration
}

/**
 * Plugin uninstall hook
 */
register_uninstall_hook(__FILE__, 'wcqs_uninstall');
function wcqs_uninstall() {
    // Clean up plugin data on uninstall
    delete_option('wcqs_settings');
}
