=== WooCommerce Quote System ===
Contributors: yourname
Donate link: https://yourdomain.com/donate
Tags: woocommerce, quote, contact form 7
Requires at least: 5.6
Tested up to: 6.5
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
专业的WooCommerce报价解决方案，提供以下功能：

* 独立控制价格显示
* 独立控制购物车按钮显示
* 可配置的报价表单系统
* 无缝集成Contact Form 7
* 响应式弹窗设计

主要特点：
- 在商品页面添加"Get Quote"按钮
- 支持使用Contact Form 7短代码
- 完全兼容最新版WooCommerce
- 直观的后台配置界面

== Installation ==
1. 通过WordPress后台插件页面上传安装
2. 激活插件
3. 确保已安装WooCommerce和Contact Form 7
4. 进入 WooCommerce → 设置 → 报价系统 进行配置

== Frequently Asked Questions ==
= 为什么报价按钮不显示？
1. 确保已在设置中启用报价功能
2. 确认Contact Form 7短代码配置正确
3. 检查是否有JavaScript冲突

= 如何自定义表单样式？
可以通过添加CSS到主题的style.css文件修改弹窗样式，我们的弹窗容器类名为`.wcqs-modal`

= 支持哪些语言？
默认包含英文，可通过语言文件添加其他语言翻译

== Screenshots ==
1. 后台设置界面
2. 前端报价按钮展示
3. 弹窗表单演示

== Changelog ==
= 1.0.0 =
* 初始发布版本
* 实现基础报价功能
* 后台控制面板集成

= 0.9.0 =
* Beta测试版本
* 核心功能开发完成

== Upgrade Notice ==
建议保持插件最新版本以获得最佳安全性和功能体验

== Technical Requirements ==
* PHP 7.4 或更高版本
* WordPress 5.6 或更高版本
* WooCommerce 6.0+
* Contact Form 7 5.7+

== Developer Notes ==
插件包含以下hooks：
`wcqs_before_quote_button` - 在报价按钮前添加内容
`wcqs_after_quote_modal` - 在弹窗后添加内容

样式覆盖指南：
所有前端样式可通过!important声明覆盖

== Translations ==
包含中文语言文件，翻译贡献欢迎通过GitHub提交：
https://github.com/yourname/woocommerce-quote-system