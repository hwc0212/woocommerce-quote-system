<?php
/**
 * Admin functionality class
 *
 * @package WooCommerce Quote System
 * @version 1.5.2
 */

if (!defined('ABSPATH')) {
    exit;
}

class WCQS_Admin {
    
    /**
     * Plugin settings
     *
     * @var array
     */
    private $settings;
    
    /**
     * Settings fields configuration
     *
     * @var array
     */
    private $settings_fields;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->settings = WCQS_Main::get_settings();
        $this->init_settings_fields();
        
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_filter('plugin_action_links_' . WCQS_PLUGIN_BASENAME, array($this, 'add_plugin_action_links'));
        
        // Clear cache when settings are updated
        add_action('update_option_wcqs_settings', array($this, 'clear_settings_cache'));
    }
    
    /**
     * Initialize settings fields configuration
     */
    private function init_settings_fields() {
        $this->settings_fields = array(
            'enable_plugin' => array(
                'type' => 'checkbox',
                'title' => __('Enable Plugin', 'woocommerce-quote-system'),
                'label' => __('Enable WooCommerce Quote System functionality', 'woocommerce-quote-system'),
                'description' => __('Master switch to enable/disable all plugin features', 'woocommerce-quote-system'),
                'default' => 'yes'
            ),
            'hide_price' => array(
                'type' => 'checkbox',
                'title' => __('Hide Price', 'woocommerce-quote-system'),
                'label' => __('Hide product price display', 'woocommerce-quote-system'),
                'description' => __('Remove all price displays from products', 'woocommerce-quote-system'),
                'default' => 'yes'
            ),
            'hide_add_to_cart' => array(
                'type' => 'checkbox',
                'title' => __('Hide Add to Cart Button', 'woocommerce-quote-system'),
                'label' => __('Hide add to cart button and replace with quote button', 'woocommerce-quote-system'),
                'description' => __('Replace shopping cart functionality with quote requests', 'woocommerce-quote-system'),
                'default' => 'yes'
            ),
            'hide_shopping_elements' => array(
                'type' => 'checkbox',
                'title' => __('Hide Shopping Elements', 'woocommerce-quote-system'),
                'label' => __('Hide cart, checkout and other shopping elements', 'woocommerce-quote-system'),
                'description' => __('Remove all B2C shopping features like cart, checkout, payments, shipping', 'woocommerce-quote-system'),
                'default' => 'yes'
            ),
            'enable_quote_modal' => array(
                'type' => 'checkbox',
                'title' => __('Enable Quote Modal', 'woocommerce-quote-system'),
                'label' => __('Enable quote modal popup with form', 'woocommerce-quote-system'),
                'description' => __('Show a popup modal with contact form when quote button is clicked', 'woocommerce-quote-system'),
                'default' => 'yes'
            ),
            'contact_form_shortcode' => array(
                'type' => 'text',
                'title' => __('Form Shortcode', 'woocommerce-quote-system'),
                'description' => __('Enter any form plugin shortcode, e.g.: [contact-form-7 id="123"] or [wpforms id="456"]', 'woocommerce-quote-system'),
                'default' => '',
                'class' => 'large-text'
            ),
            'enable_whatsapp' => array(
                'type' => 'checkbox',
                'title' => __('Enable WhatsApp', 'woocommerce-quote-system'),
                'label' => __('Enable WhatsApp consultation button', 'woocommerce-quote-system'),
                'description' => __('Add WhatsApp button for direct customer communication', 'woocommerce-quote-system'),
                'default' => 'no'
            ),
            'whatsapp_number' => array(
                'type' => 'text',
                'title' => __('WhatsApp Number', 'woocommerce-quote-system'),
                'description' => __('Enter WhatsApp number with country code, e.g.: 8613800138000', 'woocommerce-quote-system'),
                'default' => '',
                'sanitize_callback' => array($this, 'sanitize_phone_number')
            ),
            'quote_button_text' => array(
                'type' => 'text',
                'title' => __('Quote Button Text', 'woocommerce-quote-system'),
                'description' => __('Text displayed on the quote button', 'woocommerce-quote-system'),
                'default' => __('Get Quote', 'woocommerce-quote-system')
            ),
            'whatsapp_button_text' => array(
                'type' => 'text',
                'title' => __('WhatsApp Button Text', 'woocommerce-quote-system'),
                'description' => __('Text displayed on the WhatsApp button', 'woocommerce-quote-system'),
                'default' => __('WhatsApp', 'woocommerce-quote-system')
            ),
            'modal_title' => array(
                'type' => 'text',
                'title' => __('Modal Title', 'woocommerce-quote-system'),
                'description' => __('Title displayed at the top of the quote modal', 'woocommerce-quote-system'),
                'default' => __('Product Quote', 'woocommerce-quote-system')
            ),
            'modal_subtitle' => array(
                'type' => 'text',
                'title' => __('Modal Subtitle', 'woocommerce-quote-system'),
                'description' => __('Optional subtitle displayed below the title (leave empty to hide)', 'woocommerce-quote-system'),
                'default' => '',
                'class' => 'large-text'
            )
        );
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('WooCommerce Quote System Settings', 'woocommerce-quote-system'),
            __('WC Quote System', 'woocommerce-quote-system'),
            'manage_options',
            'wcqs-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            'wcqs_settings_group', 
            'wcqs_settings',
            array(
                'sanitize_callback' => array($this, 'sanitize_settings')
            )
        );
        
        add_settings_section(
            'wcqs_general_section',
            __('General Settings', 'woocommerce-quote-system'),
            array($this, 'settings_section_callback'),
            'wcqs-settings'
        );
        
        // Add settings fields dynamically
        foreach ($this->settings_fields as $field_id => $field_config) {
            add_settings_field(
                $field_id,
                $field_config['title'],
                array($this, 'render_field'),
                'wcqs-settings',
                'wcqs_general_section',
                array('field_id' => $field_id, 'config' => $field_config)
            );
        }
    }
    
    /**
     * Settings section callback
     */
    public function settings_section_callback() {
        echo '<p>' . esc_html__('Configure your WooCommerce Quote System settings below.', 'woocommerce-quote-system') . '</p>';
    }
    
    /**
     * Render settings field
     *
     * @param array $args Field arguments
     */
    public function render_field($args) {
        $field_id = $args['field_id'];
        $config = $args['config'];
        $value = isset($this->settings[$field_id]) ? $this->settings[$field_id] : $config['default'];
        
        switch ($config['type']) {
            case 'checkbox':
                $this->render_checkbox_field($field_id, $value, $config);
                break;
            case 'text':
                $this->render_text_field($field_id, $value, $config);
                break;
            case 'textarea':
                $this->render_textarea_field($field_id, $value, $config);
                break;
            case 'select':
                $this->render_select_field($field_id, $value, $config);
                break;
        }
    }
    
    /**
     * Render checkbox field
     *
     * @param string $field_id Field ID
     * @param string $value Current value
     * @param array $config Field configuration
     */
    private function render_checkbox_field($field_id, $value, $config) {
        $checked = checked($value, 'yes', false);
        echo '<label>';
        echo '<input type="checkbox" name="wcqs_settings[' . esc_attr($field_id) . ']" value="yes" ' . $checked . '>';
        echo ' ' . esc_html($config['label']);
        echo '</label>';
        
        if (!empty($config['description'])) {
            echo '<p class="description">' . esc_html($config['description']) . '</p>';
        }
    }
    
    /**
     * Render text field
     *
     * @param string $field_id Field ID
     * @param string $value Current value
     * @param array $config Field configuration
     */
    private function render_text_field($field_id, $value, $config) {
        $class = isset($config['class']) ? $config['class'] : 'regular-text';
        echo '<input type="text" name="wcqs_settings[' . esc_attr($field_id) . ']" value="' . esc_attr($value) . '" class="' . esc_attr($class) . '">';
        
        if (!empty($config['description'])) {
            echo '<p class="description">' . esc_html($config['description']) . '</p>';
        }
    }
    
    /**
     * Render textarea field
     *
     * @param string $field_id Field ID
     * @param string $value Current value
     * @param array $config Field configuration
     */
    private function render_textarea_field($field_id, $value, $config) {
        $rows = isset($config['rows']) ? $config['rows'] : 4;
        echo '<textarea name="wcqs_settings[' . esc_attr($field_id) . ']" rows="' . esc_attr($rows) . '" class="large-text">' . esc_textarea($value) . '</textarea>';
        
        if (!empty($config['description'])) {
            echo '<p class="description">' . esc_html($config['description']) . '</p>';
        }
    }
    
    /**
     * Render select field
     *
     * @param string $field_id Field ID
     * @param string $value Current value
     * @param array $config Field configuration
     */
    private function render_select_field($field_id, $value, $config) {
        echo '<select name="wcqs_settings[' . esc_attr($field_id) . ']">';
        foreach ($config['options'] as $option_value => $option_label) {
            $selected = selected($value, $option_value, false);
            echo '<option value="' . esc_attr($option_value) . '" ' . $selected . '>' . esc_html($option_label) . '</option>';
        }
        echo '</select>';
        
        if (!empty($config['description'])) {
            echo '<p class="description">' . esc_html($config['description']) . '</p>';
        }
    }
    
    /**
     * Sanitize settings
     *
     * @param array $input Raw input data
     * @return array Sanitized data
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        
        foreach ($this->settings_fields as $field_id => $config) {
            if (isset($input[$field_id])) {
                switch ($config['type']) {
                    case 'checkbox':
                        $sanitized[$field_id] = $input[$field_id] === 'yes' ? 'yes' : 'no';
                        break;
                    case 'text':
                        if (isset($config['sanitize_callback']) && is_callable($config['sanitize_callback'])) {
                            $sanitized[$field_id] = call_user_func($config['sanitize_callback'], $input[$field_id]);
                        } else {
                            $sanitized[$field_id] = sanitize_text_field($input[$field_id]);
                        }
                        break;
                    case 'textarea':
                        $sanitized[$field_id] = sanitize_textarea_field($input[$field_id]);
                        break;
                    case 'select':
                        $sanitized[$field_id] = sanitize_text_field($input[$field_id]);
                        break;
                    default:
                        $sanitized[$field_id] = sanitize_text_field($input[$field_id]);
                }
            } else {
                // Handle unchecked checkboxes
                if ($config['type'] === 'checkbox') {
                    $sanitized[$field_id] = 'no';
                }
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Sanitize phone number
     *
     * @param string $phone_number Raw phone number
     * @return string Sanitized phone number
     */
    public function sanitize_phone_number($phone_number) {
        // Remove all non-numeric characters
        return preg_replace('/[^0-9]/', '', $phone_number);
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Show success message if settings were saved
        if (isset($_GET['settings-updated'])) {
            add_settings_error('wcqs_messages', 'wcqs_message', __('Settings Saved', 'woocommerce-quote-system'), 'updated');
        }
        
        // Show error messages
        settings_errors('wcqs_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="wcqs-admin-header">
                <p><?php esc_html_e('Transform your WooCommerce store into a professional quote request system. Perfect for B2B businesses, custom manufacturers, and service providers.', 'woocommerce-quote-system'); ?></p>
            </div>
            
            <form action="options.php" method="post">
                <?php
                settings_fields('wcqs_settings_group');
                do_settings_sections('wcqs-settings');
                submit_button(__('Save Settings', 'woocommerce-quote-system'));
                ?>
            </form>
            
            <div class="wcqs-admin-sidebar">
                <div class="wcqs-admin-box">
                    <h3><?php esc_html_e('Quick Setup Guide', 'woocommerce-quote-system'); ?></h3>
                    <ol>
                        <li><?php esc_html_e('Enable the plugin using the master switch above', 'woocommerce-quote-system'); ?></li>
                        <li><?php esc_html_e('Create a contact form using any form plugin', 'woocommerce-quote-system'); ?></li>
                        <li><?php esc_html_e('Copy the form shortcode and paste it in the "Form Shortcode" field', 'woocommerce-quote-system'); ?></li>
                        <li><?php esc_html_e('Customize button texts and modal titles as needed', 'woocommerce-quote-system'); ?></li>
                        <li><?php esc_html_e('Optionally enable WhatsApp integration with your business number', 'woocommerce-quote-system'); ?></li>
                    </ol>
                </div>
                
                <div class="wcqs-admin-box">
                    <h3><?php esc_html_e('Supported Form Plugins', 'woocommerce-quote-system'); ?></h3>
                    <ul>
                        <li>Contact Form 7</li>
                        <li>WPForms</li>
                        <li>Gravity Forms</li>
                        <li>Ninja Forms</li>
                        <li>Formidable Forms</li>
                        <li><?php esc_html_e('Any plugin that uses WordPress shortcodes', 'woocommerce-quote-system'); ?></li>
                    </ul>
                </div>
            </div>
            
            <style>
                .wcqs-admin-header {
                    background: #f1f1f1;
                    padding: 15px;
                    border-left: 4px solid #0073aa;
                    margin: 20px 0;
                }
                .wcqs-admin-sidebar {
                    margin-top: 30px;
                }
                .wcqs-admin-box {
                    background: #fff;
                    border: 1px solid #ccd0d4;
                    padding: 20px;
                    margin-bottom: 20px;
                }
                .wcqs-admin-box h3 {
                    margin-top: 0;
                    color: #23282d;
                }
                .wcqs-admin-box ol,
                .wcqs-admin-box ul {
                    padding-left: 20px;
                }
                .wcqs-admin-box li {
                    margin-bottom: 8px;
                }
            </style>
        </div>
        <?php
    }
    
    /**
     * Add plugin action links
     *
     * @param array $links Existing links
     * @return array Modified links
     */
    public function add_plugin_action_links($links) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=wcqs-settings') . '">' . __('Settings', 'woocommerce-quote-system') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
    
    /**
     * Clear settings cache when options are updated
     */
    public function clear_settings_cache() {
        WCQS_Main::clear_settings_cache();
    }
}