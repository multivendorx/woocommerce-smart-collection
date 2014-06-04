<?php
class DC_Woo_Smart_Collection {

	public $plugin_url;

	public $plugin_path;

	public $version;

	public $token;
	
	public $text_domain;
	
	public $library;

	public $admin;

	private $file;
	
	public $settings;
	
	public $dc_wp_fields;

	public function __construct($file) {

		$this->file = $file;
		$this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
		$this->plugin_path = trailingslashit(dirname($file));
		$this->token = DC_WOO_SMART_COLLECTION_PLUGIN_TOKEN;
		$this->text_domain = DC_WOO_SMART_COLLECTION_TEXT_DOMAIN;
		$this->version = DC_WOO_SMART_COLLECTION_PLUGIN_VERSION;
		
		add_action('init', array(&$this, 'init'));
	}
	
	/**
	 * initilize plugin on WP init
	 */
	function init() {
		
		// Init Text Domain
		$this->load_plugin_textdomain();
		
		// Init library
		$this->load_class('library');
		$this->library = new DC_Woo_Smart_Collection_Library();

		if (is_admin()) {
			$this->load_class('admin');
			$this->admin = new DC_Woo_Smart_Collection_Admin();
		}

		// DC Wp Fields
		$this->dc_wp_fields = $this->library->load_wp_fields();
	}
	
	/**
   * Load Localisation files.
   *
   * Note: the first-loaded translation file overrides any following ones if the same translation is present
   *
   * @access public
   * @return void
   */
  public function load_plugin_textdomain() {
    $locale = apply_filters( 'plugin_locale', get_locale(), $this->token );

    load_textdomain( $this->text_domain, WP_LANG_DIR . "/dc-Woo-Smart-Collection/dc-Woo-Smart-Collection-$locale.mo" );
    load_textdomain( $this->text_domain, $this->plugin_path . "/languages/dc-Woo-Smart-Collection-$locale.mo" );
  }

	public function load_class($class_name = '') {
		if ('' != $class_name && '' != $this->token) {
			require_once ('class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}// End load_class()
	
	/**
   * Install upon activation.
   *
   * @access public
   * @return void
   */
  function activate_dc_Woo_Smart_Collection() {
    global $DC_Woo_Smart_Collection;
    
    if(!get_option('dc_dc_WC_SC_general_settings_name')) update_option('dc_dc_WC_SC_general_settings_name', array("is_enable" => "Enable", "is_title" => "Title", "is_tag" => "Tag"));
    update_option( 'dc_Woo_Smart_Collection_installed', 1 );
  }
  
  /**
   * UnInstall upon deactivation.
   *
   * @access public
   * @return void
   */
  function deactivate_dc_Woo_Smart_Collection() {
    global $DC_Woo_Smart_Collection;
    delete_option( 'dc_Woo_Smart_Collection_installed' );
  }
	
	/** Cache Helpers *********************************************************/

	/**
	 * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
	 *
	 * @access public
	 * @return void
	 */
	function nocache() {
		if (!defined('DONOTCACHEPAGE'))
			define("DONOTCACHEPAGE", "true");
		// WP Super Cache constant
	}

}
