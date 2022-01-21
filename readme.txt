=== WooCommerce Product Stock Alert ===

Contributors: wcmp
Tags: wordpress, woocommerce, e-commerce, shop, stock, out of stock, in stock, stock alert, alert email, stock alert email
Requires at least: 4.4
Tested up to: 5.8.2
Requires PHP: 5.6
Stable tag: 1.7.4
Donate link: http://wc-marketplace.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Alert your customer when a product of her choice is available again. 

== Description ==
Never miss a sale now! This plugin lets prospective buyers subscribe to a product even when it is out of stock. Once subscribed, customers will get an email notification when the product becomes available. On the admin side – you may view several interested customers for any out of stock product.

= Features =

*   WooCommerce Product Stock Alert Plugin creates a form with an out of stock product. Interested customers can register with their email addresses.
*   When a customer subscribes to any out of stock product on your site, an email will be sent to the admin with the customer email id and interesting product.
*   This plugin also creates a field to the inventory of the edit product page to show various interested customers. This will let you decide how much stock you want to add further.
*   As soon as the product becomes available, an alert email will be sent to the interested persons.
*   This is an extremely lightweight plugin; it doesn’t hamper the speed of your website and ensures a smooth transition.
*   This killer plugin is easy to set up, use and install. You need not be a coding or developing expert to understand how the plugin works. Just install and let it do the rest.
*   You can customize the form heading message, button text, colour, hover colour, alert message after form submission, etc. from the plugin settings page.
*   You can customize the email heading, email subject which will be sent to customers from WooCommerce email settings.
*   WooCommerce Product Stock Alert Plugin will work if the product Backorder option is enabled.
*   You can change the default Stock Alert form position and display the stock alert form on the product page by using Shortcode [display_stock_alert_form].
*   Export list of subscribers via Wordpress’s Tools settings
*   Customers can unsubscribe from a product to which he/she already subscribed.
*   Double Opt In setting sends customers a confirmation mail inquiring if they would like to confirm their subscription to an Out of Stock product. To enable this feature you need to Install **[WCMp Vendor Stock Alert](https://wc-marketplace.com/product/wcmp-vendor-stock-alert/)**

= Compatibility =
*   WordPress 5.5+
*   WooCommerce 4.4+
*   Multilingual Support is included with the plugin and is fully compatible with WPML.


= Configurable =

WooCommerce Product Stock Alert has provided customizable email structure. Admin can customize email Header as well as email Subject from WooCommerce settings panel.

= Feedback =

Thanks heaps for trying this plugin. I hope it could serve your purpose. If you find this plugin is helpful, appreciate us by giving a 5/5 star and feel free to comment as well as suggest additional features. If you find the plugin is buggy, please do mention the reason and we will add or change options and fix bugs. For more information and instructions on this plugin please visit www.wc-marketplace.com. 

== Installation ==
1.  Upload your plugin folder to the '/wp-content/plugins' directory.
2.  Activate the plugin through the 'Plugins' menu in WordPress.
3.  Goto plugin settings and "Enable Stock Alert".
4.  Customize email heading, email subject from WooCommerce email settings.
5.  All done. Just wait and watch how many customers are interested with an out of stock product of your Woostore.

== Frequently Asked Questions ==
= Does this plugin work with newest WP version and also older versions? =
Yes, this plugin works with WordPress 3.6 or later.

= Does this plugin work with newest WooCommerce version and also older versions? =
Yes, this plugin is compatible with WooCommerce 2.2 or later.

= Does this plugin work with all types of theme =
Yes, this plugin is compatible with all latest version of WooCommerce themes.

= Does this plugin work with WooCommerce Subscription Product =
Yes, this plugin works with WooCommerce Subscription Product.

== Screenshots ==
1. Stock alert plugin settings panel
2. Stock alert admin email settings panel
3. Stock alert customer email settings panel
4. Stock alert customer confirmation email settings panel
5. An out of stock simple product with textarea to subscribe
6. An out of stock variable product with textarea to subscribe
7. An email sent to admin when a customer subscribed the product
8. Number of customers interested with out of stock products
9. Individual product edit page for simple product with number of interested persons
10. Individual product edit page for variable product when no customer is interested
11. Individual product edit page for variable product with number of interested persons
12. An email sent to the customer when the product becomes available
13. Plugin settings to customize stock alert form
14. A customized stock alert form
15. Export subscribers' list

== Changelog ==

= 1.7.4 - 2022-01-21 =
* Added - WordPress 5.8.2 compatibility.
* Added - WooCommerce 6.1.1 compatibility.
* Added - PHP 8.0.10 compatibility.
* Fixed - Qtip issues.
* Fixed - Fatal errors due to Uncaught TypeError #41.
* Fixed - Jquery Issues #40.
* Dev - Added `Woocommerce_product_stock_alert_form` action.
* Dev - Added `Woocommerce_product_stock_alert_form_additional_fields` action.
* Dev - Added `Woocommerce_product_stock_alert_form_process_additional_fields` action.
* Updated - Language files.

= 1.7.3 - 2021-08-26 =
* Fixed - HTML with product name in Stock alert mail.
* Fixed - Unsubscribe button text.
* Added - Settings for remove admin email Id and add additional email Id.
* Added - vendor email with Stock alert admin mail.
* Added - Double Optin setting modification for WCMp vendor stock alert.
* Added - Compatibility of WordPress 5.8.
* Added - Compatibility of Woocommerce 5.6.0.
* Updated - Language files.

= 1.7.2 - 2021-05-28 =
* Added - The dynamic setting for button font size.
* Added - WooCommerce 5.3.0 compatibility.
* Added - WordPress 5.7.2 compatibility.
* Enhancement - HTML Markup improvements.
* Enhancement - Escaping & securing output.
* Updated : Language files.

= 1.7.1 - 2020-09-28 =
* Fixed - Remove Product Subscribers #12.
* Added - WooCommerce 4.5.2 compatibility.
* Updated : Language files.

= 1.7.0 - 2020-08-29 =
* Fixed - Alert box display for backorder products also.
* Fixed - Product - Variations name added for variable product in export file.
* Added - WordPress 5.5 compatibility.
* Added - WooCommerce 4.4.1 compatibility.

= 1.6.1 =
* Woocommerce 3.8+ compatibility added
* Fix : Stock alert mail price issue fix #1.

= 1.6.0 =
* Woocommerce 3.6+ compatibility added
* Fix : Static Function issue.
* Enhancement : Product price with / without tax support via email.
* Enhancement : Subscriber list export feature.

= 1.5.2 =
* Fix : Remove subscriber for variable product.
* Fix : Email issue for variation updated via `Bulk Stock Management`.
* Fix : Undefined product method notice.

= 1.5.1 =
* Fix : Simple product out of stock issue.

= 1.5.0 =
* Fix : Minor script issue.

= 1.4.9 =
* Fix : Stock alert function issue.
* Updated : Language files.

= 1.4.8 =
* Added : Product bulk remove subscribers added.
* Updated : Language files.
* Fix : Woocommerce backorders issue.
* Fix : Fixed some minor issues.

= 1.4.7 =
* Added : Woocommerce product inventory settings added with stock alert.
* Added : Stock alert email subject and heading customizable.
* Fix : Fixed some minor issues.

= 1.4.6 =
* Fix : Fixed alert box issues for variations.

= 1.4.5 =
* Added: Support for Loco Translate plugin.
* Added: POT file in language folder
* Updated: Plugin text domain from product_stock_alert to woocommerce-product-stock-alert
* Updated: Language files.
* Woocommerce 3.0.9 compatibility added 

= 1.4.4 =
* Fix : Fixed some backend settings option issues.

= 1.4.3 =
* Added : Product variation details added into stock alert emails.
* Fix : Fixed some minor issues.

= 1.4.2 =
* Fix : Fixed stock alert shortcode issue.

= 1.4.1 =
* Fix : Fixed product alert form appearance issue.
* Fix : Fixed backend product Interested Person(s) column sortable issue.

= 1.4.0 =
* Woocommerce 3.0+ compatibility added
* Added : Number of Interested persons shown on product page with backend settings.
* Added : Product Interested Person(s) column sortable functionalities.
* Fix : Fixed backend woocommerce order issue.
* Fix : Fixed variation product alert form and its issues.

= 1.3.2 =
* Added : Added some CSS/JS on buttons call. 
* Fix : Fixed stock alert fields disappearance on error message display behavior.

= 1.3.1 =
* Fix : Fixed some fatal errors, now plugin will work with WooCommerce 2.6+..

= 1.3.0 =
* Features : Customer can unsubscribe a product which he/she already subscribed.
* Feature : A confirmation mail will be sent to subscriber after subscribe a product.
* Feature : [display_stock_alert_form] => Shortcode to display stock alert form in product page.
* Feature : An export option is added inside 'Tools->WC Stock Alert Export' to export subscribers' list.

= 1.2.0 =
*   Features : New options added into plugin settings panel to customize alert message after form submission.
*   Fix : Fixed some issues, now plugin will work with WooCommerce 2.4+.

= 1.1.1 =
*   Stock alert form will be displayed on backorder product properly.
*   Sending alert email automatically when backorder is enabled and in-stock is selected issue has been resolved.

= 1.1.0 =
*   Works with "WooCommerce Subscription Products".
*   Now this plugin works with Backorders even "in-stock" is selected.

= 1.0.3 =
*   Plugin settings panel is added to customize stock alert form.
*   Now alert email will be send as soon as product becomes in-stock.
*   Now plugin will work with Backorders.

= 1.0.2 =
*   Fixed minor bug and compatible with latest WooCommerce.

= 1.0.1 =
*   Fixed some issue and now plugin is working with Variable product.

= 1.0.0 =
*   Initial release.


== Upgrade Notice ==
= 1.4.9 =
* Fix : Stock alert function issue.
* Updated : Language files.

= 1.4.8 =
* Added : Product bulk remove subscribers added.
* Updated : Language files.
* Fix : Woocommerce backorders issue.
* Fix : Fixed some minor issues.

= 1.4.7 =
* Added : Woocommerce product inventory settings added with stock alert.
* Added : Stock alert email subject and heading customizable.
* Fix : Fixed some minor issues.

= 1.4.6 =
* Fix : Fixed alert box issues for variations.

= 1.4.5 =
* Added: Support for Loco Translate plugin.
* Added: POT file in language folder
* Updated: Plugin text domain from product_stock_alert to woocommerce-product-stock-alert
* Updated: Language files.
* Woocommerce 3.0.9 compatibility added 

= 1.4.4 =
* Fix : Fixed some backend settings option issues.

= 1.4.3 =
* Added : Product variation details added into stock alert emails.
* Fix : Fixed some minor issues.

= 1.4.2 =
* Fix : Fixed stock alert shortcode issue.

= 1.4.1 =
* Fix : Fixed product alert form appearance issue.
* Fix : Fixed backend product Interested Person(s) column sortable issue.

= 1.4.0 =
* Woocommerce 3.0+ compatibility added
* Added : Number of Interested persons shown on product page with backend settings.
* Added : Product Interested Person(s) column sortable functionalities.
* Fix : Fixed backend woocommerce order issue.
* Fix : Fixed variation product alert form and its issues.

= 1.3.2 =
* Added : Added some CSS/JS on buttons call. 
* Fix : Fixed stock alert fields disappearance on error message display behavior.

= 1.3.1 =
* Fix : Fixed some fatal errors, now plugin will work with WooCommerce 2.6+..

= 1.3.0 =
* Features : Customer can unsubscribe a product which he/she already subscribed.
* Feature : A confirmation mail will be sent to subscriber after subscribe a product.
* Feature : [display_stock_alert_form] => Shortcode to display stock alert form in product page.
* Feature : An export option is added inside 'Tools->WC Stock Alert Export' to export subscribers' list.

= 1.2.0 =
*   Features : New options added into settings panel to customize alert message after form submission.
*   Fix : Fixed some issues, now plugin will work with WooCommerce 2.4+.

= 1.1.1 =
*   Stock alert form will be displayed on backorder product properly.
*   Sending alert email automatically when backorder is enabled and in-stock is selected issue has been resolved.

= 1.1.0 =
*   Works with "WooCommerce Subscription Products".
*   Now this plugin works with Backorders even "in-stock" is selected.

= 1.0.3 =
*   Plugin settings panel is added to customize stock alert form.
*   Now alert email will be send as soon as product becomes in-stock.
*   Now plugin will work with Backorders.

= 1.0.2 =
*   Fixed minor bug and compatible with latest WooCommerce.

= 1.0.1 =
*   Fixed some issue and now plugin is working with Variable product.

= 1.0.0 =
*   Initial release.
