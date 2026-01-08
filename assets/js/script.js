/**
 * WooCommerce Quote System JavaScript
 * 
 * @package WooCommerce Quote System
 * @version 1.5.2
 */

(function($) {
    'use strict';
    
    // Plugin state management
    const WCQS = {
        isModalOpen: false,
        originalBodyOverflow: '',
        scrollPosition: 0,
        touchStartY: 0,
        touchEndY: 0,
        isSubmitting: false,
        
        // Initialize plugin
        init: function() {
            this.bindEvents();
            this.setupAccessibility();
        },
        
        // Bind all event handlers
        bindEvents: function() {
            // Quote button click
            $(document).on('click', '.wcqs-quote-btn', this.openModal.bind(this));
            
            // Modal close events
            $(document).on('click', '.wcqs-close, .wcqs-modal', this.handleModalClick.bind(this));
            $(document).on('click', '.wcqs-modal-content', this.stopPropagation);
            $(document).on('keyup', this.handleKeyPress.bind(this));
            
            // Touch events for mobile
            $(document).on('touchstart', '.wcqs-modal-content', this.handleTouchStart.bind(this));
            $(document).on('touchmove', '.wcqs-modal-content', this.handleTouchMove.bind(this));
            $(document).on('touchend', '.wcqs-modal-content', this.handleTouchEnd.bind(this));
            
            // Form events
            this.bindFormEvents();
            
            // Window events
            $(window).on('resize orientationchange', this.debounce(this.handleResize.bind(this), 100));
            
            // Focus management for mobile
            $(document).on('focus', '.wcqs-form-container input, .wcqs-form-container textarea', this.handleInputFocus.bind(this));
        },
        
        // Setup accessibility features
        setupAccessibility: function() {
            // Add ARIA attributes
            $('.wcqs-quote-btn').attr({
                'aria-haspopup': 'dialog',
                'aria-expanded': 'false'
            });
        },
        
        // Open quote modal
        openModal: function(e) {
            e.preventDefault();
            
            if (this.isModalOpen) return;
            
            const $modal = $('#wcqs-quote-modal');
            if (!$modal.length) return;
            
            this.isModalOpen = true;
            this.preventBodyScroll();
            
            // Update ARIA attributes
            $('.wcqs-quote-btn').attr('aria-expanded', 'true');
            $modal.attr('aria-hidden', 'false');
            
            // Show modal with animation
            $modal.fadeIn(300, () => {
                // Focus management
                this.focusFirstInput();
                
                // Announce to screen readers
                this.announceToScreenReader(wcqs_vars.modal_title || 'Quote modal opened');
            });
        },
        
        // Close modal
        closeModal: function() {
            if (!this.isModalOpen) return;
            
            const $modal = $('#wcqs-quote-modal');
            
            this.isModalOpen = false;
            
            // Update ARIA attributes
            $('.wcqs-quote-btn').attr('aria-expanded', 'false');
            $modal.attr('aria-hidden', 'true');
            
            $modal.fadeOut(300, () => {
                this.restoreBodyScroll();
                
                // Return focus to trigger button
                $('.wcqs-quote-btn').first().focus();
            });
        },
        
        // Handle modal click events
        handleModalClick: function(e) {
            if (e.target === e.currentTarget) {
                this.closeModal();
            }
        },
        
        // Stop event propagation
        stopPropagation: function(e) {
            e.stopPropagation();
        },
        
        // Handle keyboard events
        handleKeyPress: function(e) {
            if (!this.isModalOpen) return;
            
            switch(e.keyCode) {
                case 27: // ESC key
                    this.closeModal();
                    break;
                case 9: // TAB key
                    this.handleTabKey(e);
                    break;
            }
        },
        
        // Handle tab key for focus trapping
        handleTabKey: function(e) {
            const $modal = $('#wcqs-quote-modal');
            const $focusableElements = $modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            const $firstElement = $focusableElements.first();
            const $lastElement = $focusableElements.last();
            
            if (e.shiftKey) {
                if (document.activeElement === $firstElement[0]) {
                    e.preventDefault();
                    $lastElement.focus();
                }
            } else {
                if (document.activeElement === $lastElement[0]) {
                    e.preventDefault();
                    $firstElement.focus();
                }
            }
        },
        
        // Touch event handlers
        handleTouchStart: function(e) {
            this.touchStartY = e.originalEvent.touches[0].clientY;
        },
        
        handleTouchMove: function(e) {
            const modalContent = e.currentTarget;
            const scrollTop = modalContent.scrollTop;
            const scrollHeight = modalContent.scrollHeight;
            const clientHeight = modalContent.clientHeight;
            const currentY = e.originalEvent.touches[0].clientY;
            
            // Prevent overscroll
            if (scrollTop === 0 && currentY > this.touchStartY) {
                e.preventDefault();
            }
            if (scrollTop + clientHeight >= scrollHeight && currentY < this.touchStartY) {
                e.preventDefault();
            }
        },
        
        handleTouchEnd: function(e) {
            this.touchEndY = e.originalEvent.changedTouches[0].clientY;
            const swipeDistance = this.touchEndY - this.touchStartY;
            
            // Close modal on downward swipe from top
            if (swipeDistance > 100 && e.currentTarget.scrollTop === 0) {
                this.closeModal();
            }
        },
        
        // Handle window resize
        handleResize: function() {
            if (!this.isModalOpen) return;
            
            const $modal = $('#wcqs-quote-modal');
            if ($modal.is(':visible')) {
                $modal.css('display', 'flex');
            }
        },
        
        // Handle input focus on mobile
        handleInputFocus: function(e) {
            if (!wcqs_vars.is_mobile) return;
            
            const $input = $(e.target);
            const $modalContent = $('.wcqs-modal-content');
            
            setTimeout(() => {
                const inputTop = $input.offset().top - $modalContent.offset().top;
                $modalContent[0].scrollTop = Math.max(0, inputTop - 100);
            }, 300);
        },
        
        // Prevent body scroll
        preventBodyScroll: function() {
            this.scrollPosition = $(window).scrollTop();
            this.originalBodyOverflow = $('body').css('overflow');
            
            $('body').css({
                'overflow': 'hidden',
                'position': 'fixed',
                'width': '100%',
                'top': -this.scrollPosition + 'px'
            });
        },
        
        // Restore body scroll
        restoreBodyScroll: function() {
            $('body').css({
                'overflow': this.originalBodyOverflow,
                'position': '',
                'width': '',
                'top': ''
            });
            
            $(window).scrollTop(this.scrollPosition);
        },
        
        // Focus first input
        focusFirstInput: function() {
            setTimeout(() => {
                const $firstInput = $('#wcqs-quote-modal').find('input[type="text"], input[type="email"], textarea').first();
                if ($firstInput.length) {
                    $firstInput.focus();
                }
            }, 350);
        },
        
        // Announce to screen readers
        announceToScreenReader: function(message) {
            const $announcement = $('<div>', {
                'aria-live': 'polite',
                'aria-atomic': 'true',
                'class': 'sr-only',
                'text': message
            });
            
            $('body').append($announcement);
            setTimeout(() => $announcement.remove(), 1000);
        },
        
        // Bind form submission events
        bindFormEvents: function() {
            // Contact Form 7
            $(document).on('wpcf7mailsent', this.handleFormSuccess.bind(this));
            $(document).on('wpcf7mailfailed', this.handleFormError.bind(this));
            
            // WPForms
            $(document).on('wpformsAjaxSubmitSuccess', this.handleFormSuccess.bind(this));
            $(document).on('wpformsAjaxSubmitError', this.handleFormError.bind(this));
            
            // Ninja Forms
            $(document).on('submitResponse.default', (e) => {
                if (e.response && e.response.success) {
                    this.handleFormSuccess();
                } else {
                    this.handleFormError();
                }
            });
            
            // Gravity Forms
            $(document).on('gform_confirmation_loaded', this.handleFormSuccess.bind(this));
            
            // Generic form submission
            $(document).on('submit', '.wcqs-form-container form', this.handleFormSubmit.bind(this));
        },
        
        // Handle form submission
        handleFormSubmit: function(e) {
            if (this.isSubmitting) {
                e.preventDefault();
                return false;
            }
            
            this.isSubmitting = true;
            this.showLoadingState();
        },
        
        // Handle successful form submission
        handleFormSuccess: function() {
            this.isSubmitting = false;
            this.hideLoadingState();
            this.showSuccessMessage();
            
            setTimeout(() => {
                this.closeModal();
            }, 2000);
        },
        
        // Handle form submission error
        handleFormError: function() {
            this.isSubmitting = false;
            this.hideLoadingState();
            this.showErrorMessage();
        },
        
        // Show loading state
        showLoadingState: function() {
            const $submitBtn = $('.wcqs-form-container input[type="submit"], .wcqs-form-container button[type="submit"]');
            $submitBtn.prop('disabled', true).addClass('wcqs-loading');
        },
        
        // Hide loading state
        hideLoadingState: function() {
            const $submitBtn = $('.wcqs-form-container input[type="submit"], .wcqs-form-container button[type="submit"]');
            $submitBtn.prop('disabled', false).removeClass('wcqs-loading');
        },
        
        // Show success message
        showSuccessMessage: function() {
            this.showMessage('success', 'Thank you! Your quote request has been sent successfully.');
        },
        
        // Show error message
        showErrorMessage: function() {
            this.showMessage('error', 'Sorry, there was an error sending your request. Please try again.');
        },
        
        // Show message
        showMessage: function(type, text) {
            const $message = $('<div>', {
                'class': `wcqs-message wcqs-message-${type}`,
                'text': text,
                'role': 'alert'
            });
            
            $('.wcqs-form-container').prepend($message);
            
            setTimeout(() => {
                $message.fadeOut(() => $message.remove());
            }, 5000);
        },
        
        // Debounce utility function
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    };
    
    // Initialize when document is ready
    $(document).ready(() => {
        WCQS.init();
    });
    
    // Expose WCQS object globally for debugging
    window.WCQS = WCQS;
    
})(jQuery);