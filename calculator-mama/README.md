# Calculator Mama - WordPress Plugin

**Version:** 1.0.0  
**Text Domain:** calculator-mama  
**Unique Prefix:** cmama_

## Overview

Calculator Mama is a comprehensive WordPress plugin that provides a library of over 150 interactive calculators designed to transform your website into a powerful content marketing and organic traffic generation engine. Each calculator is optimized for search engines and built with accessibility and user experience in mind.

## Features

### 🧮 Extensive Calculator Library
- **150+ Interactive Calculators** across 4 main categories:
  - **Financial:** Mortgage, Loan, Investment, Tax, Retirement calculators
  - **Health & Fitness:** BMI, Calorie, BMR, Pregnancy, Pace calculators
  - **Math:** Scientific, Percentage, Fraction, Statistics calculators
  - **Other:** Age, Date, GPA, Conversion, Tip calculators

### 🎨 Complete Customization
- **Global Styling System** with color pickers, font selectors, and layout controls
- **Custom CSS Support** for advanced styling
- **Theme Integration** that inherits your theme's design
- **Responsive Design** that works on all devices

### 📝 Built-in SEO Engine
- **Automatic Schema.org Markup** generation for enhanced search visibility
- **Dynamic Content Fields** for introductions, instructions, and FAQs
- **Structured Data** optimized for featured snippets
- **SEO-Friendly URLs** and meta tags

### 🔧 Developer-Friendly
- **Gutenberg Block** with live preview and SEO fields
- **Universal Shortcode** support for all page builders
- **Modular Architecture** with conditional asset loading
- **WordPress Coding Standards** compliant
- **Extensive Hook System** for customization

### 🛡️ Security & Performance
- **Input Sanitization** and output escaping
- **Nonce Protection** for all forms
- **Prepared Database Queries** to prevent SQL injection
- **Conditional Asset Loading** for optimal performance
- **Capability Checks** for proper access control

## Installation

1. Upload the `calculator-mama` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to 'Calculator Mama' in your admin menu to get started

## Quick Start

### 1. Activate Calculators
- Go to **Calculator Mama > Library**
- Toggle on the calculators you want to use
- Use the search and filter options to find specific calculators

### 2. Customize Appearance
- Go to **Calculator Mama > Appearance**
- Adjust colors, fonts, and spacing to match your brand
- Add custom CSS for advanced styling
- Preview changes in real-time

### 3. Add to Your Content

#### Using Gutenberg Block
1. Add a new block and search for "Calculator Mama"
2. Select your calculator from the dropdown
3. Add SEO content (introduction, instructions, FAQs)
4. Publish your page

#### Using Shortcode
```
[cmama_calculator slug="mortgage-calculator"]
[cmama_calculator slug="bmi-calculator" title="Custom Title"]
```

## Available Calculators

### Financial Calculators
- Mortgage Calculator
- Loan Payment Calculator
- Investment Calculator
- Retirement Calculator
- Tax Calculator
- And many more...

### Health & Fitness Calculators
- BMI Calculator
- Calorie Calculator
- BMR Calculator
- Pregnancy Calculator
- Body Fat Calculator
- And many more...

### Math Calculators
- Percentage Calculator
- Scientific Calculator
- Fraction Calculator
- Statistics Calculator
- Geometry Calculator
- And many more...

### Other Calculators
- Age Calculator
- Date Calculator
- GPA Calculator
- Unit Converter
- Tip Calculator
- And many more...

## Plugin Structure

```
calculator-mama/
├── admin/                      # Admin interface files
│   ├── class-admin.php        # Main admin class
│   ├── class-settings.php     # Settings management
│   └── views/                 # Admin view templates
├── assets/                    # CSS and JavaScript files
│   ├── css/                   # Stylesheets
│   └── js/                    # JavaScript files
├── blocks/                    # Gutenberg block files
├── calculators/               # Calculator implementations
│   ├── financial/             # Financial calculators
│   ├── health/                # Health & fitness calculators
│   ├── math/                  # Math calculators
│   └── other/                 # Other calculators
├── includes/                  # Core plugin files
│   ├── class-calculator-base.php      # Base calculator class
│   ├── class-calculator-registry.php  # Calculator registry
│   ├── class-seo-engine.php          # SEO functionality
│   ├── class-asset-manager.php       # Asset management
│   ├── class-ajax-handlers.php       # AJAX handlers
│   └── functions.php                  # Helper functions
├── languages/                 # Translation files
└── templates/                 # Template files
```

## Customization

### Adding Custom Calculators

Create a new calculator by extending the base class:

```php
class My_Custom_Calculator extends CMAMA_Calculator_Base {
    protected function init() {
        $this->slug = 'my-custom-calculator';
        $this->name = __('My Custom Calculator', 'textdomain');
        $this->description = __('Description of my calculator', 'textdomain');
        $this->category = 'other';
        
        $this->fields = array(
            'input1' => array(
                'type' => 'number',
                'label' => __('Input 1', 'textdomain'),
                'required' => true
            )
        );
    }
    
    public function calculate($inputs) {
        // Your calculation logic here
        return array(
            'result' => $inputs['input1'] * 2
        );
    }
}

// Register the calculator
add_action('cmama_register_calculators', function($registry) {
    $registry->register_calculator('my-custom-calculator', new My_Custom_Calculator());
});
```

### Styling Calculators

Use CSS custom properties for easy theming:

```css
.cmama-calculator {
    --cmama-primary-color: #your-color;
    --cmama-button-color: #your-button-color;
    --cmama-border-radius: 8px;
    --cmama-padding: 24px;
}
```

### Hooks and Filters

The plugin provides numerous hooks for customization:

```php
// Modify calculator output
add_filter('cmama_calculator_output', 'my_calculator_output_filter', 10, 3);

// Add custom calculator fields
add_action('cmama_calculator_fields', 'my_custom_fields');

// Modify SEO schema
add_filter('cmama_schema_data', 'my_schema_filter', 10, 2);
```

## Requirements

- **WordPress:** 5.0 or higher
- **PHP:** 7.4 or higher
- **MySQL:** 5.6 or higher

## Browser Support

- Chrome (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Edge (latest 2 versions)
- Internet Explorer 11+

## Accessibility

Calculator Mama is built with accessibility in mind:
- WCAG 2.1 AA compliant
- Keyboard navigation support
- Screen reader friendly
- High contrast mode support
- Reduced motion support

## Performance

- **Conditional Loading:** Only loads assets for calculators in use
- **Optimized Database Queries:** Minimal database impact
- **Caching Friendly:** Works with all major caching plugins
- **CDN Compatible:** All assets can be served from CDN

## Security

- Input sanitization and output escaping
- Nonce verification for all forms
- Capability checks for admin functions
- Prepared database statements
- Regular security audits

## Support

For support, feature requests, or bug reports:
- **Documentation:** [Plugin Documentation](https://calculatormama.com/docs)
- **Support Forum:** [WordPress.org Support](https://wordpress.org/support/plugin/calculator-mama)
- **GitHub Issues:** [Report Issues](https://github.com/calculator-mama/calculator-mama)

## Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details.

## License

This plugin is licensed under the GPL v2 or later.

## Changelog

### 1.0.0
- Initial release
- 150+ calculators across 4 categories
- Gutenberg block with SEO features
- Complete admin interface
- Responsive design system
- Built-in SEO engine
- Accessibility compliance

---

**Calculator Mama** - Transform your website into a powerful content marketing engine with interactive calculators that engage visitors and boost your search rankings.