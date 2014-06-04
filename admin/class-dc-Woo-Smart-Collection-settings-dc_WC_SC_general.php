<?php
class DC_Woo_Smart_Collection_Settings_Gneral {
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;
  
  private $tab;

  /**
   * Start up
   */
  public function __construct($tab) {
    $this->tab = $tab;
    $this->options = get_option( "dc_{$this->tab}_settings_name" );
    $this->settings_page_init();
  }
  
  /**
   * Register and add settings
   */
  public function settings_page_init() {
    global $DC_Woo_Smart_Collection;
    
    $settings_tab_options = array("tab" => "{$this->tab}",
                                  "ref" => &$this,
                                  "sections" => array(
                                                      "default_settings_section" => array("title" =>  __('', $DC_Woo_Smart_Collection->text_domain), // Section one
                                                                                         "fields" => array(
                                                                                                           "is_enable" => array('title' => __('Enable Woo Smart Collection', $DC_Woo_Smart_Collection->text_domain), 'type' => 'checkbox', 'id' => 'is_enable', 'label_for' => 'is_enable', 'name' => 'is_enable', 'value' => 'Enable'),
                                                                                                           "is_append" => array('title' => __('Append with existing smart product categories', $DC_Woo_Smart_Collection->text_domain), 'type' => 'checkbox', 'id' => 'is_append', 'label_for' => 'is_append', 'name' => 'is_append', 'value' => 'Append', 'desc' => __('If unchecked will replace existing smart product categories', $DC_Woo_Smart_Collection->text_domain)),
                                                                                                           "is_title" => array('title' => __('Generate Product Smart Collection from Product Title', $DC_Woo_Smart_Collection->text_domain), 'type' => 'checkbox', 'id' => 'is_title', 'label_for' => 'is_title', 'name' => 'is_title', 'value' => 'Title'),
                                                                                                           "is_tag" => array('title' => __('Generate Product Smart Collection from Product Tags', $DC_Woo_Smart_Collection->text_domain), 'type' => 'checkbox', 'id' => 'is_tag', 'label_for' => 'is_tag', 'name' => 'is_tag', 'value' => 'Tag')
                                                                                                           )
                                                                                         )
                                                      )
                                  );
    
    $DC_Woo_Smart_Collection->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function dc_dc_WC_SC_general_settings_sanitize( $input ) {
    global $DC_Woo_Smart_Collection;
    $new_input = array();
    
    $hasError = false;
    
    if( isset( $input['is_enable'] ) )
      $new_input['is_enable'] = sanitize_text_field( $input['is_enable'] );
    
    if( isset( $input['is_append'] ) )
      $new_input['is_append'] = sanitize_text_field( $input['is_append'] );
    
    if( isset( $input['is_title'] ) )
      $new_input['is_title'] = sanitize_text_field( $input['is_title'] );
    
    if( isset( $input['is_tag'] ) )
      $new_input['is_tag'] = sanitize_text_field( $input['is_tag'] );
    
    if(!$hasError) {
      add_settings_error(
        "dc_{$this->tab}_settings_name",
        esc_attr( "dc_{$this->tab}_settings_admin_updated" ),
        __('Settings updated', $DC_Woo_Smart_Collection->text_domain),
        'updated'
      );
    }

    return $new_input;
  }

  /** 
   * Print the Section text
   */
  public function default_settings_section_info() {
    global $DC_Woo_Smart_Collection;
    //_e('Enter your default settings below', $DC_Woo_Smart_Collection->text_domain);
  }
  
}