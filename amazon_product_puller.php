<?php

/*
Plugin Name: Amazon Product Puller
Plugin URI: http://screamwork.co.cc
Description: Pull products from Amazon based on category and search term in WordPress
Version: 0.7
Author: screamwork
License: GPL2
*/

class AmazonProductPuller {

  public function __construct() {

    if ( is_admin() ) {
      add_action( 'wp_ajax_nopriv_amazon-product-puller', array(&$this, 'amazon_product_puller') );
      add_action( 'wp_ajax_amazon-product-puller', array(&$this, 'amazon_product_puller') );
      require_once 'app-options.php';
    }
    
    /*! plugin update stuff !*/
    if ( ABSPATH . 'wp-content' . '/plugins/amazon_product_puller/inc/AmazonppAutoUpdate.php' ) {
      require_once ABSPATH . 'wp-content' . '/plugins/amazon_product_puller/inc/AmazonppAutoUpdate.php';
      new AmazonppAutoUpdate('0.7', 'http://napier.selfip.com/smashing/plugin-update/update-handler.php', plugin_basename(__FILE__) );
    }

    add_shortcode('amazon_content', array( &$this, 'app_content' ) );
    add_action( 'template_redirect', array( &$this, 'initialize' ) );
    
    // needs to be static since > 3.3.1
    register_uninstall_hook( __FILE__, 'AmazonProductPuller::deinstall_amazonpp' );  

  }
  
  function initialize() {
    global $wp_query;

    // $page_object = $wp_query->get_queried_object();
    // print '<pre>'.print_r($page_object, true).'<pre>'; die();
    $post_id     = $wp_query->get_queried_object_id();
    
    // works on 'template_redirect'.
    if(!is_admin() && is_page() && $post_id == get_option('amazon_pp_page_id', '')) {
      
      wp_enqueue_script( 'my-js-functions', plugin_dir_url( __FILE__ ) . 'js/app_js_functions.js', array( 'jquery' ) );
      wp_localize_script( 
        'my-js-functions',
        'amazon_lib',
        array( 
          'ajaxurl' => admin_url( 'admin-ajax.php' ),
          'amazonNonce' => wp_create_nonce( 'amazon-product-puller-nonce' ),
          'no_img' => plugins_url('img/no-image.jpg', __FILE__ ),
        )  
      );
      wp_enqueue_script( 'jq-tmpl', "http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js");
      
      $appStyleUrl = WP_PLUGIN_URL . '/amazon_product_puller/css/app_style.css';
      $appStyleFile = WP_PLUGIN_DIR . '/amazon_product_puller/css/app_style.css';
      if ( file_exists($appStyleFile) ) {
          wp_register_style('app_style', $appStyleUrl);
          wp_enqueue_style( 'app_style');
      }

      if(!is_multisite()) {
        wp_register_style('colorbox', plugins_url('js/colorbox/colorbox.css', __FILE__ )) ;
        wp_enqueue_style('colorbox');
        
        wp_register_script('colorbox', plugins_url('js/colorbox/jquery.colorbox-min.js', __FILE__ ), array( 'jquery' ));
        wp_enqueue_script('colorbox'); 
      }
      else {
        wp_register_style('colorbox', network_home_url() .'/wp-content/plugins/amazon_product_puller/js/colorbox/colorbox.css');
        wp_enqueue_style('colorbox');
        
        wp_register_script('colorbox', network_home_url() .'/wp-content/plugins/amazon_product_puller/js/colorbox/jquery.colorbox-min.js', array( 'jquery' ));
        wp_enqueue_script('colorbox');
      }

    }
    
  }
  
  function amazon_product_puller() {
    
    $nonce = $_GET['amazonNonce'] || $_POST['amazonNonce'];
    
    // check to see if the submitted nonce matches with the
    // generated nonce we created earlier
    if ( !wp_verify_nonce( $nonce, 'amazon-product-puller-nonce' ) ) {
        die('Busted!');
    }
    
    if(isset($_GET['amazon']) && $_GET['amazon'] == 'query') {
      require_once 'query_function.php';
    }

    exit;
    
  }

  // shortcode
  function app_content($attrib){
    
    $attrib = shortcode_atts(array(), $attrib);
  
    $output = '
<!-- *** TEMPLATE -->
<script id="template" type="text/x-jquery-tmpl">
  
  <li class="tmpl_li">
    <div class="img_area">
    <a class="large_image" href="${largeimg}">
      <img class="tmpl_img" src="${smallimg}" />
    </a>
    </div>
    <div class="tmpl_info">
      
      <a href="${link}">
        <h4>${title}<span class="external_link"></span></h4>
      </a>
      
      {{if feature}}
        <p>${feature}</p>
      {{else}}
        <p>{{if binding}} ${binding} {{/if}} {{if genre}}, ${genre} {{/if}}</p>
      {{/if}}
      
      {{if price}}
        <p>${price}</p>
      {{/if}}
      
    </div>
    
  </li>
  
</script>
<!-- *** END TEMPLATE -->
  
<div id="amazonpp_wrapper">
  
  <form action="" method="post">
    <table>
    <tr>
    <td>
    <select id="category">
      <option value="All">All</option>
      <option value="Apparel">Apparel</option>
      <option value="Appliances">Appliances</option>
      <option value="ArtsAndCrafts">Arts &amp; Crafts</option>
      <option value="Automotive">Automotive</option>
      <option value="Baby">Baby</option>
      <option value="Beauty">Beauty</option>
      <option value="Blended">Blended</option>
      <option value="Books">Books</option>
      <option value="Classical">Classical</option>
      <option value="DigitalMusic">Digital Music</option>
      <option value="Music">Music</option>
      <option value="DVD">DVD</option>
      <option value="Shoes">Shoes</option>
      <option value="Software">Software</option>
      <option value="Grocery">Grocery</option>
      <option value="MP3Downloads">MP3 Downloads</option>
      <option value="Electronics">Electronics</option>
      <option value="HealthPersonalCare">Health, Personal &amp; Care</option>
      <option value="Electronics">Electronics</option>
      <option value="HomeGarden">Home - Garden</option>
      <option value="Industrial">Industrial</option>
      <option value="Jewelry">Jewelry</option>
      <option value="KindleStore">KindleStore</option>
      <option value="Kitchen">Kitchen</option>
      <option value="Magazines">Magazines</option>
      <option value="Merchants">Merchants</option>
      <option value="Miscellaneous">Miscellaneous</option>
      <option value="MobileApps">Mobile Apps</option>
      <option value="MusicalInstruments">Musical Instruments</option>
      <option value="MusicTracks">Music Tracks</option>
      <option value="OfficeProducts">Office Products</option>
      <option value="OutdoorLiving">Outdoor Living</option>
      <option value="PCHardware">PC Hardware</option>
      <option value="PetSupplies">Pet Supplies</option>
      <option value="SportingGoods">Sporting Goods</option>
      <option value="UnboxVideo">Unbox Video</option>
      <option value="Tools">Tools</option>
      <option value="Toys">Toys</option>
      <option value="VideoGames">VideoGames</option>
      <option value="Watches">Watches</option>
      <option value="WirelessAccessories">Wireless Accessories</option>
      <option value="Wireless">Wireless</option>
      <option value="VHS">VHS</option>
    </select>
    </td>
    <td>
      <input type="text" name="searchbox" placeholder="Search..." id="searchbox" />
    </td>
    <td>
      <input type="submit" name="submit" id="amazonpp_submit" value="Go" />
    </td>
    <td>
      <span id="app_ajax_wheel"></span>
    </td>
    </tr>
    </table>
  </form>
  
  <!-- pager top -->
  <div class="amazonpp_pager">
    <span class="amazonpp_prev"></span>
    <span class="amazonpp_next"></span>
    <span class="amazonpp_current"></span>
    <span class="of">of</span>
    <span class="amazonpp_total"></span>
    <span class="amazonpp_ajax_loader"><img src="' . plugins_url('img/small-facebook.gif', __FILE__ ) .'" /></span>
  </div>
  
  <div id="amazon_content"></div>
  
  <!-- pager bottom -->
  <div class="amazonpp_pager">
    <span class="amazonpp_prev"></span>
    <span class="amazonpp_next"></span>
    <span class="amazonpp_current"></span>
    <span class="of">of</span>
    <span class="amazonpp_total"></span>
    <span class="amazonpp_ajax_loader"><img src="' . plugins_url('img/small-facebook.gif', __FILE__ ) .'" /></span>
  </div>

</div> <!-- End #wrapper -->';
  
    return trim($output);
  }

  public static function deinstall_amazonpp() {
    delete_option('aws_api_key');
    delete_option('aws_api_secret_key');
    delete_option('aws_associate_tag');
  }

}
$amazon_product_puller = new AmazonProductPuller();

