<?php

// ********** OPTIONS PAGE **********
// **********************************

add_action('admin_menu', 'amazon_pp_menu');

function amazon_pp_menu() {
  add_options_page('Amazon Product Puller', 'Amazon Product Puller', 'manage_options', 'amazon-product-pull-options', 'amazon_pp_options');
  if(is_admin()) {
    add_action( 'admin_init', 'register_amazonpp_settings' );
  }  
}

function register_amazonpp_settings() {
  //register our settings
  register_setting( 'amazonpp-settings-group', 'aws_api_key' );
  register_setting( 'amazonpp-settings-group', 'aws_api_secret_key' );
  register_setting( 'amazonpp-settings-group', 'aws_associate_tag' );
  register_setting( 'amazonpp-settings-group', 'amazon_pp_page_id' );
}

function amazon_pp_options() {
  
  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }
  
?>
  
  <div class="wrap">
    <h2>AMAZON PRODUCT PULLER - OPTIONS</h2>
    
    <form action="options.php" method="POST">
      <?php settings_fields( 'amazonpp-settings-group' ); ?>

      <table class="form-table">

          <tr valign="top">
            <td colspan="2"><h4>AWS API SETTINGS</h4></td>
          </tr>         

          <tr valign="top">
            <th scope="row">AWS API KEY</th>
            <td><input type="text" name="aws_api_key" value="<?php echo get_option('aws_api_key'); ?>" /></td>
          </tr>
           
          <tr valign="top">
            <th scope="row">AWS API SECRET KEY</th>
            <td><input type="text" name="aws_api_secret_key" value="<?php echo get_option('aws_api_secret_key'); ?>" /></td>
          </tr>
          
          <tr valign="top">
            <th scope="row">AWS ASSOCIATE TAG</th>
            <td><input type="text" name="aws_associate_tag" value="<?php echo get_option('aws_associate_tag'); ?>" /></td>
          </tr>

          <tr valign="top">
            <td colspan="2"><h4>MISC SETTINGS</h4></td>
          </tr>

          <tr valign="top">
            <th scope="row">Post ID</th>
            <td><input type="text" name="amazon_pp_page_id" value="<?php echo get_option('amazon_pp_page_id'); ?>" />
            <br />
            <label>go to posts or pages and hover over "edit" (in the status bar you'll find the "id").</label> 
            </td>
          </tr>

      </table>
      
      <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
      </p>
  
    </form>
  </div>
 
<?php
}