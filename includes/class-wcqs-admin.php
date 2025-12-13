<?php
/**
 * Admin functionality class
 *
 * @package WooCommerce Quote System
 * @version 1.5.1
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
     * Constructor
     */
    public function __construct() {
        $this->settings = get_option('wcqs_settings', array());
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_filter('plugin_action_links_' . WCQS_PLUGIN_BASENAME, array($this, 'add_plugin_action_links'));
    }
    
    public function add_admin_menu() {
        add_options_page(
            __('WooCommerce Quote System Settings', 'woocommerce-quote-system'),
            __('WC Quote System', 'woocommerce-quote-system'),
            'manage_options',
            'wcqs-settings',
            array($this, 'settings_page')
        );
    }
    
    public function register_settings() {
        register_setting('wcqs_settings_group', 'wcqs_settings');
        
        add_settings_section(
            'wcqs_general_section',
            __('General Settings', 'woocommerce-quote-system'),
            null,
            'wcqs-settings'
        );
        
        add_settings_field(
            'enable_plugin',
            __('Enable Plugin', 'woocommerce-quote-system'),
            array($this, 'enable_plugin_callback'),
            'wcqs-settings',
            'wcqs_general_section'
        );
        
        add_settings_field(
            'hide_price',
            __('Hide Price', 'woocommerce-quote-system'),
            array($this, 'hide_price_callback'),
            'wcqs-settings',
            'wcqs_general_section'
        );
        
        add_settings_field(
            'hide_add_to_cart',
            __('Hide Add to Cart Button', 'woocommerce-quote-system'),
            array($this, 'hide_add_to_cart_callback'),
            'wcqs-settings',
            'wcqs_general_section'
        );
        
        add_settings_field(
            'hide_shopping_elements',
            __('Hide Shopping Elements', 'woocommerce-quote-system'),
            array($this, 'hide_shopping_elements_callback'),
            'wcqs-settings',
            'wcqs_general_section'
        );
        
        add_settings_field(
            'enable_quote_modal',
            __('Enable Quote Modal', 'woocommerce-quote-system'),
            array($this, 'enable_quote_modal_callback'),
            'wcqs-settings',
            'wcqs_general_section'
        );
        
        add_settings_field(
            'contact_form_shortcode',
            __('Form Shortcode', 'woocommerce-quote-system'),
            array($this, 'contact_form_shortcode_callback'),
            'wcqs-settings',
            'wcqs_general_section'
        );
        
        add_settings_field(
            'enable_whatsapp',
            __('Enable WhatsApp', 'woocommerce-quote-system'),
            array($this, 'enable_whatsapp_callback'),
            'wcqs-settings',
            'wcqs_general_section'
        );
        
        add_settings_field(
            'whatsapp_number',
            __('WhatsApp Number', 'woocommerce-quote-system'),
            array($this, 'whatsapp_number_callback'),
            'wcqs-settings',
            'wcqs_general_section'
        );
        
        add_settings_field(
            'quote_button_text',
            __('Quote Button Text', 'woocommerce-quote-system'),
            array($this, 'quote_button_text_callback'),
            'wcqs-settings',
            'wcqs_general_section'
        );
        
        add_settings_field(
            'whatsapp_button_text',
            __('WhatsApp Button Text', 'woocommerce-quote-system'),
            array($this, 'whatsapp_button_text_callback'),
            'wcqs-settings',
            'wcqs_general_section'
        );
        
        add_settings_field(
            'modal_title',
            __('Modal Title', 'woocommerce-quote-system'),
            array($this, 'modal_title_callback'),
            'wcqs-settings',
            'wcqs_general_section'
        );
        
        add_settings_field(
            'modal_subtitle',
            __('Modal Subtitle', 'woocommerce-quote-system'),
            array($this, 'modal_subtitle_callback'),
            'wcqs-settings',
            'wcqs_general_section'
        );
    }
    
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('wcqs_settings_group');
                do_settings_sections('wcqs-settings');
                submit_button(__('Save Settings', 'woocommerce-quote-system'));
                ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Render checkbox field
     *
     * @param string $field_name Field name
     * @param string $default_value Default value
     * @param string $label Label text
     * @param string $description Description text
     */
    private function render_checkbox($field_name, $default_value, $label, $description = '') {
        $value = isset($this->settings[$field_name]) ? $this->settings[$field_name] : $default_value;
        echo '<input type="checkbox" name="wcqs_settings[' . esc_attr($field_name) . ']" value="yes" ' . checked($value, 'yes', false) . '>';
        echo '<label>' . esc_html($label) . '</label>';
        if (!empty($description)) {
            echo '<p class="description">' . esc_html($description) . '</p>';
        }
    }
    
    /**
     * Render text field
     *
     * @param string $field_name Field name
     * @param string $default_value Default value
     * @param string $description Description text
     */
    private function render_text_field($field_name, $default_value, $description = '') {
        $value = isset($this->settings[$field_name]) ? $this->settings[$field_name] : $default_value;
        echo '<input type="text" name="wcqs_settings[' . esc_attr($field_name) . ']" value="' . esc_attr($value) . '" class="regular-text">';
        if (!empty($description)) {
            echo '<p class="description">' . esc_html($description) . '</p>';
        }
    }
    
    public function enable_plugin_callback() {
        $this->render_checkbox(
            'enable_plugin',
            'yes',
            __('Enable WooCommerce Quote System functionality', 'woocommerce-quote-system'),
            __('Master switch to enable/disable all plugin features', 'woocommerce-quote-system')
        );
    }
    
    public function hide_price_callback() {
        $this->render_checkbox('hide_price', 'yes', __('Hide product price display', 'woocommerce-quote-system'));
    }
    
    public function hide_add_to_cart_callback() {
        $this->render_checkbox('hide_add_to_cart', 'yes', __('Hide add to cart button and replace with quote button', 'woocommerce-quote-system'));
    }
    
    public function hide_shopping_elements_callback() {
        $this->render_checkbox('hide_shopping_elements', 'yes', __('Hide cart, checkout and other shopping elements', 'woocommerce-quote-system'));
    }
    
    public function enable_quote_modal_callback() {
        $this->render_checkbox('enable_quote_modal', 'yes', __('Enable quote modal popup with form', 'woocommerce-quote-system'));
    }
    
    public function contact_form_shortcode_callback() {
        $this->render_text_field(
            'contact_form_shortcode',
            '',
            __('Enter any form plugin shortcode, e.g.: [contact-form-7 id="123"] or [wpforms id="456"]', 'woocommerce-quote-system')
        );
    }
    
    public function enable_whatsapp_callback() {
        $this->render_checkbox('enable_whatsapp', 'no', __('Enable WhatsApp consultation button', 'woocommerce-quote-system'));
    }
    
    public function whatsapp_number_callback() {
        $this->render_text_field(
            'whatsapp_number',
            '',
            __('Enter WhatsApp number with country code, e.g.: 8613800138000', 'woocommerce-quote-system')
        );
    }
    
    public function quote_button_text_callback() {
        $this->render_text_field('quote_button_text', 'Get Quote');
    }
    
    public function whatsapp_button_text_callback() {
        $this->render_text_field('whatsapp_button_text', 'WhatsApp');
    }
    
    public function modal_title_callback() {
        $this->render_text_field(
            'modal_title',
            'Product Quote',
            __('Title displayed at the top of the quote modal', 'woocommerce-quote-system')
        );
    }
    
    public function modal_subtitle_callback() {
        $this->render_text_field(
            'modal_subtitle',
            '',
            __('Optional subtitle displayed below the title (leave empty to hide)', 'woocommerce-quote-system')
        );
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
}