=== WooCommerce Quote System ===
Contributors: hwc0212
Donate link: https://github.com/hwc0212/woocommerce-quote-system
Tags: woocommerce, quote system, product inquiry, contact form, whatsapp integration
Requires at least: 5.6
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 2.0.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Version: 2.0.7
Author: huwencai.com

Enhance your WooCommerce store with professional quote requests and instant WhatsApp communication.

== Description ==

= 主要功能 =
- 在产品页面添加专业的询价按钮
- 集成WhatsApp即时沟通功能
- 可定制的询价弹窗表单
- 响应式设计适配所有设备
- 智能表单验证与提交处理
- 与WooCommerce产品数据深度集成
- 支持Dashicons图标系统
- 灵活的显示条件控制

= 功能亮点 =
1. ​**双模式沟通**：客户可以选择填写询价表单或直接通过WhatsApp联系
2. ​**智能防骚扰**：自动记录用户提交频率，防止垃圾信息
3. ​**移动优先设计**：完美适配手机端操作流程
4. ​**营销集成**：自动携带产品信息到WhatsApp消息
5. ​**性能优化**：异步加载机制不影响页面加载速度

== Installation ==

= 自动安装 =
1. 前往WordPress后台插件页面
2. 搜索 "WooCommerce Quote System"
3. 点击安装并激活

= 手动安装 =
1. 下载插件zip文件
2. 通过WordPress后台插件上传界面安装
3. 或解压到wp-content/plugins目录
4. 激活插件

= 配置步骤 =
1. 进入 WooCommerce > Quote Settings
2. 配置WhatsApp号码和表单短代码
3. 选择需要显示按钮的产品分类
4. 自定义按钮样式和提示文字

== Frequently Asked Questions ==

= 按钮不显示可能的原因 =
1. 未启用WooCommerce
2. 未配置WhatsApp号码或表单短代码
3. 当前产品不在指定分类中
4. 主题CSS冲突（尝试添加!important）

= 如何自定义表单样式 =
推荐使用以下CSS类进行样式覆盖：
- .wqs-form-container
- .wqs-input-field
- .wqs-submit-button
- .wqs-error-message

= 支持哪些WhatsApp格式 =
支持国际格式号码（需包含国家代码），例如：
- +8613123456789
- 008613123456789

= 如何添加自定义字段 =
1. 通过表单生成插件创建新表单
2. 获取新表单的短代码
3. 在插件设置中替换原有短代码

== Screenshots ==
1. 产品页按钮展示截图
2. 询价表单弹窗界面
3. 后台设置页面
4. 移动端适配效果

== Changelog ==


== Upgrade Notice ==
建议所有用户升级到1.1版本以获得WhatsApp集成和增强的安全功能。

== Developer Docs ==
GitHub仓库: [https://github.com/hwc0212/woocommerce-quote-system](https://github.com/hwc0212/woocommerce-quote-system)

= 可用钩子 =
- `wqs_before_button_container`
- `wqs_after_form_submission`
- `wqs_whatsapp_message_filter`

= 示例代码 =
添加自定义按钮容器样式：
add_filter('wqs_button_container_class', function(classes) { classes[] = 'custom-button-style';
return $classes;
});


== Support ==
请通过GitHub Issues提交问题：  
[https://github.com/hwc0212/woocommerce-quote-system/issues](https://github.com/hwc0212/woocommerce-quote-system/issues)
