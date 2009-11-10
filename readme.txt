=== Amazon Post Purchase ===
Contributors: Richard Luck
Donate link: http://pifmagazine.com/
Tags: Amazon, Affiliate, ASIN, Amazon Associate, Monetize, Amazon.com
Requires at least: 2.5
Tested up to: 2.8.4
Stable tag: 1.0.1

Quickly add Amazon Products related to a to post/page in a side-bar widget by simply setting a Custom Field that includes the Amazon ASIN (ISBN-10). 

== Description ==
Amazon Post Purchase Plugin is based upon the "Amazon Product In a Post Plugin" developed by [Don Fischer](http://fischercreativemedia.com/).
This widget, however, is intended for use as a side-bar widget in themes that support dynamic side-bars.

The Plugin is useful for quickly displaying an appropriate Amazon Product in the side-bar for an individual post.  The plugin only displays for posts where the Amazon product ASIN (also known as the ISBN-10) has been set in a post's custom field.

**Usage Examples:**

* Interview an Author -- and allow readers to purchase the author's latest book.
* Review a Movie -- and allow readers to purchase the DVD.
* Blog about your latest trip to Greece -- and allow readers to purchase a Greek Travel Guide.
* Monetize your blog posts with the ability for readers to purchase ANY Product related to your post.

**How it Works:**
The plugin uses the newest Amazon Product Advertising API, ensuring full security on all transaction calls.

To use the plugin, we recommend that you first get an Amazon Affiliate Account.  Once you have an account, install the plugin, then enter your Amazon Associate ID and the keyname you will use in your post's Custom Field to track the ASIN.

*PLEASE NOTE:* If you DO NOT add your own custom Associate ID, you WILL NOT get credit for any purchases made from your product posts.  By default, this plugin uses our Affiliate Account ID -- so until you input your Affiliate Account ID, all referral $$ will go to us.  (If you're wishing to make a donation - there are easier ways to do it :) You have been warned!

== Installation ==
1. Upload the *amazon-post-purchase* folder (inside the zip file) to your */wp-content/plugins/* directory
2. Activate the plugin through the 'Plugins' menu in WordPress

After you have installed the plugin and loaded the widget into your side-bar, you will need to configure it.   

Drag the "AmazonPostPurchase" widget to the appropriate side-bar container.  Where prompted, set the following fields:

* "Title" - this will be the title displayed above the widget in the side-bar.  You can make this simple text, like "You Might Also Like", or the title can dynamically include the product's author, director, publisher or manufacturer by using the keyword *#_AUTHORNAME_#*, like this: "More by #_AUTHORNAME_#".

* "Amazon Affiliate ID" - this should be _your_ Affiliate ID.  If you do not set this field, all referral $$ from Amazon will go to us.

* "Custom Field Name" - this is the 'name' you will use when you create a custom field in your post.  By default this value is set to 'ASIN'.

No additional adjustments are needed unless you want to configure your own CSS styles. 

*WARNING: If you do not add your Amazon Affiliate ID, you will NOT get credit for purchases made using this Plugin.*

== Usage ==

Once installed, adding a product to your post is a simple process:

*  Go into the full edit mode for the post (Post/Edit then select the post).
*  Under the Custom Fields, click on the link 'Enter New' (after you've done this the first time, you will simply select the value from the drop-down list).
*  Input the value you set in the widget "Custom Field Name" in the "Name" field.  (we recommend the word "ASIN").
*  Input the ASIN for the product you want displayed in the "Value" field.
*  Save or Publish the post.

**Yes -- it's that easy!**

== Frequently Asked Questions ==

= Can I Embed Amazon Products In My Post With This Plugin? =
No.  If you want to do that, we recommend the Amazon Product In a Post Plugin.

= Can I Use This Widget More Than Once In My Sidebar? =
Yes.  Wordpress will allow you to do that.  But each instance of the widget will display exactly the same information.  In short, we don't recommend you do this.

== Screenshots ==

1. Widget displayed in the Widget manager.
2. Control screen for configuring the widget.
3. Custom Field in the Post Edit screen.  This is where you set the ASIN for the product you want to display.

== Changelog ==
= 1.0.1 =
* Simplified the Admin configuration screen.
* Added ability to create dynamic widget titles through use of reserved word *#_AUTHORNAME_#*
* Cleaned up some cluttered parts of code.
* Updated readme.txt file to better explain installation process.
= 1.0 =
* Plugin Release (10/23/2009)
