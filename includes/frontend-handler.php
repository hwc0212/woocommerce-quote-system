<?php
class WQS_Frontend_Handler {

    private $options;

    public function __construct() {
        $this->options = get_option('wqs_options');
        $this->init_hooks();
    }

    private function init_hooks() {
        // 隐藏价格
        if (!empty($this->options['hide_prices'])) {
            add_filter('woocommerce_get_price_html', [$this, 'hide_price']);
        }

        // 隐藏购物车按钮
        if (!empty($this->options['hide_cart_button'])) {
            remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
        }

        // 添加报价按钮
        if (!empty($this->options['enable_quote_button']) && !empty($this->options['form_shortcode'])) {
            add_action('woocommerce_single_product_summary', [$this, 'add_quote_button'], 35);
            add_action('wp_footer', [$this, 'quote_modal']);
            add_action('wp_enqueue_scripts', [$this, 'register_assets']);
        }
        
        // 添加WhatsApp按钮
        if (!empty($this->options['enable_whatsapp']) && !empty($this->options['whatsapp_number'])) {
            add_action('woocommerce_single_product_summary', [$this, 'add_whatsapp_button'], 36);
        }
    }

    public function register_assets() {
        // 加载Dashicons字体
        wp_enqueue_style('dashicons');

        // 前端样式
        wp_enqueue_style(
            'wqs-frontend',
            WQS_PLUGIN_URL . 'assets/css/frontend.css',
            [],
            filemtime(WQS_PLUGIN_PATH . 'assets/css/frontend.css')
        );

        // 前端脚本
        wp_enqueue_script(
            'wqs-frontend',
            WQS_PLUGIN_URL . 'assets/js/frontend.js',
            ['jquery'],
            filemtime(WQS_PLUGIN_PATH . 'assets/js/frontend.js'),
            true
        );
    }

    public function hide_price() {
        return '';
    }

    public function add_quote_button() {
        echo '<button class="wqs-quote-button button alt">'.esc_html__('Get Quote', 'wqs').'</button>';
    }

    public function quote_modal() {
        $shortcode = $this->options['form_shortcode'] ?? '';
        ?>
        <div id="wqs-quote-modal" class="wqs-modal">
            <div class="wqs-modal-overlay"></div>
            <div class="wqs-modal-content">
                <span class="wqs-close">&times;</span>
                <div class="wqs-form-container">
                    <?php 
                    // 调试输出
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log('[WQS] Rendering shortcode: ' . $shortcode);
                    }
                    echo do_shortcode($shortcode); 
                    ?>
                </div>
            </div>
        </div>
        <?php
    }

    public function add_whatsapp_button() {
        $number = $this->options['whatsapp_number'] ?? '';
        if (empty($number)) return;

        $current_url = rawurlencode(get_permalink());
        $message = rawurlencode(__("I'm interested in this product: ", 'wqs'));
        
        echo sprintf(
            '<a href="%s" class="wqs-whatsapp-button button alt" target="_blank" rel="noopener noreferrer">
                <span class="dashicons dashicons-whatsapp"></span> %s
            </a>',
            esc_url('https://wa.me/'.$number.'?text='.$message.$current_url),
            esc_html__('Chat via WhatsApp', 'wqs')
        );
    }
}