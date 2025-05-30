<?php
class WQS_Admin_Settings {

    private $options;

    public function __construct() {
        $this->options = get_option('wqs_options');
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'admin_assets']);
        add_action('admin_footer', [$this, 'add_admin_scripts']);
    }

    public function admin_assets($hook) {
        if ($hook !== 'woocommerce_page_wqs-settings') return;
        
        wp_enqueue_style(
            'wqs-admin',
            plugins_url('assets/css/admin.css', __FILE__),
            [],
            filemtime(plugin_dir_path(__FILE__) . 'assets/css/admin.css')
        );

        wp_enqueue_script(
            'wqs-admin',
            plugins_url('assets/js/admin.js', __FILE__),
            ['jquery'],
            filemtime(plugin_dir_path(__FILE__) . 'assets/js/admin.js'),
            true
        );

        wp_localize_script('wqs-admin', 'wqsAdminParams', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('wqs_nonce')
        ]);
    }

    public function add_menu() {
        add_submenu_page(
            'woocommerce',
            __('Quote System Settings', 'wqs'),
            __('Quote System', 'wqs'),
            'manage_options',
            'wqs-settings',
            [$this, 'settings_page']
        );
    }

    public function register_settings() {
        register_setting(
            'wqs_settings',
            'wqs_options',
            ['sanitize_callback' => [$this, 'sanitize_options']]
        );

        add_settings_section(
            'wqs_main',
            __('Core Settings', 'wqs'),
            null,
            'wqs-settings'
        );

        // Basic features
        $this->add_switch_field('hide_prices', __('Hide Product Prices', 'wqs'), [
            'description' => __('Globally hide product price display', 'wqs')
        ]);
        
        $this->add_switch_field('hide_cart_button', __('Hide Cart Button', 'wqs'), [
            'description' => __('Disable the default cart function', 'wqs')
        ]);
        
        // Quote feature
        add_settings_field(
            'quote_settings',
            __('Quote Button Settings', 'wqs'),
            [$this, 'render_quote_field'],
            'wqs-settings',
            'wqs_main'
        );

        // WhatsApp feature
        add_settings_field(
            'whatsapp_settings',
            __('WhatsApp Settings', 'wqs'),
            [$this, 'render_whatsapp_field'],
            'wqs-settings',
            'wqs_main'
        );
    }

    private function add_switch_field($name, $title) {
        add_settings_field(
            $name,
            $title,
            [$this, 'render_switch'],
            'wqs-settings',
            'wqs_main',
            ['name' => $name]
        );
    }
    
    private function add_text_field($name, $title, $args = []) {
        add_settings_field(
            $name,
            $title,
            [$this, 'render_text_field'],
            'wqs-settings',
            'wqs_main',
            array_merge(
                ['name' => $name],
                $args
            )
        );
    }

    public function render_text_field($args) {
        $name = $args['name'];
        $value = $this->options[$name] ?? '';
        $description = $args['description'] ?? '';
        $wrapper_attrs = $args['wrapper_attrs'] ?? [];
        
        // Build container attributes
        $wrapper_attr_string = '';
        foreach ($wrapper_attrs as $attr => $val) {
            $wrapper_attr_string .= sprintf(' %s="%s"', $attr, esc_attr($val));
        }
        ?>
        <div<?php echo $wrapper_attr_string; ?>>
            <input type="text" 
                   name="wqs_options[<?php echo esc_attr($name); ?>]"
                   value="<?php echo esc_attr($value); ?>"
                   class="regular-text">
            <?php if ($description) : ?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    public function settings_page() {
        ?>
        <div class="wrap wqs-settings-wrap">
            <h1><?php esc_html_e('WooCommerce Quote System Settings', 'wqs'); ?></h1>
            <?php settings_errors('wqs_options'); ?>
            <form method="post" action="options.php">
                <?php
                settings_fields('wqs_settings');
                do_settings_sections('wqs-settings');
                submit_button('Save Changes', 'primary', 'submit', true);
                ?>
            </form>
        </div>
        <?php
    }

    public function sanitize_options($input) {
        $clean = [];
        $errors = [];

        // Basic switches
        $switches = ['hide_prices', 'hide_cart_button', 'enable_quote', 'enable_whatsapp'];
        foreach ($switches as $switch) {
            $clean[$switch] = isset($input[$switch]) ? 1 : 0;
        }

        // Quote validation
        if ($clean['enable_quote'] && empty(trim($input['form_shortcode']))) {
            $errors[] = __('You must fill in the form shortcode when enabling the quote function', 'wqs');
            $clean['enable_quote'] = 0;
        } else {
            $clean['form_shortcode'] = sanitize_text_field($input['form_shortcode'] ?? '');
        }

        // WhatsApp validation
        if ($clean['enable_whatsapp']) {
            $number = $input['whatsapp_number'] ?? '';
            if (empty($number)) {
                $errors[] = __('You must fill in the WhatsApp number', 'wqs');
                $clean['enable_whatsapp'] = 0;
            } elseif (!preg_match('/^\+[1-9]\d{1,14}$/', $number)) {
                $errors[] = __('The WhatsApp number format is incorrect. Example: +8612345678901', 'wqs');
                $clean['enable_whatsapp'] = 0;
            } else {
                $clean['whatsapp_number'] = sanitize_text_field($number);
            }
        }

        // Error handling
        if (!empty($errors)) {
            add_settings_error(
                'wqs_options',
                'wqs_validation',
                implode('<br>', $errors),
                'error'
            );
        }

        return array_merge($this->options, $clean);
    }

    public function render_switch($args) {
        $name = $args['name'];
        $checked = $this->options[$name] ?? 0;
        ?>
        <label class="wqs-switch">
            <input type="checkbox" 
                   name="wqs_options[<?php echo esc_attr($name); ?>]" 
                   value="1" 
                   <?php checked($checked, 1); ?>>
            <span class="wqs-slider"></span>
        </label>
        <?php
    }

    public function render_quote_field() {
        $enabled = $this->options['enable_quote'] ?? 0;
        $shortcode = esc_attr($this->options['form_shortcode'] ?? '');
        ?>
        <div class="wqs-field-group">
            <div class="wqs-switch-container">
                <?php $this->render_switch(['name' => 'enable_quote']); ?>
                <span class="switch-label"><?php esc_html_e('Enable Get Quote Button', 'wqs'); ?></span>
            </div>
            
            <div id="enable-quote-field" class="wqs-dependent-field<?php echo $enabled ? '' : ' hidden'; ?>">
                <input type="text" 
                       name="wqs_options[form_shortcode]"
                       value="<?php echo $shortcode; ?>"
                       placeholder="Enter shortcode"
                       class="regular-text">
                <p class="description">
                    <?php esc_html_e('Enter the form shortcode', 'wqs'); ?>
                </p>
            </div>
        </div>
        <?php
    }

    public function render_whatsapp_field() {
        $enabled = $this->options['enable_whatsapp'] ?? 0;
        $number = esc_attr($this->options['whatsapp_number'] ?? '');
        ?>
        <div class="wqs-field-group">
            <div class="wqs-switch-container">
                <?php $this->render_switch(['name' => 'enable_whatsapp']); ?>
                <span class="switch-label"><?php esc_html_e('Enable WhatsApp Button', 'wqs'); ?></span>
            </div>
            
            <div id="enable-whatsapp-field" class="wqs-dependent-field<?php echo $enabled ? '' : ' hidden'; ?>">
                <input type="tel" 
                       name="wqs_options[whatsapp_number]"
                       value="<?php echo $number; ?>"
                       placeholder="+8612345678901"
                       class="regular-text"
                       pattern="^\+[1-9]\d{1,14}$">
                <p class="description">
                    <?php esc_html_e('International format, example: +8612345678901', 'wqs'); ?>
                </p>
            </div>
        </div>
        <?php
    }
    
    public function add_admin_scripts() {
        wp_enqueue_script('jquery');
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Handle WhatsApp switch click event
                $('.wqs-switch input[name="wqs_options[enable_whatsapp]"]').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#enable-whatsapp-field').removeClass('hidden');
                    } else {
                        $('#enable-whatsapp-field').addClass('hidden');
                    }
                });

                // Handle Get Quote switch click event
                $('.wqs-switch input[name="wqs_options[enable_quote]"]').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#enable-quote-field').removeClass('hidden');
                    } else {
                        $('#enable-quote-field').addClass('hidden');
                    }
                });
            });
        </script>
        <?php
    }
}
