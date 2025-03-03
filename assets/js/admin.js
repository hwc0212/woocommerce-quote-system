jQuery(document).ready(function($) {
    // 初始化开关状态
    function initSwitches() {
        $('.wqs-switch input').each(function() {
            const target = $(this).data('target');
            if (target) {
                $(target).toggle(this.checked);
            }
        });
    }

    // 动态切换
    $('.wqs-switch input').on('change', function() {
        const target = $(this).data('target');
        if (target) {
            $(target).stop().slideToggle(300);
        }
    });

    // 初始化执行
    initSwitches();
});
