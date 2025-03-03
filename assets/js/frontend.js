jQuery(document).ready(function($) {
    // 弹窗控制
    const initModal = () => {
        const $modal = $('#wqs-quote-modal');
        
        $('body').on('click', '.wqs-quote-button', function(e) {
            e.preventDefault(); // 防止默认行为
            $modal.fadeIn(200);
        });

        // 点击关闭按钮或蒙层关闭弹窗
        $modal.on('click', '.wqs-close, .wqs-modal-overlay', function(e) {
            e.preventDefault(); // 防止默认行为
            $modal.fadeOut(200);
        });

        // 按下esc键关闭弹窗
        $(document).on('keyup', function(e) {
            if (e.key === "Escape" && $modal.is(':visible')) {
                $modal.fadeOut(200);
            }
        });
    };

    // 修改布局计算逻辑
    const adjustFormLayout = () => {
        const $formContainer = $('.wqs-form-container');
        const modalContent = document.querySelector('.wqs-modal-content');
        
        // 自动居中
        modalContent.style.marginTop = `${window.innerHeight * 0.15}px`;
        
        // 高度计算
        const maxContentHeight = window.innerHeight * 0.7;
        $formContainer.css({
            'max-height': `${maxContentHeight}px`,
            'overflow-y': 'auto'
        }).scrollTop(0);
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
            $(document).off('keyup');
            $(window).off('resize', handleResize);
        };
    };

    const cleanup = init();
    $(window).on('unload', cleanup);
});
