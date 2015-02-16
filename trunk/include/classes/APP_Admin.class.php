<?php
/*
  Admin Class
  
  Contains all of the functions for managing SGW administration functions.

  Icons : http://www.iconarchive.com/show/100-flat-icons-by-graphicloads.html
*/
class APP_Admin {


  var $help = false;
  var $options = array();
  var $donate_link = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Y8SL68GN5J2PL';
  // default settings to prevent plugin from breaking when no value provided.
  var $defaults = array('tag' => 'ASIN', 'aff_id' => 'pifmag-amazon-post-purchase-20', 'aff_cc' => 'com');


  public function __construct() {
    $this->options = get_option(AMZNPP_PLUGIN_OPTTIONS);
    $this->log(sprintf("in constructor\nopts = %s",print_r($this->options,true)));
    $this->check_plugin_version();
  }   

  public function __destruct() {
    // nothing to see yet
  }

  public function activate_plugin() {
    $this->log("in the activate_plugin()");
    // $this->check_plugin_version();
  }
  public function deactivate_plugin() {
    $this->log("in the deactivate_plugin()");
    $this->options = false;
    delete_option(AMZNPP_PLUGIN_OPTTIONS);  // remove the default options
	  return;
  }
  
  private function plugin_admin_url() {
    $url = 'options-general.php?page='.AMZNPP_ADMIN_PAGE;
    return $url;
  }
  // Filter for creating the link to settings
  public function plugin_filter() {
    return sprintf('plugin_action_links_%s',AMZNPP_PLUGIN_FILE); 
  }
  // Called by plugin filter to create the link to settings
  public function plugin_link($links) {
    $url = $this->plugin_admin_url();
    $settings = '<a href="'. $url . '">'.__("Settings", "sgw").'</a>';
    array_unshift($links, $settings);  // push to left side
    return $links;
  }

  public function register_admin_page() {
    $this->log("in the register_admin_page()");
    // ensure our js and style sheet only get loaded on our admin page
    $this->help = add_options_page('Amazon Post Purchase', 'Amazon Post Pur...', 'manage_options', AMZNPP_ADMIN_PAGE, array(&$this,'AdminPage'));
    add_action("admin_print_scripts-". $this->help, array(&$this,'admin_js'));
    add_action("admin_print_styles-". $this->help, array(&$this,'admin_stylesheet') );
  }
  function admin_js() {
    $this->log("in the admin_js()");
    // wp_enqueue_script('sgw', WP_PLUGIN_URL . '/support-great-writers/js/sgw.js'); 
  }
  function admin_stylesheet() {
    $this->log("in the admin_stylesheet()");
    printf("<link rel='stylesheet' href='%sinclude/css/amznpp_admin.css' type='text/css' />",AMZNPP_BASE_URL); 
  }
  function AdminPage() {
    $this->configuration_screen();
  }

  public function configuration_screen() {
    if (is_user_logged_in() && is_admin() ){
      $this->log("config screen settings");
      $this->log(sprintf("POST = %s",print_r($_POST,1)));
      // update then refetch
      $message = $this->update_options($_POST);
      $opts = get_option(AMZNPP_PLUGIN_OPTTIONS)[options];
      $aff_id = $opts[aff_id];
      if ($aff_id == $this->defaults['aff_id']) { $aff_id = ''; }  // don't display default

      if ($message) {
        printf('<div id="message" class="updated fade"><p>%s</p></div>',$message);
      } elseif ($this->error) { // reload the form post in this form
        // set the defaults
        // $opts['default'] =  $_POST['AMZNPP_opt']['default'];
        // // restructure the posts hash
        // foreach ($posts as $x=>$hash) {
        //   $id = $hash['ID'];
        //   if (isset($_POST['AMZNPP_opt']['posts'][$id])) {
        //     $hash['meta_value'] = $_POST['AMZNPP_opt']['posts'][$id];
        //     $posts[$x] = $hash;
        //   }
        // }
      }
      //       if ($opts['default'] && !$opts['affiliate_id']) {
      //         // $this->missing_affiliate_id();
      //       }
      // if (!$opts['default']) {
      //  $opts['default'] = AMZNPP_BESTSELLERS;
      // }

      // Need some styling
      $this->sidebar_link_icon_style();
      // $this->options[options][tag] = $val[asin_tag];
      // $this->options[options][aff_id] = $val[affiliate];
      // $this->options[options][aff_cc] = 'com';  // this wasn't set before - so assume US
    ?>
    <div class="wrap">
      <h2>Amazon Post Purchase Widget</h2>
      <?php
      if (!$message) {
      ?>
      <div class="updated">
				<p><strong>Thanks for using this plugin! If it works for you, <a href='<?php echo $this->donate_link; ?>' target='_blank'>please donate!</a> Donations help keep this plugin free for everyone to use.</strong></p>
      </div>
      <?php
      }
      ?>
      <div id="poststuff" class="metabox-holder has-right-sidebar">
        <!-- Right Side -->
				<div class="inner-sidebar">
					<div id="side-sortables" class="meta-box-sortabless ui-sortable" style="position:relative;">
            <?php 
              $this->html_box_header('amznpp_about',__('About this Plugin','amznpp'),true);
              $this->sidebar_link('PayPal',$this->donate_link,'Donate with PayPal'); 
              $this->sidebar_link('Home','https://wordpress.org/plugins/amazon-post-purchase/','Plugin Homepage'); 
              $this->sidebar_link('Suggestion','https://wordpress.org/support/plugin/amazon-post-purchase','Help/Suggestions'); 
              $this->sidebar_link('Contact','mailto:wordpress@loudlever.com','Contact Us'); 
              $this->sidebar_link('More','https://wordpress.org/plugins/search.php?q=loudlever','More Plugins by Us'); 
          	  $this->html_box_footer(true); 
          	?>  
          </div>
        </div>
        <!-- Left Side -->
        <div class="has-sidebar sm-padded">
					<div id="post-body-content" class="has-sidebar-content">
						<div class="meta-box-sortabless">
              <form method="post" action="admin.php?page=<?php echo AMZNPP_ADMIN_PAGE; ?>">
                <?php
                  if(function_exists('wp_nonce_field')){ wp_nonce_field(AMZNPP_ADMIN_PAGE_NONCE); }
                ?>   
                <!-- Default Settings -->
                <?php $this->html_box_header('amznpp_default_asins',__('Settings','amznpp'),true); ?>
  						  <p>Don't forget to add the widget to your side-bar after configuring your settings below.</p>
                <p>
                  <label class='amznpp_label' for='amznpp_aff_id'>Amazon Affiliate ID:</label>
                  <input type="text" name="amznpp_opt[aff_id]" id="amznpp_aff_id" class='amznpp_input' value="<?php echo  $aff_id; ?>" />
                </p>
                <p>
                  <label class='amznpp_label' for='amznpp_aff_cc'>Affiliate Country:</label>
                  <select name="amznpp_opt[aff_cc]" id="amznpp_aff_cc" class='amznpp_input'>
                    <?php
                      $countries = $this->supported_countries();
                      foreach ($countries as $key=>$val) {
                        $sel = '';
                        if ($opts['aff_cc']==$key) { $sel = 'selected="selected"'; }
                        printf("<option value='%s' %s>%s</option>",$key,$sel,$val);
                      }
                    ?>          
                  </select>
                  <br/>
                  <small class="amznpp_small">* Prices will be displayed in the default currency of this store.</small>
                </p>
                <p>
                  <label class="amznpp_label" for='amznpp_tag'>Custom Field Name:</label>
                  <input type="text" name="amznpp_opt[tag]" id="amznpp_tag" class='amznpp_input' value="<?php echo  $opts['tag']; ?>" />
                  <br/>
                  <SMALL class="amznpp_small">* The POST custom field name where ASINs will be stored.</SMALL>
                  <input type="hidden" name="save_settings" value="1" />
                </p>
                <?php $this->html_box_footer(true); ?>  
                <input type="submit" class="button-primary" name="save_button" value="<?php _e('Update Settings', 'amznpp'); ?>" />
  	          </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php

    }

  }  
  
  public function update_options($form) {
    $message = null;
    if(isset($_POST['save_settings'])) {
      check_admin_referer(AMZNPP_ADMIN_PAGE_NONCE);
      if (isset($_POST['amznpp_opt'])) {
        $message = 'Your updates have been saved.';
        $opts = $_POST['amznpp_opt'];
        // printf("<pre>In update_options()\OPTS: %s\naction = %s</pre>",print_r($opts,1),$_REQUEST['action']);
        // biz rule, if aff_id is blank, set country to 'com'
        // if tag is blank, set to default
        if ($opts[aff_id] && $opts[aff_cc]) {
          $this->options[options][aff_id] = $opts[aff_id];
          $this->options[options][aff_cc] = $opts[aff_cc];
        } else {
          $this->options[options][aff_id] = $this->defaults[aff_id];
          $this->options[options][aff_cc] = $this->defaults[aff_cc];
          $message = 'Affiliate ID not set - using default configuration.';
        }
        if ($opts[tag]) {
          $this->options[options][tag]    = $opts[tag];
        } else {
          $this->options[options][tag]    = $this->defaults[tag];
          $message = 'Custom field name not set - using default.';
        }
        update_option(AMZNPP_PLUGIN_OPTTIONS,$this->options);
      }
      return $message;
    }
  }
  	public function html_box_header($id, $title) {
  ?>
  			<div id="<?php echo $id; ?>" class="postbox">
  				<h3 class="hndle"><span><?php echo $title ?></span></h3>
  				<div class="inside">
  <?php
  	}

  	public function html_box_footer() {
  ?>
  				</div>
  			</div>
  <?php
    }
  /*
    Private Functions
  */
  private function check_plugin_version() {
    $opts = $this->options;
    $this->log(sprintf("in check_plugin_version()\nPLUGIN VERSION = %s\nopts = %s",AMZNPP_PLUGIN_VERSION,print_r($opts,1)));
    if (!$opts || !$opts[plugin] || $opts[plugin][version_current] == false) {
      $this->log("no old version - initializing");
      $this->init_plugin();
      return;
    }
    // check for upgrade option here
    if ($opts[plugin][version_current] != AMZNPP_PLUGIN_VERSION) {
      $this->log("need to upgrade version");
      $this->upgrade_plugin($opts);
      return;
    }
    $this->log('-Returning from check_plugin_version()');
  }
  private function get_version_as_int($str) {
    $var = intval(preg_replace("/[^0-9 ]/", '', $str));
    return $var;
  }
  private function init_install_options() {
    $this->options = array(
      'plugin' => array(
        'version_last'    => AMZNPP_PLUGIN_VERSION,
        'version_current' => AMZNPP_PLUGIN_VERSION,
        'install_date'    => Date('Y-m-d'),
        'upgrade_date'    => Date('Y-m-d')
      ),
      'options' => $this->defaults
    );
    return;
  }
  private function init_plugin() {
    $this->init_install_options();
    add_option(AMZNPP_PLUGIN_OPTTIONS,$this->options);
    return;
  }
  public function log($msg) {
    if (AMZNPP_DEBUG) {
      error_log(sprintf("%s\n",$msg),3,dirname(__FILE__) . '/../../error.log');
    }
  }
  private function sidebar_link($key,$link,$text) {
    printf('<a class="amznpp_button amznpp_%s" href="%s" target="_blank">%s</a>',strtolower($key),$link,__($text,'amznpp'));
  }
  // print out an inline style for each side-bar icon we'll use
  private function sidebar_link_icon_style() {
    $icons = array('paypal','home','suggestion','contact','more'); // names of the images and styles
    print("<style type='text/css'>");
    foreach($icons as $val) {
      printf("a.amznpp_%s { background-image:url(%simages/%s.png); }",$val,AMZNPP_BASE_URL,$val);
    }
    print("</style>");
    return;
  }  
  private function supported_countries() {
    // $countries = array(
    //   'us' => 'United States', 
    //   'uk' => 'United Kingdon', 
    //   'de' => 'Germany', 
    //   'fr' => 'France', 
    //   'ca' => 'Canada'
    // );
    $countries = array(
      'com' => 'amazon.com', 
      'co.uk' => 'amazon.co.uk', 
      'de' => 'amazon.de', 
      'fr' => 'amazon.fr', 
      'ca' => 'amazon.ca');
    return $countries;
  }
  private function upgrade_plugin($opts) {
    $ver = $this->get_version_as_int($this->options['plugin']['version_current']);
    $this->log("Version = $ver");
    if ($ver < 200) {
      $widget_obj = new AmazonPostPurchase; // we need to reference old settings from widget config and migrate to new plugin opts
      // need to migrate the settings from widget to options
      $instance = $widget_obj->get_settings();
      $this->log(sprintf("widget settings = %s",print_r($instance,1)));
      foreach($instance as $key => $val) {
        if($val && $val[affiliate] && $val[asin_tag]) {
          // we have a match
          $this->options[options][tag] = $val[asin_tag];
          $this->options[options][aff_id] = $val[affiliate];
          $this->options[options][aff_cc] = 'com';  // this wasn't set before - so assume US
          break;
        }
      }
    }
    $this->options[plugin][version_last] = $this->options[plugin][version_current];
    $this->options[plugin][version_current] = AMZNPP_PLUGIN_VERSION;
    $this->options[plugin][upgrade_date] = Date('Y-m-d');
    $this->log(sprintf("upgrading plugin with opts %s",print_r($this->options,1)));
    update_option(AMZNPP_PLUGIN_OPTTIONS,$this->options);
  }
}
?>