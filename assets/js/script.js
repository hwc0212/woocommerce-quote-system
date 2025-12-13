/**
 * WooCommerce Quote System JavaScript
 * 
 * @package WooCommerce Quote System
 * @version 1.5.1
 */

jQuery(document).ready(function($) {
    'use strict';
    
    var isModalOpen = false;
    var originalBodyOverflow = '';
    var scrollPosition = 0;
    
    /**
     * Prevent body scroll on mobile devices
     */
    function preventBodyScroll() {
        scrollPosition = $(window).scrollTop();
        originalBodyOverflow = $('body').css('overflow');
        
        $('body').css({
            'overflow': 'hidden',
            'position': 'fixed',
            'width': '100%',
            'top': -scrollPosition + 'px'
        });
    }
    
    /**
     * Restore body scroll
     */
    function restoreBodyScroll() {
        $('body').css({
            'overflow': originalBodyOverflow,
            'position': '',
            'width': '',
            'top': ''
        });
        
        $(window).scrollTop(scrollPosition);
    }
    
    /**
     * Open quote modal
     */
    $(document).on('click', '.wcqs-quote-btn', function(e) {
        e.preventDefault();
        
        // 直接显示弹窗，不需要获取产品信息
        isModalOpen = true;
        preventBodyScroll();
        $('#wcqs-quote-modal').fadeIn(300);
        
        // 聚焦到第一个输入框（移动端友好）
        setTimeout(function() {
            $('#wcqs-quote-modal').find('input[type="text"], input[type="email"], textarea').first().focus();
        }, 350);
    });
    

    
    /**
     * Close modal function
     */
    function closeModal() {
        if (isModalOpen) {
            isModalOpen = false;
            $('#wcqs-quote-modal').fadeOut(300, function() {
                restoreBodyScroll();
            });
        }
    }
    
    // 关闭弹窗 - 点击背景或关闭按钮
    $(document).on('click', '.wcqs-close, .wcqs-modal', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    
    // 阻止弹窗内容区域点击时关闭弹窗
    $(document).on('click', '.wcqs-modal-content', function(e) {
        e.stopPropagation();
    });
    
    // ESC键关闭弹窗
    $(document).keyup(function(e) {
        if (e.keyCode === 27 && isModalOpen) { // ESC key
            closeModal();
        }
    });
    
    // 移动端触摸优化
    var touchStartY = 0;
    var touchEndY = 0;
    
    $(document).on('touchstart', '.wcqs-modal-content', function(e) {
        touchStartY = e.originalEvent.touches[0].clientY;
    });
    
    $(document).on('touchmove', '.wcqs-modal-content', function(e) {
        var modalContent = this;
        var scrollTop = modalContent.scrollTop;
        var scrollHeight = modalContent.scrollHeight;
        var clientHeight = modalContent.clientHeight;
        
        // 防止过度滚动
        if (scrollTop === 0 && e.originalEvent.touches[0].clientY > touchStartY) {
            e.preventDefault();
        }
        if (scrollTop + clientHeight >= scrollHeight && e.originalEvent.touches[0].clientY < touchStartY) {
            e.preventDefault();
        }
    });
    
    // 向下滑动关闭弹窗（移动端）
    $(document).on('touchend', '.wcqs-modal-content', function(e) {
        touchEndY = e.originalEvent.changedTouches[0].clientY;
        var swipeDistance = touchEndY - touchStartY;
        
        // 如果向下滑动超过100px且在顶部，则关闭弹窗
        if (swipeDistance > 100 && this.scrollTop === 0) {
            closeModal();
        }
    });
    
    /**
     * Form submission success handlers
     */
    function handleFormSuccess() {
        setTimeout(closeModal, 2000);
    }
    
    // Contact Form 7
    $(document).on('wpcf7mailsent', handleFormSuccess);
    
    // WPForms
    $(document).on('wpformsAjaxSubmitSuccess', handleFormSuccess);
    
    // Ninja Forms
    $(document).on('submitResponse.default', function(event) {
        if (event.response && event.response.success) {
            handleFormSuccess();
        }
    });
    
    // Gravity Forms
    $(document).on('gform_confirmation_loaded', handleFormSuccess);
    
    // 窗口大小改变时的处理
    $(window).on('resize orientationchange', function() {
        if (isModalOpen) {
            // 延迟处理，等待方向改变完成
            setTimeout(function() {
                // 重新计算弹窗位置
                var $modal = $('#wcqs-quote-modal');
                if ($modal.is(':visible')) {
                    $modal.css('display', 'flex');
                }
            }, 100);
        }
    });
    
    // 提升移动端表单体验
    $(document).on('focus', '.wcqs-form-container input, .wcqs-form-container textarea', function() {
        // 移动端聚焦时滚动到输入框
        if ($(window).width() <= 768) {
            setTimeout(function() {
                var $input = $(this);
                var modalContent = $('.wcqs-modal-content')[0];
                var inputTop = $input.offset().top - $('.wcqs-modal-content').offset().top;
                modalContent.scrollTop = inputTop - 100;
            }.bind(this), 300);
        }
    });
    
});