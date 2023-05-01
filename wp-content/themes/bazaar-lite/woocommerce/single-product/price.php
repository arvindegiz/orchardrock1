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



?>
<p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>"><?php echo $product->get_price_html(); ?></p>
<?php if (!empty($course_venue)) { ?>
<p class=""><span class="price_addition_attr">Venue: </span><?php echo $course_venue; ?> </p>
<?php } if (!empty($new_course_date)) { ?>
<p class=""><span class="price_addition_attr">Date: </span><?php echo $new_course_date; ?></p>
<?php } if (!empty($course_duration)) { ?>
<p class=""><span class="price_addition_attr">Duration: </span><?php echo $course_duration; ?> <i class="fa fa-clock-o" aria-hidden="true"></i></p>
<?php }  ?>

<style>
	.price_addition_attr {
		color:#00B4E0;
		font-weight:600;
	} 
</style>


