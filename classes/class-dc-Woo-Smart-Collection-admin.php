<?php
class DC_Woo_Smart_Collection_Admin {
  
  public $settings;

	public function __construct() {
		//admin script and style
		add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_script'));
		
		add_action('dc_Woo_Smart_Collection_dualcube_admin_footer', array(&$this, 'dualcube_admin_footer_for_dc_Woo_Smart_Collection'));
		
		add_action( 'add_meta_boxes', array(&$this, 'add_custom_meta_boxes') );
		
		add_action( 'save_post', array(&$this, 'assign_woo_smart_collection') );

		$this->load_class('settings');
		$this->settings = new DC_Woo_Smart_Collection_Settings();
	}
	
	/**
   * WP Samrt Taxonomy settings custom meta options
   */
  function add_custom_meta_boxes() {
    global $DC_Woo_Smart_Collection;
    
    // Smart Taxonomy settings
    add_meta_box( 
        'woo_smart_collection_options',
        __( 'WooCommerce Smart Collection', $DC_Woo_Smart_Collection->text_domain ),
        array(&$this, 'set_woo_smart_collection_options'),
        'product', 'normal', 'high'
    );
    
  }
  
  function set_woo_smart_collection_options($product) {
    global $DC_Woo_Smart_Collection;
    
    $smart_cat_settings = get_post_meta($product->ID, '_smart_cat_settings', true);
    if(!$smart_cat_settings) $smart_cat_settings = get_Woo_Smart_Collection_settings('', 'dc_WC_SC_general');
    
    echo '<table>';
    $settings_options = array(
                             "placeholder" => array('type' => 'hidden', 'name' => 'smart_cat_settings[placeholder]', 'value' => 'placeholder'),
                             "is_enable" => array('label' => __('Enable Smart Category', $DC_Woo_Smart_Collection->text_domain), 'type' => 'checkbox', 'name' => 'smart_cat_settings[is_enable]', 'value' => 'Enable', 'dfvalue' => $smart_cat_settings['is_enable']),
                             "is_append" => array('label' => __('Append with existing smart categories', $DC_Woo_Smart_Collection->text_domain), 'type' => 'checkbox', 'name' => 'smart_cat_settings[is_append]', 'value' => 'Append', 'dfvalue' => $smart_cat_settings['is_append'], 'hints' => __('If unchecked will replace existing smart categories', $DC_Woo_Smart_Collection->text_domain)),
                             "is_title" => array('label' => __('Generate Smart Category from Post Title', $DC_Woo_Smart_Collection->text_domain), 'type' => 'checkbox', 'name' => 'smart_cat_settings[is_title]', 'value' => 'Title', 'dfvalue' => $smart_cat_settings['is_title']),
                             "is_tag" => array('label' => __('Generate Smart Category from Post Tags', $DC_Woo_Smart_Collection->text_domain), 'type' => 'checkbox', 'name' => 'smart_cat_settings[is_tag]', 'value' => 'Tag', 'dfvalue' => $smart_cat_settings['is_tag'])
                             );
    
    $DC_Woo_Smart_Collection->dc_wp_fields->dc_generate_form_field($settings_options, array('in_table' => true));
    echo '</table>';
    
    do_action('dc_Woo_Smart_Collection_dualcube_admin_footer');
  }
	
	public function assign_woo_smart_collection($product_id) {
	  
	  // If this is just a autosave, don't do anything
	  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $product_id;
	  
	  // If this is just a revision, don't do anything
    if ( wp_is_post_revision( $product_id ) )
      return $product_id;
  
    if( get_post_type($product_id) != 'product' )
      return $product_id;
    
    $product_categories = get_terms( 'product_cat', array( 'hide_empty' => 0 ) );
    if(count($product_categories) == 0)
      return $product_id;
    
    $smart_cat_settings = $_POST['smart_cat_settings'];
    if(!$smart_cat_settings) $smart_cat_settings = get_Woo_Smart_Collection_settings('', 'dc_WC_SC_general');
    
    update_post_meta($product_id, '_smart_cat_settings', $smart_cat_settings);
    
    $old_smart_cats = (get_post_meta($product_id, '_smart_cats', true)) ? get_post_meta($product_id, '_smart_cats', true) : array();
    if(!empty($old_smart_cats)) wp_remove_object_terms( $product_id, $old_smart_cats, 'product_cat' );
    
    if(!$smart_cat_settings['is_enable'])
      return $product_id;
    
    $product_title = get_the_title( $product_id );
    $product_tags = wp_get_object_terms( $product_id, 'product_tag', array('fields' => 'all') );
    
    $smart_cats = array();
    
    foreach($product_categories as $product_category) {
      $woo_collection_default_association = true;
      $woo_collection_default_association = apply_filters('woo_smart_collection_asso_mode', $woo_collection_default_association, $product_id, $product_category);
      
      if($woo_collection_default_association) {
        // Decide Samrt Cats from Post Title
        if($smart_cat_settings['is_title']) {
          if(strpos(strtolower($product_title), wptexturize(strtolower($product_category->name))) !== false) {
            $smart_cats[] = $product_category->term_id;
          }
        }
        
        // Decide Samrt Cats from associated Tags
        if($smart_cat_settings['is_tag']) {
          if(!empty($product_tags)) {
            foreach($product_tags as $product_tag) {
              if(strtolower($product_category->name) == strtolower($product_tag->name)) {
                $smart_cats[] = $product_category->term_id;
              }
            }
          }
        }
      } else {
        $smart_association = false;
        $smart_association = apply_filters('woo_smart_collection_asso', $smart_association, $product_id, $product_category);
        if($smart_association) $smart_cats[] = $product_category->term_id;
      }
    }
    
    if(!empty($smart_cats)) {
      $smart_cats = array_map('intval', $smart_cats);
      
      if($smart_cat_settings['is_append']) {
        $smart_cats = array_merge((array)$smart_cats, (array)$old_smart_cats);
      }
       
      $smart_cats = array_unique( $smart_cats );
      wp_set_object_terms( $product_id, $smart_cats, 'product_cat', true );
        
      update_post_meta($product_id, '_smart_cats', $smart_cats);
    }
    
    return $product_id;
	}

	function load_class($class_name = '') {
	  global $DC_Woo_Smart_Collection;
		if ('' != $class_name) {
			require_once ($DC_Woo_Smart_Collection->plugin_path . '/admin/class-' . esc_attr($DC_Woo_Smart_Collection->token) . '-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}// End load_class()
	
	function dualcube_admin_footer_for_dc_Woo_Smart_Collection() {
    global $DC_Woo_Smart_Collection;
    ?>
    <div style="clear: both"></div>
    <div id="dc_admin_footer">
      <?php _e('Powered by', $DC_Woo_Smart_Collection->text_domain); ?> <a href="http://dualcube.com" target="_blank"><img src="<?php echo $DC_Woo_Smart_Collection->plugin_url.'/assets/images/dualcube.png'; ?>"></a><?php _e('Dualcube', $DC_Woo_Smart_Collection->text_domain); ?> &copy; <?php echo date('Y');?>
    </div>
    <?php
	}

	/**
	 * Admin Scripts
	 */

	public function enqueue_admin_script() {
		global $DC_Woo_Smart_Collection;
		$screen = get_current_screen();
		
		if (in_array( $screen->id, array( 'product' ))) :
		  $DC_Woo_Smart_Collection->library->load_qtip_lib();
		  wp_enqueue_style('admin_css',  $DC_Woo_Smart_Collection->plugin_url.'assets/admin/css/admin.css', array(), $DC_Woo_Smart_Collection->version);
		  wp_enqueue_script('admin_js', $DC_Woo_Smart_Collection->plugin_url.'assets/admin/js/admin.js', array('jquery'), $DC_Woo_Smart_Collection->version, true);
		endif;
		
		// Enqueue admin script and stylesheet from here
		if (in_array( $screen->id, array( 'toplevel_page_dc-WC-SC-setting-admin' ))) :   
		  $DC_Woo_Smart_Collection->library->load_qtip_lib();
		  $DC_Woo_Smart_Collection->library->load_upload_lib();
		  $DC_Woo_Smart_Collection->library->load_colorpicker_lib();
		  $DC_Woo_Smart_Collection->library->load_datepicker_lib();
		  wp_enqueue_script('admin_js', $DC_Woo_Smart_Collection->plugin_url.'assets/admin/js/admin.js', array('jquery'), $DC_Woo_Smart_Collection->version, true);
		  wp_enqueue_style('admin_css',  $DC_Woo_Smart_Collection->plugin_url.'assets/admin/css/admin.css', array(), $DC_Woo_Smart_Collection->version);
	  endif;
	}
}