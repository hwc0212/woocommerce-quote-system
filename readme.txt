# WooCommerce Quote System

## 基本信息
- **贡献者**：hwc0212
- **捐赠链接**：https://github.com/hwc0212/woocommerce-quote-system
- **标签**：woocommerce, quote system, product inquiry, contact form, whatsapp integration
- **最低要求版本**：WordPress 5.6
- **测试通过版本**：WordPress 6.5
- **PHP 版本要求**：7.4
- **稳定版本**：2.2.1
- **许可证**：GPLv2 或更高版本
- **许可证 URI**：http://www.gnu.org/licenses/gpl-2.0.html
- **版本**：2.2.1
- **作者**：huwencai.com

## 插件描述
这个插件可以增强你的 WooCommerce 商店，提供专业的报价请求功能和即时的 WhatsApp 沟通功能。

### 主要功能
- 在产品页面添加专业的询价按钮。
- 集成 WhatsApp 即时沟通功能。
- 可定制的询价弹窗表单。
- 响应式设计，适配所有设备。
- 智能表单验证与提交处理。
- 与 WooCommerce 产品数据深度集成。
- 支持 Dashicons 图标系统。
- 灵活的显示条件控制。

### 功能亮点
1. **双模式沟通**：客户可以选择填写询价表单或直接通过 WhatsApp 联系。
2. **智能防骚扰**：自动记录用户提交频率，防止垃圾信息。
3. **移动优先设计**：完美适配手机端操作流程。
4. **营销集成**：自动携带产品信息到 WhatsApp 消息。
5. **性能优化**：异步加载机制，不影响页面加载速度。

## 安装方法

### 自动安装
1. 前往 WordPress 后台的插件页面。
2. 搜索 “WooCommerce Quote System”。
3. 点击安装，然后激活插件。

### 手动安装
1. 下载插件的 zip 文件。
2. 通过 WordPress 后台的插件上传界面进行安装。
3. 或者将下载的 zip 文件解压到 `wp-content/plugins` 目录。
4. 激活插件。

## 配置步骤
1. 进入 `WooCommerce > Quote Settings`。
2. 配置 WhatsApp 号码和表单短代码。
3. 选择需要显示按钮的产品分类。
4. 自定义按钮样式和提示文字。

## 常见问题解答

### 按钮不显示可能的原因
1. 未启用 WooCommerce。
2. 未配置 WhatsApp 号码或表单短代码。
3. 当前产品不在指定分类中。
4. 主题 CSS 冲突（尝试添加 `!important`）。

### 如何自定义表单样式
推荐使用以下 CSS 类进行样式覆盖：
- `.wqs-form-container`
- `.wqs-input-field`
- `.wqs-submit-button`
- `.wqs-error-message`

### 支持哪些 WhatsApp 格式
支持国际格式号码（需包含国家代码），例如：
- `+8613123456789`
- `008613123456789`

### 如何添加自定义字段
1. 通过表单生成插件创建新表单。
2. 获取新表单的短代码。
3. 在插件设置中替换原有短代码。

## 截图
1. 产品页按钮展示截图。
2. 询价表单弹窗界面。
3. 后台设置页面。
4. 移动端适配效果。

## 开发者文档
- **GitHub 仓库**：[https://github.com/hwc0212/woocommerce-quote-system](https://github.com/hwc0212/woocommerce-quote-system)

### 可用钩子
- `wqs_before_button_container`
- `wqs_after_form_submission`
- `wqs_whatsapp_message_filter`

### 示例代码
添加自定义按钮容器样式：
```php
add_filter('wqs_button_container_class', function($classes) {
    $classes[] = 'custom-button-style';
    return $classes;
});
```

## 支持
请通过 GitHub Issues 提交问题：[https://github.com/hwc0212/woocommerce-quote-system/issues](https://github.com/hwc0212/woocommerce-quote-system/issues)
