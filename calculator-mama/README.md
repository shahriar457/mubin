# Calculator Mama - WordPress Plugin

**Version:** 1.0.0  
**Text Domain:** calculator-mama  
**Unique Prefix:** cmama_

## Description

Calculator Mama is a comprehensive WordPress plugin that provides over 150 interactive calculators designed to transform your website into an organic traffic powerhouse. Each calculator is optimized for SEO and comes with built-in Schema.org markup to maximize search engine visibility.

## Features

### 🎯 Core Features

- **150+ Interactive Calculators** across 4 main categories
- **Built-in SEO Engine** with automatic Schema.org JSON-LD markup
- **Gutenberg Block** with live preview and SEO content fields
- **Universal Shortcode Support** for Classic Editor, Elementor, and other page builders
- **Global Styling System** to match any WordPress theme
- **Modular Architecture** for optimal performance

### 📊 Calculator Categories

1. **Financial Calculators**
   - Mortgage Calculator
   - Loan Calculator
   - Compound Interest Calculator
   - Investment Calculator
   - Retirement Calculator
   - ROI Calculator

2. **Health & Fitness Calculators**
   - BMI Calculator
   - BMR Calculator
   - Calorie Calculator
   - Body Fat Calculator
   - Pregnancy Calculator
   - Ideal Weight Calculator

3. **Math Calculators**
   - Percentage Calculator
   - Fraction Calculator
   - Scientific Calculator
   - Square Root Calculator
   - Exponent Calculator
   - Average Calculator

4. **Other Calculators**
   - Age Calculator
   - Date Difference Calculator
   - Tip Calculator
   - GPA Calculator
   - Grade Calculator
   - Time Calculator

### 🎨 Appearance Customization

- **Color Settings**
  - Primary Color
  - Button Color
  - Input Background
  - Result Background

- **Layout Controls**
  - Border Radius (0-50px)
  - Padding (0-100px)
  - Font Family

- **Custom CSS** field for advanced styling

### 🔍 SEO Features

- **Dynamic Content Generation**
  - Introduction section for context
  - Usage instructions
  - FAQ section with repeater fields

- **Automatic Schema.org Markup**
  - WebApplication
  - MedicalRiskCalculator
  - MathSolver
  - And more...

## Installation

1. Upload the `calculator-mama` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to "Calculator Mama" in the admin menu
4. Activate the calculators you want to use
5. Customize appearance settings if needed
6. Add calculators to your pages using Gutenberg blocks or shortcodes

## Usage

### Using Gutenberg Block

1. Add a new block to your post/page
2. Search for "Calculator Mama"
3. Select the calculator from the dropdown
4. Add SEO-rich content (introduction, instructions, FAQs)
5. Publish!

### Using Shortcodes

```
[cmama_calculator slug="mortgage-calculator"]
[cmama_calculator slug="bmi-calculator"]
[cmama_calculator slug="percentage-calculator"]
```

Find the shortcode for any calculator in the Calculator Library.

## File Structure

```
calculator-mama/
├── calculator-mama.php           # Main plugin file
├── includes/                      # Core functionality
│   ├── class-calculator-base.php
│   ├── class-calculator-registry.php
│   ├── class-calculator-manager.php
│   ├── class-admin-settings.php
│   ├── class-frontend-renderer.php
│   ├── class-shortcode.php
│   ├── class-seo-engine.php
│   └── class-gutenberg-block.php
├── calculators/                   # Calculator modules
│   ├── financial/
│   ├── health/
│   ├── math/
│   └── other/
├── admin/                         # Admin interface
│   ├── css/
│   ├── js/
│   └── views/
├── assets/                        # Frontend assets
│   ├── css/
│   └── js/
└── languages/                     # Translations
    └── calculator-mama.pot
```

## Development

### Adding New Calculators

1. Create a new calculator class extending `CMAMA_Calculator_Base`
2. Place it in the appropriate category folder
3. Implement required methods:
   - `render()` - HTML output
   - `get_javascript()` - Calculator logic
4. The calculator will automatically appear in the library

Example:

```php
class CMAMA_Custom_Calculator extends CMAMA_Calculator_Base {
    public function __construct() {
        $this->slug = 'custom-calculator';
        $this->name = __('Custom Calculator', 'calculator-mama');
        $this->description = __('Description here', 'calculator-mama');
        $this->category = 'other';
        $this->schema_type = 'WebApplication';
        
        CMAMA_Calculator_Registry::instance()->register($this);
    }
    
    public function render($atts = array()) {
        // Return calculator HTML
    }
    
    public function get_javascript() {
        // Return calculator JavaScript
    }
}

new CMAMA_Custom_Calculator();
```

### Coding Standards

- Follows WordPress Coding Standards
- PHP 7.4+ compatible
- Full PHPDoc comments
- Sanitization and escaping for all user inputs
- Nonce verification for AJAX requests

## Security

- Input sanitization on all user inputs
- Output escaping on all displayed data
- Nonce protection for AJAX operations
- Capability checks for admin functions
- Prepared statements for database queries
- XSS and CSRF protection

## Performance

- **Modular Loading**: Only loads assets for active calculators
- **Conditional Scripts**: Calculator-specific JavaScript loaded only when needed
- **Single Database Option**: All settings stored in one array
- **No External Dependencies**: Pure WordPress solution
- **Optimized CSS**: Minimal footprint with CSS variables

## Browser Compatibility

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## Support

For support, feature requests, or bug reports, please visit:
- GitHub: [Calculator Mama Repository]
- WordPress.org Support Forums

## Changelog

### 1.0.0 - 2024-10-18

- Initial release
- 24 fully functional calculators
- Gutenberg block support
- Shortcode support
- SEO engine with Schema.org markup
- Global styling system
- Admin dashboard and library management
- Full internationalization support

## License

GPL v2 or later

Copyright (C) 2024 Calculator Mama

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

## Credits

Developed by Calculator Mama Team
