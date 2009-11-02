<?php
/*
Plugin Name: Amazon Post Purchase
Plugin URI: http://dimax.com
Description: Side-Bar Widget to display an Amazon product for purchase that is related to the post.  Degrades gracefully if post lacks necessary meta data.
Author: Richard Luck
Version: 1.0
Author URI: http://dimax.com
*/

require_once("sha256.inc.php"); //required for php4
require_once("aws_signed_request.php"); //major workhorse for plugin

class AmazonPostPurchase extends WP_Widget {

    // global vars
    var $public_key = '07HQNKSPECJ80ZWQW5G2';
    var $private_key = '9++ZOtMc0mFTm+LguipUzs8frz1pR4YWJsxNweXe';
    var $affiliate = 'pifaliterajourna';  // you should set your own affiliate code, or we'll get credit for your purchases
    var $asin_tag = 'ASIN'; // default custom field 'name'
    
    /** constructor */
	function AmazonPostPurchase() {
		// widget contructor
        parent::WP_Widget(false, $name = 'AmazonPostPurchase');	
	}

    /** @see WP_Widget::widget */
	function widget($args, $instance) {
	    global $post;
	    
        // This widget does NOT execute on the homepage
	    if (is_home()) { return false; }
		// outputs the content of the widget
        extract($args);
        $asin_tag = apply_filters('widget_asin_tag', $instance['asin_tag']);
    	$asin = get_post_meta($post->ID,$asin_tag,true);
        if (!$asin) { return false; }  // we don't execute if the page does not have an asin
        // get the remainder of the vars
        $title = apply_filters('widget_title', $instance['title']);
        $affiliate = apply_filters('widget_affiliate', $instance['affiliate']);
        $widget_content = $this->getSingleAmazonProduct($asin,$affiliate);
        
    // start the output
        echo $before_widget; 
        if ( $title ) {
            echo $before_title . $title . $after_title; 
        }
        echo $widget_content; 
        echo $after_widget; 
	}

    /** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
	    if (!$new_instance['affiliate']) {
	        $new_instance['affiliate'] = $this->affiliate;
        }
	    if (!$new_instance['asin_tag']) {
	        $new_instance['asin_tag'] = $this->asin_tag;
        }
	    return $new_instance;
	}

	// outputs the options form on admin
    /** @see WP_Widget::form */
	function form($instance) {
        $title = esc_attr($instance['title']);
        $affiliate = esc_attr($instance['affiliate']);
        $asin_tag = esc_attr($instance['asin_tag']);
?>
    <h3>The Amazon-Post-Purchase widget will <i>only</i> display on post pages.</h3>
    <p>To enable it's display for a Post, ensure that you:
    <ol><li>set a custom field in your post with the same name indicated below, and</li>
        <li>set the custom field value to the product's 10-digit ASIN code as provided by Amazon</li></ol></p>
        <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title:'); ?>
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label>
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('affiliate'); ?>"><?php _e('Amazon Affiliate ID:'); ?>
          <input class="widefat" id="<?php echo $this->get_field_id('affiliate'); ?>" name="<?php echo $this->get_field_name('affiliate'); ?>" type="text" value="<?php echo $affiliate; ?>" /></label>
          <small>* If not set, will default to <i>our</i> Affiliate ID</small>
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('asin_tag'); ?>"><?php _e('Custom Field Name:'); ?>
          <input class="widefat" id="<?php echo $this->get_field_id('asin_tag'); ?>" name="<?php echo $this->get_field_name('asin_tag'); ?>" type="text" value="<?php echo $asin_tag; ?>" /></label>
        </p>
<?php           
          
	}
	
	//Single Product API Call - Returns One Product Data
	function getSingleAmazonProduct($asin='',$aws_partner_id, $extratext=''){
		$target=' target="_new" ';
		$a_err_msg='Product Unavailable.';
		$a_hidden_msg='Visit Amazon for Price.';
		$aws_partner_locale='com';
	    $pop_new_window = true;
        $public_key = $this->public_key;
        $private_key = $this->private_key;
        
        if (!$asin) { return false; }

			$ASIN = $asin; //valid ASIN
			$errors='';
			// Amazon API Call
			$pxml = aws_signed_request($aws_partner_locale, array(
			    "Operation"=>"ItemLookup",
			    "ItemId"=>"$ASIN",
			    "ResponseGroup"=>"ItemAttributes,Images,Offers",
			    "IdType"=>"ASIN",
			    "AssociateTag"=>"$aws_partner_id"), $public_key, $private_key);
			
			//print_r($pxml);
			if(isset($pxml["ItemLookupResponse"]["Items"]["Request"]["Errors"]["Error"]["Message"])){
				$errors=$pxml["ItemLookupResponse"]["Items"]["Request"]["Errors"]["Error"]["Message"];
			}
			if($errors!=''){
                // for now - errors are turned off and widget will not display
                return false;
			}
            // get our result data formatted into a meaningful hash
            
			$result = FormatASINResult($pxml);
            // build up our HTML

            // Test for a large image - if we have it, provide a link
            $largeimage = false;
            if($result['LargeImage']!=''){
            $largeimage = <<< EOF
        <div id="amazon-post-purchase-large-image-link">
            <a target="amazon-image" href="javascript: void(0)" 
                onclick="artwindow=window.open('{$result['LargeImage']}','art','directories=no, location=no, menubar=no, resizable=no, scrollbars=no, status=no, toolbar=no, width=400,height=525');artwindow.focus();return false;">
            <span>See larger image</span></a>
        </div>
EOF;
             }
             // Test for byline - set it appropriately
             $byline = false;
			if(isset($result["Author"])){
			    $byline .= "<div id='amazon-post-purchase-author'>By {$result['Author']} </div>";
			}
			if(isset($result["Director"])){
			    $byline .= "<div id='amazon-post-purchase-director'>Directed by {$result['Director']} </div>";
			}
			if(isset($result["Actors"])){
			    $byline .= "<div id='amazon-post-purchase-starring'>Starring: {$result['Actors']} </div>";
			}
			if(isset($result["Rating"])){
			    $byline .= "<div id='amazon-post-purchase-rating'>Rating: {$result['Rating']} </div>";
			}

            // Hidden price
			if($result["ListPrice"]!='0') {
			    $list_price = $result["ListPrice"];
		    }
			else {
			    $list_price = $a_hidden_msg;
			}

            // lowest price
            $lowest_new_price = false;
			if (isset($result["LowestNewPrice"])){
				if($result["LowestNewPrice"]=='Too low to display'){
					$newPrice = 'Check Amazon For Pricing';
				}
				else{
					$newPrice = $result["LowestNewPrice"];
				}
				if($result["TotalNew"]>0){
				    $instock = " <span class='instock'>In Stock</span>";
				}
				else {
				    $instock = " <span class='outofstock'>Out of Stock</span>";
                }
                $lowest_new_price = "<tr><td>New From:</td><td>{$newPrice}{$instock}</td></tr>";
			}
            //  check for used prices
            $lowest_used_price = false;
			if(isset($result["LowestUsedPrice"])){
				if($result["TotalUsed"]>0){
				    $lowest_used_price = "<tr><td>Used From:</td><td>{$result["LowestUsedPrice"]} <span class='instock'>In Stock</span></td></tr>";
				}
			}

            $plugin_dir = get_bloginfo('url') . '/' . PLUGINDIR . '/amazon-post-purchase';
// the whole kit-n-kaboodle
			$returnval  =  <<< EOF
<div id="amazon-post-purchase-container">  
	<div id="amazon-post-purchase-image">
	    <a href="{$result["URL"]}">{$this->awsImageGrabber($result['MediumImage'],'amazon-image')}</a>
        {$largeimage}
    </div>
    <div id="amazon-post-purchase-byline">
        <h2><a href="{$result["URL"]}">{$result["Title"]}</a></h2>
        {$byline}
    </div>
    <table id="amazon-post-purchase-publication">
        <tr><td>{$result['Binding']}</td></tr>
        <tr><td>Released {$result['PublicationDate']}</td></tr>
        <tr><td>By {$result['Publisher']}</td></tr>
    </table>

    <table id="amazon-post-purchase-price">
        <tr><td>List Price:</td><td>{$list_price}</td></tr>
        {$lowest_new_price}
        {$lowest_used_price}
    </table>

    <div id="amazon-post-purchase-button">
        <a style="display:block;margin-top:8px;width:165px;" {$target} href="{$result["URL"]}">
            <img src="{$plugin_dir}/images/buyamzon-button.png" border="0" style="border:0 none !important;margin:0px !important;background:transparent !important;"/>
        </a>
    </div>
</div>

EOF;
	    return $returnval;
	}
	
	//Amazon Product Image from ASIN function - Returns HTML Image Code
	function awsImageGrabber($imgurl, $class=""){

	    $base_url0 = '<'.'img src="';
	    $base_url = $imgurl;
	    $base_url1 = '"';
	    $base_url1 = $base_url1.' class="amazon-image '.$class.'"';
	    $base_url1 = $base_url1.' rel="image-'.$asin.'" />';
		
		if($base_url!=''){
	    	return $base_url0.$base_url.$base_url1;
		}else{
			$base_url = get_bloginfo('url').'/'.PLUGINDIR.'/amazon-product-in-a-post-plugin/images/noimage.jpg';
	    	return $base_url0.$base_url.$base_url1;
		}
	}
	
	//Amazon Product Image from ASIN function - Returns URL only
	function awsImageGrabberURL($asin, $size="M"){
	    $base_url = 'http://images.amazon.com/images/P/'.$asin.'.03.';
	    if (strcasecmp($size, 'S') == 0){
	      $base_url .= 'THUMBZZZ';
	    }
	    else if (strcasecmp($size, 'L') == 0){
	      $base_url .= 'LZZZZZZZ';
	    }
	    else{
	      $base_url .= 'MZZZZZZZ';
	    }
	    $base_url .= '.jpg';
	    return $base_url;
	}
	
	
	
	
	
	
	
	
	
	
} // end class

//  register_widget('AmazonPostPurchase');
// register FooWidget widget
add_action('widgets_init', create_function('', 'return register_widget("AmazonPostPurchase");'));

?>
