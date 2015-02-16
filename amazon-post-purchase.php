<?php
/*
Plugin Name: Amazon Post Purchase
Plugin URI: http://www.loudlever.com/callout/amazonpost
Description: Side-Bar Widget to display an Amazon product related to the post being displayed.  This widget does not display on Home Page.  The widget will not display if the prerequisite Custom Field in the Post/Page is not set.
Author: Loudlever
Author URI: http://www.loudlever.com
Version: 2.0.0

Copyright 2009-2015 Loudlever (wordpress@loudlever.com)

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

/*
---------------------------------------------------------------------------------
  OPTION SETTINGS
---------------------------------------------------------------------------------
*/  
define('AMZNPP_DEBUG',false);
define('AMZNPP_PLUGIN_VERSION', '2.0.0');
define('AMZNPP_PLUGIN_OPTTIONS', '_amznapp_plugin_options');
define('AMZNPP_ADMIN_PAGE','amazon-post-purchase');
define('AMZNPP_ADMIN_PAGE_NONCE','amznapp-save-options');
define('AMZNPP_PLUGIN_FILE',plugin_basename(__FILE__));
define('AMZNPP_BASE_URL', get_option('siteurl').'/wp-content/plugins/amazon-post-purchase/');
define('AMZNPP_ADMIN_PAGE_NONCE','amznpp-save-options');

require_once("sha256.inc.php"); //required for php4
require_once("aws_signed_request.php"); //major workhorse for plugin
require_once(dirname(__FILE__) . '/include/classes/APP_Widget.class.php');
require_once(dirname(__FILE__) . '/include/classes/APP_Admin.class.php');
$amznpp = new APP_Admin;

if (class_exists("AmazonPostPurchase")) {
  add_action('widgets_init', create_function('', 'return register_widget("AmazonPostPurchase");'));
  add_action('admin_menu', array(&$amznpp,'register_admin_page')); 
}

// enable link to settings page
add_filter($amznpp->plugin_filter(), array(&$amznpp,'plugin_link'), 10, 2 );
register_activation_hook( __FILE__, array(&$amznpp,'activate_plugin'));
register_deactivation_hook( __FILE__, array(&$amznpp,'deactivate_plugin'));

?>
