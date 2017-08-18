=== WooCommerce Products Carousel all in one ===
Contributors: teastudio.pl
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2SQA4FL25Y73W
Tags: carousel, slider, woocommerce carousel, woocommerce slider, products carousel, products slider, woocommerce products carousel, woocommerce products slider, woocommerce owl carousel, owl carousel
Requires at least: 3.6
Tested up to: 4.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WooCommerce Products Carousel all in one is a widget and shortcode generator to show newest, featured, bestsellers and many more products in Carousel
== Description ==

**WooCommerce Products Carousel all in one** allows you inserting the product carousel (newest, featured, bestsellers, on sale and many more) from the store based on the WooCommerce. The plugin offers rich parameters of carousel display and entry information, and provides better support for mobile devices.
This plugin uses [OWL Carousel](http://www.owlcarousel.owlgraphic.com/) in new version 2.0.0-beta.2.4.

= Now available in the following Languages: =

* English (en_EN) - 100% completed
* Polish (pl_PL) - 100% completed
* French (fr_FR) - 51% completed
* Swedish (sv_SE) - 50% completed
* Danish (da_DK) - 66% completed
* Portuguese, Brazilian (PT_BR) - 100% completed


If you need other translation or you would like to create some, please visit [crowdin.com](https://crowdin.com/project/woocommerce-products-carousel-all-in-one) project page.



For more information, please visit [PLUGIN HOMEPAGE](http://www.teastudio.pl/en/product/woocommerce-products-carousel-all-in-one/).

= DEMO =
You can see the plugin in action on [DEMO PAGE](http://wordpress.teastudio.pl/category/woocommerce-products-carousel-all-in-one/)


I will be grateful for opinions and reviews.

== Translations: ==

* English - by Marcin Gierada (100%)
* Polish - by Marcin Gierada (100%)
* French - thanks to tunteam (51%)
* Swedish - thanks to Imran Khalid (50%)
* Danish - thanks to Daniel Christiansen (66%)
* Portuguese, Brazilian - thanks to Cleber Paiva de Souza (100%)


== Installation ==

1. Upload plugin either via the yours WordPress installation, or by uploading to the /wp-content/plugins/ by FTP client
2. Install the plugin through the 'Plugins' menu in WordPress.
3. Activate "WooCommerce Products Carousel all in one" in the "Plugins" using the "Activate" link.
4. Go to the plugin settings page in the "Settings" menu.

== Frequently Asked Questions ==
If you've got any questions, don't hesitate to ask.

= I made a update from a previous version. What now? =

* If you're using on your website shortcode from this plugin, the best way is to delete old code and generate it again.
* If you're using widget, you must update its options.
* **Go to the plugin settings page and see if there are any notifications**


= How can I add custom theme? =

1. Now, you can add your custom stylesheet in yours theme directory.
2. If you don't have "css" folder in your WordPress theme, you must create it.
3. Then, in this folder create another - named "woocommerce_products_carousel_all_in_one" and now you can move custom stylesheets in there :)

Folders tree should looks like this:
`/themes/my_wordpress_theme/css/woocommerce_products_carousel_all_in_one/custom.css`


= How can I use custom actions or filters? =
From 1.1.0 version you can use your own actions and filters to overwrite values or html code.


Lists of actions:

* woocommerce_products_carousel_all_in_one_before_item_content (1 parameter - $params)
* woocommerce_products_carousel_all_in_one_after_item_content (1 parameter - $params)


Lists of filters:

* woocommerce_products_carousel_all_in_one_query (1 parameter - $value)
* woocommerce_products_carousel_all_in_one_item_featured_image_placeholder (1 parameter - $value)
* woocommerce_products_carousel_all_in_one_item_featured_image (2 parameters - $value, $params)
* woocommerce_products_carousel_all_in_one_item_title (2 parameters - $value, $params)
* woocommerce_products_carousel_all_in_one_item_categories (2 parameters - $value, $params)
* woocommerce_products_carousel_all_in_one_item_description (2 parameters - $value, $params)
* woocommerce_products_carousel_all_in_one_item_price (2 parameters - $value, $params)
* woocommerce_products_carousel_all_in_one_item_tags (2 parameters - $value, $params)
* woocommerce_products_carousel_all_in_one_item_buttons (2 parameters - $value, $params)

* woocommerce_products_carousel_all_in_one_template (2 parameters - $value, $params)


Variable **$params** includes all plugin's values and other variables that are required to display.


e.g:
To overwrite html of the title, you can create function in your functions.php file:
[See "Overwrite title by filters from WooCommerce Products Carousel all in one"](http://pastebin.com/kcvr0cnp)


For more info visit [WordPress Function Reference/add filter](https://codex.wordpress.org/Function_Reference/add_filter)



== Screenshots ==
1. Widget configuration
2. Shortcode generator
3. WYSIWYG button
4. Settings page
5. Example of usage

== Changelog ==
= 1.2.9 =
* fixed option to display selected products by IDs

= 1.2.8 =
* added the missing translation

= 1.2.7 =
* fiexed bugs and optimized the plugin's forms
* added the new filter to manage template structure (woocommerce_products_carousel_all_in_one_template)
* added option to show "out of stock" products
* added option to exclude products by IDs
* added option to set the relation method between categories and tags
* added new parameters to a owl.Carousel settings - "stagePadding" and "autoWidth"
* added option to define custom breakpoints for RWD
* added option to include "Font Awesome" library to the WordPress Admin Panel
* new translation - Portuguese, Brazilian, thanks to Cleber Paiva de Souza
* updated language files
* removed Russian translation, less than 50% has been translated


= 1.2.6 =
* new translation - Danish, thanks to Daniel Christiansen

= 1.2.5 =
* added the "woocommerce_products_carousel_all_in_one_query" filter to overwrite query parameters

= 1.2.4 =
* fixed shortcode/widget bug

= 1.2.3 =
* fixed wrong link for the product category
* added option to display on sale products
* new translation - French (thanks to tunteam)
* new translation - Swedish (thanks to Imran Khalid)
* added input's HTML5/jQuery validation
* added donate link

= 1.2.2 =
* fixed carousel loading - hide before load
* fixed display one item carousel

= 1.2.1 =
* fixed custom stylesheet loading (thanks to CotswoldPhoto)

= 1.2.0 =
* fixed another typo in the code (sorry!)

= 1.1.9 =
* fixed typo in the code

= 1.1.8 =
* fixed auto height display

= 1.1.7 =
* improved WordPress 4.3 capatible
* added new filter "woocommerce_products_carousel_all_in_one_item_featured_image_placeholder" for the featured image placeholder
* fixed problem with lazy loading images

= 1.1.6 =
* added autoplay speed option
* improved lazy load for large images

= 1.1.5 =
* fixed problem with featured image

= 1.1.4 =
* fixed problem with loading Owl Carousel Libary

= 1.1.3 =
* fixed problem with jQuery UI Effects library

= 1.1.2 =
* fixed problem with duplicate posts

= 1.1.1 =
* fixed problem with animation variable in shortcode generator

= 1.1.0 =
This version contains a lot of modifications:
* added option to setup visible items on mobile, tablets and desktop devices
* added sale info
* new option to sort - by id
* new option to sort - by title
* new option to sort - by date
* new option to sort - by popular
* new option to sort - by featured
* added option to select products by IDs
* new option to order - random lists
* new way to display products - by full content
* added option to allow shortcodes in product full content
* new option to display product image
* new option to display product category
* new option to display product tags
* themes update
* added actions and filters to overwrite html and values

You must regenerate all yours shortcodes and update widgets.

= 1.1.1 =
* fixed problem with animation variable in shortcode generator

= 1.1.0 =
* This version contains a lot of modifications

= 1.0.5 =
* new option to order - random lists

= 1.0.4 =
* new translation - Russian, thanks to Oleg Grymashevich

= 1.0.3 =
* fixed FontAwesome include method
* fixed sql query

= 1.0.2 =
* new option to display product tags
* new option to set margin between items
* new way to include FontAwesome - from official Bootstrap CDN

= 1.0.1 =
* fixed verification requirements
* fixed custom theme dir
* FAQ update

= 1.0.0 =
Initial release