<?php
/**
 * Frontend functionality class
 *
 * @package WooCommerce Quote System
 * @version 1.5.1
 */

if (!defined('ABSPATH')) {
    exit;
}

class WCQS_Frontend {
    
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
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Check if plugin is enabled
        if (empty($this->settings['enable_plugin']) || $this->settings['enable_plugin'] !== 'yes') {
            return;
        }
        
        // Hide prices
        if (!empty($this->settings['hide_price']) && $this->settings['hide_price'] === 'yes') {
            add_filter('woocommerce_get_price_html', array($this, 'hide_price'), 10, 2);
            add_filter('woocommerce_variable_sale_price_html', array($this, 'hide_price'), 10, 2);
            add_filter('woocommerce_variable_price_html', array($this, 'hide_price'), 10, 2);
        }
        
        // Hide add to cart buttons and replace with quote buttons
        if (!empty($this->settings['hide_add_to_cart']) && $this->settings['hide_add_to_cart'] === 'yes') {
            add_action('woocommerce_single_product_summary', array($this, 'remove_add_to_cart_button'), 1);
            add_action('woocommerce_single_product_summary', array($this, 'add_quote_buttons'), 30);
            
            // Product listing pages - hide add to cart buttons
            add_filter('woocommerce_loop_add_to_cart_link', array($this, 'hide_loop_add_to_cart_button'), 10, 2);
        }
        
        // Hide other shopping related elements
        if (!empty($this->settings['hide_shopping_elements']) && $this->settings['hide_shopping_elements'] === 'yes') {
            add_action('wp_head', array($this, 'hide_shopping_elements'));
        }
        
        // Add modal HTML
        if (!empty($this->settings['enable_quote_modal']) && $this->settings['enable_quote_modal'] === 'yes') {
            add_action('wp_footer', array($this, 'add_quote_modal'));
        }
    }
    
    /**
     * Hide product price
     *
     * @param string $price Product price HTML
     * @param WC_Product $product Product object
     * @return string Empty string to hide price
     */
    public function hide_price($price, $product) {
        return '';
    }
    
    /**
     * Remove add to cart button from single product page
     */
    public function remove_add_to_cart_button() {
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
    }
    
    /**
     * Add quote buttons to single product page
     */
    public function add_quote_buttons() {
        global $product;
        
        $quote_text = isset($this->settings['quote_button_text']) ? $this->settings['quote_button_text'] : 'Get Quote';
        $whatsapp_text = isset($this->settings['whatsapp_button_text']) ? $this->settings['whatsapp_button_text'] : 'WhatsApp';
        
        echo '<div class="wcqs-quote-buttons">';
        
        // Show quote button if quote modal is enabled
        if (!empty($this->settings['enable_quote_modal']) && $this->settings['enable_quote_modal'] === 'yes') {
            echo '<button type="button" class="wcqs-quote-btn">' . esc_html($quote_text) . '</button>';
        }
        
        // Show WhatsApp button if enabled and number is set
        if (!empty($this->settings['enable_whatsapp']) && 
            $this->settings['enable_whatsapp'] === 'yes' && 
            !empty($this->settings['whatsapp_number'])) {
            $whatsapp_url = $this->get_whatsapp_url($product);
            echo '<a href="' . esc_url($whatsapp_url) . '" target="_blank" rel="noopener" class="wcqs-whatsapp-btn">' . esc_html($whatsapp_text) . '</a>';
        }
        
        echo '</div>';
    }
    
    /**
     * Hide add to cart button on product loops
     *
     * @param string $button Button HTML
     * @param WC_Product $product Product object
     * @return string Empty string to hide button
     */
    public function hide_loop_add_to_cart_button($button, $product) {
        return '';
    }
    
    /**
     * Hide shopping related elements with CSS
     */
    public function hide_shopping_elements() {
        echo '<style>
            .woocommerce .cart, 
            .woocommerce-cart,
            .woocommerce-checkout,
            .woocommerce-account .woocommerce-MyAccount-navigation-link--orders,
            .woocommerce-account .woocommerce-MyAccount-navigation-link--downloads,
            .woocommerce .quantity,
            .single_variation_wrap .variations_button,
            .woocommerce-mini-cart,
            .widget_shopping_cart,
            .woocommerce-cart-form,
            .checkout-button,
            .wc-proceed-to-checkout {
                display: none !important;
            }
        </style>';
    }
    
    /**
     * Add quote modal HTML to footer
     */
    public function add_quote_modal() {
        // Only show modal on single product pages
        if (!is_product()) {
            return;
        }
        
        $form_shortcode = isset($this->settings['contact_form_shortcode']) ? $this->settings['contact_form_shortcode'] : '';
        $modal_title = isset($this->settings['modal_title']) ? $this->settings['modal_title'] : __('Product Quote', 'woocommerce-quote-system');
        $modal_subtitle = isset($this->settings['modal_subtitle']) ? $this->settings['modal_subtitle'] : '';
        ?>
        <div id="wcqs-quote-modal" class="wcqs-modal" style="display: none;">
            <div class="wcqs-modal-content">
                <span class="wcqs-close">&times;</span>
                <div class="wcqs-modal-header">
                    <h3><?php echo esc_html($modal_title); ?></h3>
                    <?php if (!empty($modal_subtitle)) : ?>
                        <p class="wcqs-modal-subtitle"><?php echo esc_html($modal_subtitle); ?></p>
                    <?php endif; ?>
                </div>
                <div class="wcqs-form-container">
                    <?php 
                    if (!empty($form_shortcode)) {
                        echo do_shortcode($form_shortcode);
                    } else {
                        echo '<p>' . __('Please configure form shortcode in plugin settings', 'woocommerce-quote-system') . '</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Generate WhatsApp URL with product information
     *
     * @param WC_Product $product Product object
     * @return string WhatsApp URL
     */
    private function get_whatsapp_url($product) {
        $number = $this->settings['whatsapp_number'];
        $message = sprintf(
            __('Hello, I\'m interested in this product: %s - %s', 'woocommerce-quote-system'),
            $product->get_name(),
            get_permalink($product->get_id())
        );
        
        return 'https://wa.me/' . $number . '?text=' . urlencode($message);
    }
}