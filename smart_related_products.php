<?php
/*
Plugin Name: Smart Related Products for Woocommerce
Description: By defaualt Woocommerce displays related products by the category. Smart related products for Woocommerce can override this logic and show them by attributes.
Version:     0.1
Author:      Anton Drobyshev
Author URI:  https://vk.com/antoshadrobyshev
License:     GPL2License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	/*
	* Require class file with admin settings page
	*/
	require_once dirname(__FILE__ ) . '/includes/admin/class-smart-related-products.php';
	/*
	* Internationalizing the Plugin
	*/
	add_action('plugins_loaded', 'srp_load_textdomain');
	function srp_load_textdomain() {
		load_plugin_textdomain( 'smart-related-products-for-woocommerce', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
	}
add_action( 'admin_enqueue_scripts', 'srp_load_admin_scripts' );
	function srp_load_admin_scripts() {
		wp_enqueue_style( 'admin_css', plugin_dir_url(__FILE__ ) . 'multiselect-plugin/css/multi-select.css', false, '1.0.0' );
		wp_enqueue_style( 'admin_multiselect_css', plugin_dir_url(__FILE__ ) . 'assets/css/multiselect.css', false, '1.0.0' );
		wp_enqueue_script( 'admin_js', plugin_dir_url(__FILE__ ) . 'multiselect-plugin/js/jquery.multi-select.js', array('jquery'),'', true );
		wp_enqueue_script( 'admin_multiselect_js', plugin_dir_url(__FILE__ ) . 'assets/js/multiselect.js', array('jquery'),'', true );
		wp_localize_script( 'admin_multiselect_js', 'smart', array(
			'active_title' => __( "Active", 'smart-related-products-for-woocommerce' ),
			'non_active_title' => __( "Not active", 'smart-related-products-for-woocommerce' )
		));
	}
add_action('wp_enqueue_scripts', 'srp_load_front_scripts');
	function srp_load_front_scripts(){
		if(is_product()){
			wp_enqueue_style( 'slickStyle', plugin_dir_url(__FILE__ ).'slick/slick.css' );
			wp_enqueue_style( 'admin_multiselect_css', plugin_dir_url(__FILE__ ) . 'assets/css/slider.css', false, '1.0.0' );
			wp_enqueue_script('slickScript', plugin_dir_url(__FILE__ ).'slick/slick.min.js', array('jquery'), '', true);
			wp_enqueue_script('relatedAjax', plugin_dir_url(__FILE__ ).'assets/js/relatedAjax.js', array('jquery'));
			wp_localize_script('relatedAjax', 'ajaxData', array(
				'ajaxurl'	=>	admin_url('admin-ajax.php'),
				'post_id'	=>	get_the_ID(),
			));
		}
	}
	if( wp_doing_ajax()){
	add_action('wp_ajax_related_ajax', 'srp_related_ajax_handler');
	add_action('wp_ajax_nopriv_related_ajax', 'srp_related_ajax_handler');
	function srp_related_ajax_handler(){
		$post_id = esc_attr($_POST['post_id']);
		$tax_query = array('relation' => 'AND');
		$a = get_option("smart_related_products_option")['attributes'];
		$posts_per_page = get_option('smart_related_products_option')['posts_per_page'];
		if(empty($posts_per_page)){
			$posts_per_page = 10;
		}
		if($a){
			foreach($a as $b){
				array_push($tax_query, array(
					'taxonomy' => 'pa_'.$b,
					'field'    => 'slug',
					'terms'    => wp_get_post_terms( $post_id, 'pa_'.$b, array('fields' => 'slugs') )
				));
			}
		}
		if(array_key_exists('category', get_option('smart_related_products_option')) && get_option('smart_related_products_option')['category'] === 'on'){
			array_push($tax_query, array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => wp_get_post_terms( $post_id, 'product_cat', array('fields' => 'slugs') )
			));
		}
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => $posts_per_page,
			'post__not_in' => array($post_id),
			'meta_query' => array(
				array(
					'key' => '_stock_status',
					'value' => 'instock',
					'compare' => '='
				)
			),
			'tax_query' => $tax_query
		);
		$query = new WP_Query($args);
		if ( $query->have_posts() ) {
			?> <section class="related-products">
			<center><h2><?php esc_html_e( 'Related products', 'woocommerce' ); ?></h2></center> <?php
			woocommerce_product_loop_start();
			while ( $query->have_posts() ) {
				$query->the_post();
				wc_get_template_part( 'content', 'product' );
			}
			woocommerce_product_loop_end();
			?> </section> <?php
		}
		wp_reset_postdata();
		wp_die();
	}
}
/*
* Удалим дефолтные похожие товары
*/
add_filter('woocommerce_related_products_args', '__return_empty_array', 500);
add_filter ('woocommerce_ajax_variation_threshold','woocommerce_ajax_variation_threshold_more',10,2);
function woocommerce_ajax_variation_threshold_more($count,$product) {
	return 500;
}
if( is_admin() )
    $my_settings_page = new SRP_SettingsPage();
}