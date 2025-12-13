# Changelog

All notable changes to WooCommerce Quote System will be documented in this file.

## [1.5.1] - 2024-12-13

### Changed
- **BREAKING**: Renamed plugin from "Simple Quote for WC" to "WooCommerce Quote System"
- **BREAKING**: Changed plugin directory from `simple-quote-for-wc` to `woocommerce-quote-system`
- **BREAKING**: Updated text domain from `simple-quote-for-wc` to `woocommerce-quote-system`
- **BREAKING**: Changed CSS class prefix from `sqwc-` to `wcqs-`
- Updated GitHub repository URL to https://github.com/hwc0212/woocommerce-quote-system
- Updated plugin header information and metadata
- Improved code organization and documentation
- Updated language files with new text domain

### Migration Notes
- This is a major rename that requires manual migration
- Settings and data from the old plugin will need to be reconfigured
- CSS customizations using old class names will need to be updated

## [1.5.0] - 2024-12-13

### Added
- Email icon to quote button for visual consistency with WhatsApp button
- Customizable modal titles and subtitles in admin settings
- Enhanced mobile touch interactions and swipe gestures
- Comprehensive code documentation and PHPDoc comments

### Changed
- WhatsApp button now uses official styling with authentic green color (#25D366)
- Improved modal background to light gray for better visibility
- Enhanced responsive design for better mobile experience
- Optimized CSS by merging duplicate button styles
- Refactored admin callbacks to reduce code duplication by 90%

### Fixed
- Modal background color now properly displays as light gray instead of black
- Improved form plugin compatibility across different themes
- Enhanced security with better input validation and sanitization

## [1.4.0] - 2024-12-12

### Added
- Individual toggle switches for each plugin feature
- Master switch to enable/disable all functionality
- Comprehensive mobile optimization with touch-friendly interactions
- Enhanced form plugin compatibility beyond Contact Form 7
- Proper accessibility features and keyboard navigation
- Advanced error handling and input validation

### Changed
- Improved admin interface with better organization
- Enhanced modal design with smooth animations
- Better mobile responsiveness across all screen sizes
- Optimized JavaScript for better performance

### Fixed
- Form submission handling across different form plugins
- Mobile modal positioning and scroll behavior
- Touch interaction conflicts on mobile devices

## [1.3.0] - 2024-12-11

### Added
- Multilingual support with English as default language
- Chinese (Simplified) translation included
- WhatsApp integration with automatic product information sharing
- Enhanced modal design with better animations
- Form submission success handling for multiple form plugins
- Improved mobile touch interactions and swipe-to-close

### Changed
- Default language changed from Chinese to English
- Enhanced responsive design for better mobile experience
- Improved modal styling and user experience
- Better form plugin compatibility

### Fixed
- Modal display issues on various themes
- Mobile scrolling and touch interaction problems
- Form plugin integration edge cases

## [1.2.0] - 2024-12-10

### Added
- Support for any form plugin shortcodes (not just Contact Form 7)
- Enhanced responsive design for all device sizes
- Improved modal functionality with better animations
- Proper sanitization and security measures throughout

### Changed
- Expanded form plugin compatibility beyond Contact Form 7
- Enhanced modal styling and animations
- Improved code organization and structure
- Better error handling and validation

### Fixed
- Form shortcode processing issues
- Modal positioning on different screen sizes
- CSS conflicts with various themes

## [1.1.0] - 2024-12-09

### Added
- WhatsApp consultation button with customizable number
- Enhanced modal styling with smooth animations
- Improved mobile compatibility and touch interactions
- Form validation and error handling
- Better integration with WooCommerce themes

### Changed
- Enhanced user interface design
- Improved modal animations and transitions
- Better mobile responsiveness
- Optimized JavaScript performance

### Fixed
- Modal display issues on mobile devices
- Form submission handling edge cases
- CSS conflicts with popular themes

## [1.0.0] - 2024-12-08

### Added
- Initial release of Simple Quote for WC
- Basic quote system functionality to replace WooCommerce shopping cart
- Hide product prices across the store
- Replace "Add to Cart" buttons with "Get Quote" buttons
- Quote modal with Contact Form 7 integration
- Hide shopping cart, checkout, and payment functionality
- Remove inventory management and shipping options
- Basic responsive design for mobile devices
- Chinese language support as default

### Features
- Transform WooCommerce into a quote-based system
- Professional quote modal with form integration
- Mobile-friendly responsive design
- Easy configuration through WordPress admin
- Seamless integration with existing WooCommerce stores

---

## Version History Summary

- **1.5.1**: Major rename to "WooCommerce Quote System" with updated branding
- **1.5.0**: Enhanced styling, mobile optimization, and code improvements
- **1.4.0**: Individual feature toggles and comprehensive mobile optimization
- **1.3.0**: Multilingual support and WhatsApp integration
- **1.2.0**: Expanded form plugin support and enhanced security
- **1.1.0**: WhatsApp button and improved mobile experience
- **1.0.0**: Initial release with core quote system functionality

## Upgrade Notes

### From 1.5.0 to 1.5.1
This is a major rename that changes the plugin identity. Manual migration is required:
1. Export your current settings if needed
2. Deactivate the old "Simple Quote for WC" plugin
3. Install the new "WooCommerce Quote System" plugin
4. Reconfigure settings as needed
5. Update any custom CSS that uses old class names (sqwc- → wcqs-)

### From 1.4.x to 1.5.0
- Settings are preserved automatically
- New modal customization options available in admin
- Enhanced mobile experience with improved touch interactions
- WhatsApp button styling updated to official colors

### From 1.3.x to 1.4.0
- All existing settings are preserved
- New individual feature toggles available
- Enhanced mobile optimization automatically applied
- Improved form plugin compatibility

### General Upgrade Guidelines
- Always backup your site before upgrading
- Test the plugin on a staging site first
- Review settings after upgrade to take advantage of new features
- Check mobile experience after major updates