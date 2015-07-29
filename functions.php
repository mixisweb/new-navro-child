<?php
if ( ! function_exists( 'et_show_cart_total' ) ) {
	function et_show_cart_total( $args = array() ) {
		if ( ! class_exists( 'woocommerce' ) ) {
			return;
		}

		$defaults = array(
				'no_text' => false,
		);

		$args = wp_parse_args( $args, $defaults );
		$pattern = "/<span ?.*>(.*)<\/span>/";
		preg_match($pattern, WC()->cart->get_cart_subtotal(), $matches);
		$total_price=$matches[1];
		printf(
		'<a href="%1$s" class="et-cart-info">
				<span class="cart-image"></span>
				<div class="cart-count">%2$s</div>
				%3$s
			</a>',
			esc_url( WC()->cart->get_cart_url() ),
			esc_html( WC()->cart->get_cart_contents_count() ),
			$total_price
		);
	}
}


add_action( 'wp_enqueue_scripts', 'true_include_myscript' );
function true_include_myscript(){
	wp_enqueue_script( 'mixis_script', get_stylesheet_directory_uri() . '/js/mixis.js', array('jquery'), null, true );
}

/* ====== Change upload filename ====== */
add_filter('sanitize_file_name', 'rename_upfile', 10, 2);
function rename_upfile($filename, $filename_raw) {
	date_default_timezone_set('Europe/Kiev');
	$info	= pathinfo($filename);
	$ext	= empty($info['extension']) ? '' : '.' . $info['extension'];
	$date	= date('Y-m-d-H-i-s');
	$new 	= $date.$ext;
	if( $new != $filename_raw ) {
		$new = sanitize_file_name( $new );
	}
	return $new;
}


/* ====== Change url back to shop button ====== */
function wc_empty_cart_redirect_url() {
	return get_site_url().'/shop/';
}
add_filter( 'woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url' );
add_filter('woocommerce_continue_shopping_redirect', 'wc_empty_cart_redirect_url');

/* ====== Change dafault catalog ordering ====== */
add_filter( 'woocommerce_get_catalog_ordering_args', 'custom_woocommerce_get_catalog_ordering_args' );
function custom_woocommerce_get_catalog_ordering_args( $args ) {
  $orderby_value = isset( $_GET['orderby'] ) ? woocommerce_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
	if ( 'random_list' == $orderby_value ) {
		$args['orderby'] = 'date';
		$args['order'] = 'desc';
		$args['meta_key'] = 'price';
	}
	return $args;
}
add_filter( 'woocommerce_default_catalog_orderby_options', 'custom_woocommerce_catalog_orderby' );
add_filter( 'woocommerce_catalog_orderby', 'custom_woocommerce_catalog_orderby' );
function custom_woocommerce_catalog_orderby( $sortby ) {
	$sortby['date'] = 'Sort by newness';
	return $sortby;
}

/* ====== Modify breadcrumb ====== */
add_filter( 'woocommerce_breadcrumb_defaults', 'jk_woocommerce_breadcrumbs' );
function jk_woocommerce_breadcrumbs() {
    return array(
            'delimiter'   => ' - ',
            'wrap_before' => '<nav class="woocommerce-breadcrumb" itemprop="breadcrumb">',
            'wrap_after'  => '</nav>',
            'before'      => '<span>',
            'after'       => '</span>',
            'home'        => null,
        );
}

/* ====== Change checkout fields ====== */
add_filter( 'woocommerce_billing_fields', 'filter_billing_fields' );
function filter_billing_fields($fields){
	unset( $fields["billing_country"] );
	unset( $fields["billing_company"] );
	//unset( $fields["billing_last_name"] );
	unset( $fields["billing_address_2"] );
	//unset( $fields["billing_city"] );
	unset( $fields["billing_state"] );
	unset( $fields["billing_postcode"] );
	$fields["billing_first_name"]['class']=array('form-row-last');
	$fields["billing_last_name"]['class']=array('form-row-first');
	$fields["billing_city"]['class']=array('form-row-first');
	$fields["billing_address_1"]['class']=array('form-row-last');
	$fields["billing_phone"]['class']=array('form-row-first');
	$fields["billing_email"]['class']=array('form-row-last');
	return $fields;
}

/* ====== Modify HTML price ====== */
add_filter( 'woocommerce_get_price_html', 'navro_price_html', 100, 2 );
function navro_price_html( $price, $product ){
	$price=number_format($product->price,wc_get_price_decimals(),wc_get_price_decimal_separator(),wc_get_price_thousand_separator());
	return '<span class="amount">'.get_woocommerce_currency_symbol().' '.$price.'</span>';
}

/* Edit OpenGraph for Taxonomy product_cat */
/* Edit OpenGraph title */
add_filter( 'wpseo_opengraph_title', 'change_og_title' );
function change_og_title($title){
	global $wp_query;
	$tag = $wp_query->get_queried_object();
	if ($tag->taxonomy=='product_cat'){
		$option_name = 'product_cat_fb_title_' . $tag->term_id;
		$title = get_option( $option_name );
	}
	return $title;
}

/* Edit OpenGraph description */
add_filter( 'wpseo_opengraph_desc', 'change_og_description' );
function change_og_description($ogdesc){
	global $wp_query;
	$tag = $wp_query->get_queried_object();
	if ($tag->taxonomy=='product_cat'){
		$option_name = 'product_cat_fb_desc_' . $tag->term_id;
		$ogdesc = get_option( $option_name );
	}
	return $ogdesc;
}

/* Edit OpenGraph image */
add_action( 'wpseo_opengraph', 'change_og_image', 30 );
function change_og_image(){
	global $wp_query;
	$tag = $wp_query->get_queried_object();
	if ($tag->taxonomy=='product_cat'){
		$option_name = 'up_thumb_fb_img_' . $tag->term_id;
		$img = get_option( $option_name );
		if ( $img ) {
			echo '<meta property="og:image" content="'.$img.'" />'."\n";
		}
	}
	if ( is_shop() ) {
		$ogimg = WPSEO_Meta::get_value( 'opengraph-image', get_option( 'woocommerce_shop_page_id' ) );
		echo '<meta property="og:image" content="'.$ogimg.'" />'."\n";
	}
}


/* End edit OpenGraph for Taxonomy product_cat */

function image_setup_thumb(){
	add_image_size( 'product_cat-thumbnails', 1200 );
}
add_action( 'after_setup_theme', 'image_setup_thumb' );

/* Add thumbnail for Taxonomy product_cat */
add_action('product_cat_add_form_fields','add_category_edit_form_fields', 15, 2);
add_action('product_cat_edit_form_fields','edit_category_edit_form_fields', 15, 2);
add_action( 'created_product_cat', 'category_form_custom_field_save', 10, 2 );
add_action( 'edited_product_cat', 'category_form_custom_field_save', 10, 2 );

function add_category_edit_form_fields () {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('upload_media_widget', get_template_directory_uri(). '/js/img-upload.js' , array('jquery'));

	echo '<div class="form-field">';
	echo '<label>'.__( 'Facebook title:' ).'</label>';
	echo '<input type="text" name="product_cat_fb_title" id="product_cat_fb_title" value="">';
	echo '</div>';

	echo '<div class="form-field">';
	echo '<label>'.__( 'Facebook description:' ).'</label>';
	echo '<input type="text" name="product_cat_fb_desc" id="product_cat_fb_desc" value="">';
	echo '</div>';

	echo '<div class="form-field">';
	echo '<label>'.__( 'Facebook image:' ).'</label>';
	echo '<div class="product_cat_thumbnail" style="float:left;margin-right:10px;">';
	echo '<img src="'.wc_placeholder_img_src().'" class="thumb_fb_img" width="120px" height="60px" >';
	echo '</div>';
	echo '<div style="line-height:60px;">';
	echo '<input type="hidden" class="up_thumb_fb_img"  name="up_thumb_fb_img"/>';
	echo '<input class="up_thumb_fb_button button" type="submit" value="Upload Image">';
	echo '<input class="rm_thumb_fb_button button" type="submit" style="display:none;" value="Remove Image">';
	echo '</div>';
	echo '</div>';
}

function edit_category_edit_form_fields($tag){
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('upload_media_widget', get_template_directory_uri(). '/js/img-upload.js' , array('jquery'));

	$option_name = 'product_cat_fb_title_' . $tag->term_id;
	$product_cat_fb_title = get_option( $option_name );
	$option_name = 'product_cat_fb_desc_' . $tag->term_id;
	$product_cat_fb_desc = get_option( $option_name );
	$option_name = 'up_thumb_fb_img_' . $tag->term_id;
	$up_thumb_fb_img = get_option( $option_name );
	if(empty($up_thumb_fb_img)){ $up_thumb_fb_img=wc_placeholder_img_src();}

	echo '<tr class="form-field">';
	echo '<th scope="row" valign="top"><label>'.__( 'Facebook title:' ).'</label></th>';
	echo '<td><input type="text" name="product_cat_fb_title" id="product_cat_fb_title" value="'.esc_attr( $product_cat_fb_title ).'"></td>';
	echo '</tr>';

	echo '<tr class="form-field">';
	echo '<th scope="row" valign="top"><label>'.__( 'Facebook description:' ).'</label></th>';
	echo '<td><input type="text" name="product_cat_fb_desc" id="product_cat_fb_desc" value="'.esc_attr( $product_cat_fb_desc ).'"></td>';
	echo '</tr>';

	echo '<tr class="form-field">';
	echo '<th scope="row" valign="top"><label>'.__( 'Facebook image:' ).'</label></th>';
	echo '<td><div class="product_cat_thumbnail" style="float:left;margin-right:10px;">';
	echo '<img src="'.$up_thumb_fb_img.'" class="thumb_fb_img" width="120px" height="60px" >';
	echo '</div>';
	echo '<div style="line-height:60px;">';
	echo '<input type="hidden" class="up_thumb_fb_img" name="up_thumb_fb_img" value="'.$up_thumb_fb_img.'"/>';
	echo '<input class="up_thumb_fb_button button" type="button" value="Upload Image">';
	echo '<input class="rm_thumb_fb_button button" type="button" value="Remove Image">';
	echo '</div></td>';
	echo '</tr>';
}

function category_form_custom_field_save( $term_id) {

	if ( isset( $_POST['product_cat_fb_title'] ) ) {
		$option_name = 'product_cat_fb_title_' . $term_id;
		update_option( $option_name, $_POST['product_cat_fb_title'] );
	}
	if ( isset( $_POST['product_cat_fb_desc'] ) ) {
		$option_name = 'product_cat_fb_desc_' . $term_id;
		update_option( $option_name, $_POST['product_cat_fb_desc'] );
	}
	if ( isset( $_POST['up_thumb_fb_img'] ) ) {
		$option_name = 'up_thumb_fb_img_' . $term_id;
		update_option( $option_name, $_POST['up_thumb_fb_img'] );
	}
}

/**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'free_shipping_branch_update_order_meta' );

function free_shipping_branch_update_order_meta( $order_id ) {
	if ( ! empty( $_POST['free_shipping_branch'] ) ) {
		update_post_meta( $order_id, 'free_shipping_branch_no', sanitize_text_field( $_POST['free_shipping_branch'] ) );
	}
}

/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'free_shipping_branch_display_admin_order_meta', 10, 1 );

function free_shipping_branch_display_admin_order_meta($order){
	echo '<p><strong>'.__('Номер отделения "Новой Почты"').':</strong> ' . get_post_meta( $order->id, 'free_shipping_branch_no', true ) . '</p>';
}


?>