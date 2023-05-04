<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$new_course_date = '';
$course_date = get_post_meta( get_the_ID(), 'course_date', true );
if(isset($course_date) && !empty($course_date)) {
	$timestamp = strtotime($course_date);
	$new_course_date = date('M d, Y', $timestamp);
}

$course_duration = '';
$course_duration_data = get_post_meta( get_the_ID(), 'course_duration', true );
if(isset($course_duration_data) && !empty($course_duration_data)) {
	$course_duration = $course_duration_data;
}

$course_venue = '';
$course_venue_data = get_post_meta( get_the_ID(), 'course_venue', true );
if(isset($course_venue_data) && !empty($course_venue_data)) {
	$course_venue = $course_venue_data;
}

$tag = get_the_terms( $product->ID, 'product_tag' );
if($tag[0]->slug == 'public-course') {
	
?>
<script type="text/javascript">
jQuery(document).ready(function() 
{
    jQuery(".woocommerce div.product form.cart div.quantity").css("display", "block");
	jQuery(".woocommerce div.product form.cart .button").css("display", "block");
});
</script>
<p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>"><?php echo $product->get_price_html(); ?></p>
<?php } if (!empty($course_venue)) { ?>
<p class="addition_attr"><span class="price_addition_attr">Venue: </span><?php echo $course_venue; ?> </p>
<?php } if (!empty($new_course_date)) { ?>
<p class="addition_attr"><span class="price_addition_attr">Date: </span><?php echo $new_course_date; ?></p>
<?php } if (!empty($course_duration)) { ?>
<p class="addition_attr"><span class="price_addition_attr">Duration: </span><?php echo $course_duration; ?> <i class="fa fa-clock-o" aria-hidden="true"></i></p>
<?php }  ?>
<!-- <p class="addition_btn"><?php woocommerce_template_loop_add_to_cart();?></p> -->

<style>
	/* .button.single_add_to_cart_button.button.alt.wp-element-button {
		display: none !important;
	} 
	.button.product_type_simple.add_to_cart_button.ajax_add_to_cart.added::after {
    display: none !important;
}*/
	.price_addition_attr, .posted_in {
		color:#00B4E0;
		font-weight:600;
	} 
	.addition_attr {
		margin-bottom:0;
	}
	.wcppec-checkout-buttons.woo_pp_cart_buttons_div {
		display:none;
	}
	.woocommerce div.product form.cart div.quantity {
		display: none;
		margin-top: 20px;
		margin-bottom: 20px;
	}
	.woocommerce div.product form.cart .button {
		display: none;
		margin: 20px 0;
	}
	h1.product-title {
		margin-bottom:10px;
	}
	.woocommerce-product-details__short-description {
		margin-top: 15px;
	}
	.woocommerce div.product form.cart, .woocommerce div.product p.cart {
		margin:0;
	}
	.ppc-button-wrapper {
		display:none;
	}
	.addition_btn{
		position: absolute;
		margin-top: 13px;
		margin-left: 60px;
	}
	.woocommerce div.product form.cart .button {
		display: none;
		margin: 20px 0;
		border-radius: 5px;
		border: 1px solid #d1d1d1;
	}
	a.button.wc-forward.wp-element-button {
		border-radius: 5px;
		border: 1px solid #d1d1d1;
	}
</style>


