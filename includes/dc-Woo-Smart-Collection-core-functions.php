<?php
if(!function_exists('get_Woo_Smart_Collection_settings')) {
  function get_Woo_Smart_Collection_settings($name = '', $tab = '') {
    if(empty($tab) && empty($name)) return '';
    if(empty($tab)) return get_option($name);
    if(empty($name)) return get_option("dc_{$tab}_settings_name");
    $settings = get_option("dc_{$tab}_settings_name");
    if(!isset($settings[$name])) return '';
    return $settings[$name];
  }
}

if(!function_exists('woocommerce_inactive_notice')) {
  function woocommerce_inactive_notice() {
    ?>
    <div id="message" class="error">
      <p><?php printf( __( '%sWoo Samrt Collection is inactive.%s The %sWooCommerce plugin%s must be active for the Woo Smart Collection to work. Please %sinstall & activate WooCommerce%s', DC_WOO_SMART_COLLECTION_TEXT_DOMAIN ), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url( 'plugins.php' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
    </div>
		<?php
  }
}
?>
