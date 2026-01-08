<?php
/**
 * Frontend functionality class
 *
 * @package WooCommerce Quote System
 * @version 1.5.2
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
        $this->settings = WCQS_Main::get_settings();
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
            $this->hide_price_hooks();
        }
        
        // Hide add to cart buttons and replace with quote buttons
        if (!empty($this->settings['hide_add_to_cart']) && $this->settings['hide_add_to_cart'] === 'yes') {
            $this->hide_cart_hooks();
        }
        
        // Hide other shopping related elements
        if (!empty($this->settings['hide_shopping_elements']) && $this->settings['hide_shopping_elements'] === 'yes') {
            $this->hide_shopping_hooks();
        }
        
        // Add modal HTML
        if (!empty($this->settings['enable_quote_modal']) && $this->settings['enable_quote_modal'] === 'yes') {
            add_action('wp_footer', array($this, 'add_quote_modal'));
        }
    }
    
    /**
     * Initialize price hiding hooks
     */
    private function hide_price_hooks() {
        add_filter('woocommerce_get_price_html', array($this, 'hide_price'), 10, 2);
        add_filter('woocommerce_variable_sale_price_html', array($this, 'hide_price'), 10, 2);
        add_filter('woocommerce_variable_price_html', array($this, 'hide_price'), 10, 2);
        add_filter('woocommerce_grouped_price_html', array($this, 'hide_price'), 10, 2);
        add_filter('woocommerce_subscription_price_string', array($this, 'hide_price'), 10, 2);
        
        // Hide price in structured data
        add_filter('woocommerce_structured_data_product_offer', array($this, 'remove_price_structured_data'), 10, 2);
    }
    
    /**
     * Initialize cart hiding hooks
     */
    private function hide_cart_hooks() {
        // Single product page
        add_action('woocommerce_single_product_summary', array($this, 'remove_add_to_cart_button'), 1);
        add_action('woocommerce_single_product_summary', array($this, 'add_quote_buttons'), 30);
        
        // Product listing pages
        add_filter('woocommerce_loop_add_to_cart_link', array($this, 'hide_loop_add_to_cart_button'), 10, 2);
        
        // Remove quantity selectors
        add_filter('woocommerce_is_sold_individually', '__return_true');
        
        // Hide variations add to cart
        remove_action('woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20);
    }
    
    /**
     * Initialize shopping elements hiding hooks
     */
    private function hide_shopping_hooks() {
        // Remove cart and checkout functionality
        add_action('wp_head', array($this, 'hide_shopping_elements'));
        
        // Disable cart functionality
        add_filter('woocommerce_is_purchasable', '__return_false');
        
        // Remove cart widget
        add_action('widgets_init', array($this, 'unregister_cart_widget'));
        
        // Hide cart menu items
        add_filter('wp_nav_menu_items', array($this, 'hide_cart_menu_items'), 10, 2);
        
        // Redirect cart and checkout pages
        add_action('template_redirect', array($this, 'redirect_cart_checkout'));
        
        // Remove WooCommerce notices related to cart
        add_filter('wc_add_to_cart_message_html', '__return_empty_string');
        
        // Hide stock status and inventory
        add_filter('woocommerce_product_is_in_stock', '__return_true');
        add_filter('woocommerce_product_backorders_allowed', '__return_false');
        add_filter('woocommerce_product_stock_status_options', array($this, 'remove_stock_options'));
        
        // Hide shipping and tax related elements
        add_filter('woocommerce_product_needs_shipping', '__return_false');
        add_filter('woocommerce_product_is_taxable', '__return_false');
        
        // Remove payment gateways
        add_filter('woocommerce_available_payment_gateways', '__return_empty_array');
        
        // Hide reviews if they contain purchase verification
        add_filter('woocommerce_product_review_comment_form_args', array($this, 'modify_review_form'));
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
     * Remove price from structured data
     *
     * @param array $markup Structured data markup
     * @param WC_Product $product Product object
     * @return array Modified markup
     */
    public function remove_price_structured_data($markup, $product) {
        if (isset($markup['price'])) {
            unset($markup['price']);
        }
        if (isset($markup['priceValidUntil'])) {
            unset($markup['priceValidUntil']);
        }
        return $markup;
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
        
        if (!$product) {
            return;
        }
        
        $quote_text = $this->settings['quote_button_text'];
        $whatsapp_text = $this->settings['whatsapp_button_text'];
        
        echo '<div class="wcqs-quote-buttons">';
        
        // Show quote button if quote modal is enabled
        if (!empty($this->settings['enable_quote_modal']) && $this->settings['enable_quote_modal'] === 'yes') {
            echo '<button type="button" class="wcqs-quote-btn" data-product-id="' . esc_attr($product->get_id()) . '">';
            echo '<span class="wcqs-btn-icon"></span>';
            echo esc_html($quote_text);
            echo '</button>';
        }
        
        // Show WhatsApp button if enabled and number is set
        if (!empty($this->settings['enable_whatsapp']) && 
            $this->settings['enable_whatsapp'] === 'yes' && 
            !empty($this->settings['whatsapp_number'])) {
            $whatsapp_url = $this->get_whatsapp_url($product);
            echo '<a href="' . esc_url($whatsapp_url) . '" target="_blank" rel="noopener noreferrer" class="wcqs-whatsapp-btn">';
            echo '<span class="wcqs-btn-icon"></span>';
            echo esc_html($whatsapp_text);
            echo '</a>';
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
        ?>
        <style id="wcqs-hide-elements">
            /* Hide cart and checkout elements */
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
            .wc-proceed-to-checkout,
            .woocommerce-cart-form__cart-item .product-remove,
            .woocommerce-shipping-calculator,
            .woocommerce-cart-form .coupon,
            .cart-collaterals,
            .woocommerce-cart-form .actions,
            .woocommerce-checkout-review-order,
            .woocommerce-checkout-payment,
            .woocommerce-billing-fields,
            .woocommerce-shipping-fields,
            .woocommerce-additional-fields,
            
            /* Hide inventory and stock elements */
            .stock,
            .woocommerce-product-attributes-item--stock,
            .woocommerce-product-attributes-item--weight,
            .woocommerce-product-attributes-item--dimensions,
            
            /* Hide shipping and tax related */
            .woocommerce-shipping-methods,
            .woocommerce-shipping-destination,
            .woocommerce-tax-toggle,
            
            /* Hide payment related */
            .woocommerce-payment-methods,
            .payment_methods,
            .wc_payment_methods,
            
            /* Hide purchase related notices */
            .woocommerce-message[class*="add-to-cart"],
            .wc-forward,
            
            /* Hide related cart elements in widgets */
            .widget_shopping_cart_content,
            .woocommerce-mini-cart__buttons,
            
            /* Hide account order related elements */
            .woocommerce-orders-table,
            .woocommerce-order-downloads,
            
            /* Hide product comparison and wishlist if present */
            .compare-button,
            .yith-wcwl-add-to-wishlist,
            
            /* Hide quick view add to cart */
            .quick-view .single_add_to_cart_button {
                display: none !important;
            }
            
            /* Hide price elements more thoroughly */
            .price,
            .woocommerce-price-suffix,
            .woocommerce-variation-price,
            .price-range,
            .amount,
            ins .woocommerce-Price-amount,
            del .woocommerce-Price-amount,
            .woocommerce-Price-amount {
                display: none !important;
            }
        </style>
        <?php
    }
    
    /**
     * Unregister cart widget
     */
    public function unregister_cart_widget() {
        unregister_widget('WC_Widget_Cart');
    }
    
    /**
     * Hide cart menu items
     *
     * @param string $items Menu items HTML
     * @param object $args Menu arguments
     * @return string Modified menu items
     */
    public function hide_cart_menu_items($items, $args) {
        if (strpos($items, 'cart-contents') !== false || strpos($items, 'woocommerce-cart') !== false) {
            $items = preg_replace('/<li[^>]*cart[^>]*>.*?<\/li>/is', '', $items);
        }
        return $items;
    }
    
    /**
     * Redirect cart and checkout pages
     */
    public function redirect_cart_checkout() {
        if (is_cart() || is_checkout()) {
            wp_redirect(wc_get_page_permalink('shop'));
            exit;
        }
    }
    
    /**
     * Remove stock status options
     *
     * @param array $options Stock status options
     * @return array Empty array
     */
    public function remove_stock_options($options) {
        return array();
    }
    
    /**
     * Modify review form to remove purchase verification
     *
     * @param array $comment_form Comment form arguments
     * @return array Modified arguments
     */
    public function modify_review_form($comment_form) {
        if (isset($comment_form['comment_notes_after'])) {
            $comment_form['comment_notes_after'] = '';
        }
        return $comment_form;
    }
    
    /**
     * Add quote modal HTML to footer
     */
    public function add_quote_modal() {
        // Only show modal on single product pages
        if (!is_product()) {
            return;
        }
        
        $form_shortcode = $this->settings['contact_form_shortcode'];
        $modal_title = $this->settings['modal_title'];
        $modal_subtitle = $this->settings['modal_subtitle'];
        ?>
        <div id="wcqs-quote-modal" class="wcqs-modal" style="display: none;" role="dialog" aria-labelledby="wcqs-modal-title" aria-hidden="true">
            <div class="wcqs-modal-content" role="document">
                <button class="wcqs-close" type="button" aria-label="<?php esc_attr_e('Close modal', 'woocommerce-quote-system'); ?>">&times;</button>
                <div class="wcqs-modal-header">
                    <h3 id="wcqs-modal-title"><?php echo esc_html($modal_title); ?></h3>
                    <?php if (!empty($modal_subtitle)) : ?>
                        <p class="wcqs-modal-subtitle"><?php echo esc_html($modal_subtitle); ?></p>
                    <?php endif; ?>
                </div>
                <div class="wcqs-form-container">
                    <?php 
                    if (!empty($form_shortcode)) {
                        echo do_shortcode(wp_kses_post($form_shortcode));
                    } else {
                        echo '<p>' . esc_html__('Please configure form shortcode in plugin settings', 'woocommerce-quote-system') . '</p>';
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
        $number = preg_replace('/[^0-9]/', '', $this->settings['whatsapp_number']);
        $message = sprintf(
            /* translators: %1$s: Product name, %2$s: Product URL */
            __('Hello, I\'m interested in this product: %1$s - %2$s', 'woocommerce-quote-system'),
            $product->get_name(),
            get_permalink($product->get_id())
        );
        
        return 'https://wa.me/' . $number . '?text=' . urlencode($message);
    }
}