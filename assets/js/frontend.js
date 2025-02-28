jQuery(document).ready(function($) {
    // 弹窗控制
    const initModal = () => {
        // 显示弹窗 - 使用事件委托
        $('body').on('click', '.wqs-quote-button', function(e) {
            e.preventDefault();
            $('#wqs-quote-modal').fadeIn();
            setTimeout(adjustFormLayout, 150);
        });

        // 关闭弹窗 - 使用CSS类选择器
        $('body').on('click', '.wqs-close, .wqs-modal-overlay', function() {
            $('#wqs-quote-modal').fadeOut();
        });

        // ESC关闭 - 使用命名函数便于解绑
        $(document).on('keyup.wqsModal', function(e) {
            if (e.key === 'Escape') {
                $('#wqs-quote-modal').fadeOut();
            }
        });
    };

    // 修改布局计算逻辑
	const adjustFormLayout = () => {
		const $formContainer = $('.wqs-form-container');
		const modalContent = document.querySelector('.wqs-modal-content');
    
		// 使用现代API获取精确尺寸
		const { height: modalHeight } = modalContent.getBoundingClientRect();
		const maxContentHeight = modalHeight - 100; // 保留操作区空间
    
		$formContainer.css({
			'max-height': `${maxContentHeight}px`,
			'overflow-y': 'auto'
		}).scrollTop(0);
    
		// 强制浏览器重绘
		void modalContent.offsetHeight;
	};

    // WhatsApp链接生成 - 使用dataset API
    const initWhatsApp = () => {
        $('body').on('click', '.wqs-whatsapp-button', function() {
            const phone = this.dataset.phone;
            const message = encodeURIComponent(
                `I'm interested in: ${window.location.href}`
            );
            window.open(`https://wa.me/${phone}?text=${message}`, '_blank');
        });
    };

    // 窗口调整监听 - 使用requestAnimationFrame优化
    let resizeTimer;
    const handleResize = () => {
        cancelAnimationFrame(resizeTimer);
        resizeTimer = requestAnimationFrame(() => {
            if ($('#wqs-quote-modal').is(':visible')) {
                adjustFormLayout();
            }
        });
    };

    // 初始化
    const init = () => {
        initModal();
        initWhatsApp();
        $(window).on('resize', handleResize);
        
        // 清理事件监听
        return () => {
            $(document).off('keyup.wqsModal');
            $(window).off('resize', handleResize);
        };
    };

    const cleanup = init();
    $(window).on('unload', cleanup);
});
