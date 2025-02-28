jQuery(document).ready(function($) {
    // 获取nonce
    const security = $('#wqs_nonce').val();

    // 通用开关控制（使用事件委托）
    function handleToggle(containerClass, checkboxSelector, targetSelector) {
        $('.' + containerClass).on('change', checkboxSelector, function() {
            const $target = $(this).closest(targetSelector);
            $target.toggle(this.checked);
            if (!this.checked) {
                $target.find('input').val('');
            }
        }).trigger('change');
    }

    // 初始化开关
    handleToggle('wqs-toggle-group', '.wqs-switch input[type="checkbox"]', '.wqs-field-group');

    // 国际号码验证（E.164标准）
    const validatePhone = (number) => /^\+[1-9]\d{1,3}\d{4,14}$/.test(number);

    $('input[name="wqs_options[whatsapp_number]"]').on('input', function() {
        const $this = $(this);
        const isValid = validatePhone($this.val());
        
        $this.toggleClass('invalid', !isValid);
        $this.next('.error-message').remove();
        
        if (!isValid && $this.val()) {
            $this.after(
                $('<div/>', {
                    class: 'error-message',
                    css: {color:'#dc3232', marginTop:'5px'},
                    text: wp.i18n.__('请输入有效的国际号码 (例如: +8612345678901)', 'wqs')
                })
            );
        }
    });

    // 表单提交处理
    $('form').on('submit', function(e) {
        const $form = $(this);
        
        // 验证nonce
        if (!wpApiSettings.nonce || !$form.find('#_wpnonce').val()) {
            alert(wp.i18n.__('安全验证失败', 'wqs'));
            return false;
        }

        // WhatsApp验证
        if ($('#wqs-enable-whatsapp').prop('checked')) {
            const phone = $('input[name="wqs_options[whatsapp_number]"]').val();
            if (!validatePhone(phone)) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $('.wqs-whatsapp-field').offset().top - 100
                }, 500);
                return false;
            }
        }

        // 其他验证逻辑...
        return true;
    });
});