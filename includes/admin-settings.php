<?php
class WQS_Admin_Settings {

    private $options;

    public function __construct() {
        $this->options = get_option('wqs_options');
        add_action('admin_enqueue_scripts', [$this, 'admin_assets']);
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function admin_assets($hook) {
        // 仅在插件设置页面加载资源
        if ($hook !== 'woocommerce_page_wqs-settings') return;

        // 加载后台JS
        wp_enqueue_script(
            'wqs-admin',
            WQS_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery', 'wp-i18n'],
            filemtime(WQS_PLUGIN_PATH . 'assets/js/admin.js'),
            true
        );

        // 传递安全参数
        wp_localize_script('wqs-admin', 'wpApiSettings', [
            'nonce' => wp_create_nonce('wp_rest')
        ]);

        // 加载翻译文件
        wp_set_script_translations('wqs-admin', 'wqs', WQS_PLUGIN_PATH . 'languages');
    }

    public function add_menu() {
        add_submenu_page(
            'woocommerce',
            esc_html__('Quote Settings', 'wqs'),
            esc_html__('Quote Settings', 'wqs'),
            'manage_options',
            'wqs-settings',
            [$this, 'settings_page']
        );
    }

    public function register_settings() {
        register_setting('wqs_settings', 'wqs_options', [
            'sanitize_callback' => [$this, 'sanitize_options']
        ]);

        add_settings_section(
            'wqs_main',
            esc_html__('Quote System Settings', 'wqs'),
            null,
            'wqs-settings'
        );

        add_settings_field(
            'hide_prices',
            esc_html__('Hide Product Prices', 'wqs'),
            [$this, 'switch_field'],
            'wqs-settings',
            'wqs_main',
            ['name' => 'hide_prices']
        );

        add_settings_field(
            'hide_cart_button',
            esc_html__('Hide Add to Cart', 'wqs'),
            [$this, 'switch_field'],
            'wqs-settings',
            'wqs_main',
            ['name' => 'hide_cart_button']
        );

        add_settings_field(
            'enable_quote_button',
            esc_html__('Enable Quote Button', 'wqs'),
            [$this, 'quote_button_field'],
            'wqs-settings',
            'wqs_main'
        );

        add_settings_field(
            'enable_whatsapp',
            esc_html__('Enable WhatsApp Button', 'wqs'),
            [$this, 'whatsapp_button_field'],
            'wqs-settings',
            'wqs_main'
        );
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('WooCommerce Quote Settings', 'wqs'); ?></h1>
            <?php settings_errors('wqs_options'); ?>
            <form method="post" action="options.php">
                <?php 
                settings_fields('wqs_settings');
                do_settings_sections('wqs-settings');
                submit_button(); 
                ?>
            </form>
        </div>
        <?php
    }

	public function sanitize_options($input) {
		$clean = [];
		$errors = [];

		// WhatsApp号码验证（保留）
		if (!empty($input['enable_whatsapp'])) {
			$number = sanitize_text_field($input['whatsapp_number'] ?? '');
			if (!preg_match('/^\+[1-9]\d{1,14}$/', $number)) {
				$errors[] = esc_html__('Invalid WhatsApp number format', 'wqs');
				$input['enable_whatsapp'] = 0;
			} else {
				$clean['whatsapp_number'] = $number;
			}
		}

		// 短代码处理（移除验证）
		if (!empty($input['enable_quote_button'])) {
			$clean['form_shortcode'] = sanitize_text_field($input['form_shortcode'] ?? '');
		}

		// 处理开关状态
		$switches = [
			'hide_prices', 
			'hide_cart_button',
			'enable_quote_button',
			'enable_whatsapp'
		];
		foreach ($switches as $switch) {
			$clean[$switch] = isset($input[$switch]) ? 1 : 0;
		}

		// 显示错误（仅WhatsApp错误）
		if (!empty($errors)) {
			add_settings_error(
				'wqs_options',
				'wqs_validation_error',
				implode('<br>', $errors),
				'error'
			);
		}

		return $clean;
	}

    public function switch_field($args) {
        $name = esc_attr($args['name']);
        $checked = $this->options[$name] ?? 0;
        ?>
        <label class="wqs-switch">
            <input type="checkbox" 
                   name="wqs_options[<?php echo $name; ?>]" 
                   value="1" <?php checked($checked, 1); ?>>
            <span class="slider"></span>
        </label>
        <?php
    }

    public function quote_button_field() {
        $enabled = $this->options['enable_quote_button'] ?? 0;
        $shortcode = esc_attr($this->options['form_shortcode'] ?? '');
        ?>
        <div class="wqs-toggle-group">
            <label class="wqs-switch">
                <input type="checkbox" 
                       name="wqs_options[enable_quote_button]" 
                       id="wqs-enable-quote"
                       value="1" <?php checked($enabled, 1); ?>>
                <span class="slider"></span>
            </label>
            
            <div class="wqs-shortcode-field" style="margin-top:15px;<?php echo !$enabled ? 'display:none;' : ''; ?>">
                <input type="text" 
                       name="wqs_options[form_shortcode]"
                       placeholder="[your_form_shortcode]"
                       value="<?php echo $shortcode; ?>"
                       class="regular-text">
                <p class="description">
                    <?php esc_html_e('Example: [contact-form-7 id="123" title="Quote Form"]', 'wqs'); ?>
                </p>
            </div>
        </div>
        <?php
    }

    public function whatsapp_button_field() {
        $enabled = $this->options['enable_whatsapp'] ?? 0;
        $number = esc_attr($this->options['whatsapp_number'] ?? '');
        ?>
        <div class="wqs-toggle-group">
            <label class="wqs-switch">
                <input type="checkbox" 
                       name="wqs_options[enable_whatsapp]"
                       id="wqs-enable-whatsapp"
                       value="1" <?php checked($enabled, 1); ?>>
                <span class="slider"></span>
            </label>
            
            <div class="wqs-whatsapp-field" style="margin-top:15px;<?php echo !$enabled ? 'display:none;' : ''; ?>">
                <input type="tel" 
                       name="wqs_options[whatsapp_number]"
                       placeholder="+8612345678901"
                       value="<?php echo $number; ?>"
                       class="regular-text"
                       pattern="^\+[1-9]\d{1,14}$">
                <p class="description">
                    <?php esc_html_e('Format: +[country code][phone number]', 'wqs'); ?>
                </p>
            </div>
        </div>
        <?php
    }
}
