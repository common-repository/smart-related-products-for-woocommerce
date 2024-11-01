<?php
/**
* Admin settings part
*/
class SRP_SettingsPage{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    /**
     * Start up
     */
    public function __construct(){
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
	}
    /**
     * Add options page
     */
    public function add_plugin_page(){
        add_submenu_page(
			'woocommerce',
            'Smart related products settings', 
            'Smart related products', 
            'manage_options', 
            'smart-related-products', 
            array( $this, 'create_admin_page' )
        );
	}
    /**
     * Options page callback
     */
    public function create_admin_page(){
        // Set class property
        $this->options = get_option( 'smart_related_products_option' );
        ?>
        <div class="wrap">
            <h1><?php _e('Settings','smart-related-products-for-woocommerce'); ?></h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'smart_related_products_group' );
                do_settings_sections( 'smart-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
	}
    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'smart_related_products_group', // Option group
            'smart_related_products_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            __('Basic options','smart-related-products-for-woocommerce'), // Title
            array( $this, 'print_section_info' ), // Callback
            'smart-setting-admin' // Page
        );
		add_settings_field(
            'posts_per_page', // ID
            __('Products to display','smart-related-products-for-woocommerce'), // Title 
            array( $this, 'posts_per_page_callback' ), // Callback
            'smart-setting-admin', // Page
            'setting_section_id'		// Section           
        );
		add_settings_field(
            'category', // ID
            __('Product category','smart-related-products-for-woocommerce'), // Title 
            array( $this, 'category_callback' ), // Callback
            'smart-setting-admin', // Page
            'setting_section_id'		// Section           
        );
		add_settings_field(
            'attributes', // ID
            __('Attributes','smart-related-products-for-woocommerce'), // Title 
            array( $this, 'attributes_callback' ), // Callback
            'smart-setting-admin', // Page
            'setting_section_id'		// Section           
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
		if( isset( $input['posts_per_page'] ) ){
			$new_input['posts_per_page'] =  $input['posts_per_page'];
		}
		if( isset( $input['attributes'] ) ){
			$new_input['attributes'] =  $input['attributes'];
		}
		if( isset( $input['category'] ) ){
			$new_input['category'] =  $input['category'];
		}
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info(){
		echo '<div id="contact"><h1>'.__('Version','smart-related-products-for-woocommerce').': 0.2</h1><h1>'.__('Author','smart-related-products-for-woocommerce').': '.__('Anton Drobyshev','smart-related-products-for-woocommerce').'</h1><h1>'.__('Contacts','smart-related-products-for-woocommerce').':</h1><h3>Email: antondrob@bk.ru</h3><h3>Facebook: <a href="https://www.facebook.com/anton.drobyshev.98" target="_blank">https://www.facebook.com/anton.drobyshev.98</a></h3><h3>'.__('VK','smart-related-products-for-woocommerce').': <a href="https://vk.com/antoshadrobyshev" target="_blank">https://vk.com/antoshadrobyshev</a></h3></div>';
    }
	
	public function posts_per_page_callback(){
			echo '<input id="posts_per_page" name="smart_related_products_option[posts_per_page]" type="number" value="'.get_option('smart_related_products_option')['posts_per_page'].'" placeholder="'.__('10 by default', 'smart-related-products-for-woocommerce').'" />';
	}
	
	public function category_callback(){
		if(!is_null(get_option('smart_related_products_option')['category']) && get_option('smart_related_products_option')['category'] === 'on'){
			echo '<label><input id="category" name="smart_related_products_option[category]" type="checkbox" checked />'.__('Related products from its own category','smart-related-products-for-woocommerce').'</label>';
		}else{
			echo '<label><input id="category" name="smart_related_products_option[category]" type="checkbox" />'.__('Related products from its own category','smart-related-products-for-woocommerce').'</label>';
		}
	}
    /** 
     * Get the settings option array and print one of its values
     */
	public function attributes_callback(){
		$args = array(
			'object_type' => 'product'
		);
		$taxonomies = wc_get_attribute_taxonomies();
		echo '<select id="attributes" name="smart_related_products_option[attributes][]" multiple="multiple">';
		foreach($taxonomies as $tax){
			if(in_array($tax->attribute_name, get_option("smart_related_products_option")['attributes'])){
				echo '<option value="'.$tax->attribute_name.'" selected /> '.$tax->attribute_label.'</option>';
			}else{
				echo '<option value="'.$tax->attribute_name.'" /> '.$tax->attribute_label.'</option>';
			}
		}
		echo '</select>';
	}

}