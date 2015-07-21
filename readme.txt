=== CleverReach Extension ===

Contributors: hofmannsven
Tags: cleverreach, email, newsletter, sign-up, opt-in, form, ajax

Requires at least: 4.0
Tested up to: 4.2.2
Stable tag: 0.1.0

License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.txt

Simple interface for CleverReach newsletter software using the official CleverReach SOAP API.


== Description ==

The CleverReach Extension for WordPress provides an easy way to embed your CleverReach](http://www.cleverreach.com/) sign-up form anywhere on your website.

= Features =
* Easily embed your CleverReach sign-up form anywhere on your website
* Double opt-in according to your CleverReach configuration
* Smooth form submission using Ajax (no page reload)
* Optional: Customize your form and error messages via filters (Check the [Wiki](https://github.com/hofmannsven/cleverreach-extension/wiki) for available filters)

= Coming soon =
* Unsubscribe form
* Support for WordPress Widgets
* PHPUnit Tests

= Languages =
* English
* German

= Integrations =
* Coming soon: Visual Composer
* Coming soon: Contact Form 7


== Installation ==

= Requirements =
* PHP 5.3.0 or newer
* PHP SOAP extension
* CleverReach API key

= Using WP-CLI =
1. Install and activate: `wp plugin install cleverreach-extension --activate`

= Using Composer =
1. Install: `composer create-project hofmannsven/cleverreach-extension --stability=dev`
2. Activate the plugin on the plugin dashboard

= Using the WordPress dashboard =
1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'CleverReach Extension'
3. Click 'Install Now'
4. Activate the plugin on the plugin dashboard

= Using FTP =
1. Unzip the download package
2. Upload `cleverreach-extension` folder to your plugins directory
3. Activate the plugin on the plugin dashboard


== Support ==

If you find an issue, please [raise an issue](https://github.com/hofmannsven/cleverreach-extension/issues) on GitHub.


== Frequently Asked Questions ==

= Why would I use the API instead of the source code provided within my CleverReach account? =
Using the API will allow you to push and pull data from CleverReach.
This allows things like smooth form submission via Ajax and custom error handling.

= Is it secure? =
No customer data is stored within your WordPress database. 
We heavily rely on the security of CleverReach which is [tested and verified](http://www.cleverreach.com/security) according to German standards.

= Having problems with the PHP SOAP Extension? =
Check the [PHP SOAP wiki page](https://github.com/hofmannsven/cleverreach-extension/wiki/PHP-SOAP-Extension) for further information.

= How can I customize the sign-up form or the error messages? =
Check the [Wiki](https://github.com/hofmannsven/cleverreach-extension/wiki) for further information.

= I'm having issues getting the plugin to work what should I do? =
See [CleverReach Extension on Github](https://github.com/hofmannsven/cleverreach-extension) for a detailed rundown of common issues.

= Where can I get more information and support for this plugin? =
Visit [CleverReach Extension on Github](https://github.com/hofmannsven/cleverreach-extension)


== Changelog ==

= 0.1.0 =
* Initial release
