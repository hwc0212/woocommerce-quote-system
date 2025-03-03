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

		// 统一按钮初始化逻辑
		if ($this->should_show_buttons()) {
			add_action('woocommerce_single_product_summary', [$this, 'render_buttons'], 35);
			add_action('wp_footer', [$this, 'quote_modal']);
			add_action('wp_enqueue_scripts', [$this, 'register_assets']);
		}
    }
	
	// 新增辅助方法
	private function should_show_buttons() {
		$show_quote = !empty($this->options['enable_quote']) && !empty($this->options['form_shortcode']);
		$show_whatsapp = !empty($this->options['enable_whatsapp']) && !empty($this->options['whatsapp_number']);
		
		return $show_quote || $show_whatsapp;
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

	public function quote_modal() {
		if (empty($this->options['form_shortcode'])) return;
		
		echo '
		<div id="wqs-quote-modal" class="wqs-modal">
			<div class="wqs-modal-overlay"></div>
			<div class="wqs-modal-content">
				<h2 class="wqs-modal-title">Request a Quote</h2> <!-- 添加标题 -->
				<span class="wqs-close">&times;</span>
				<div class="wqs-form-container">
					'.do_shortcode($this->options['form_shortcode']).'
				</div>
			</div>
		</div>';
	}

	public function render_buttons() {
		echo '<div class="wqs-button-container">';

		// 报价按钮
		if (!empty($this->options['enable_quote']) && !empty($this->options['form_shortcode'])) {
			echo '<button class="wqs-quote-button button alt">';
			echo '<span class="dashicons dashicons-email wqs-icon"></span>';
			echo esc_html__('Get Quote', 'wqs');
			echo '</button>';
		}

		// WhatsApp按钮
		if (!empty($this->options['enable_whatsapp']) && !empty($this->options['whatsapp_number'])) {
			$number = $this->options['whatsapp_number'];
			$current_url = rawurlencode(get_permalink());
			$message = rawurlencode(__("I'm interested in this product: ", 'wqs'));
			
			if (strpos($number, '+') === 0) {
				$number_for_url = '+' . rawurlencode(substr($number, 1));
			} else {
				$number_for_url = rawurlencode($number);
			}
			
			$whatsapp_url = 'https://wa.me/' . $number_for_url . '?text=' . $message . $current_url;
			
			echo '<a href="' . esc_url($whatsapp_url) . '" class="wqs-whatsapp-button button alt" target="_blank">';
			echo '<span class="dashicons dashicons-whatsapp wqs-icon"></span>';
			echo esc_html__('Chat via WhatsApp', 'wqs');
			echo '</a>';
		}

		echo '</div>';
	}
}
