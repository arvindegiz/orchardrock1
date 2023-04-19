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

$course_date = get_post_meta( get_the_ID(), 'course_date', true );
$timestamp = strtotime($course_date);
$new_course_date = date('M d, Y');


?>
<p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>"><?php echo $product->get_price_html(); ?></p>

<p class="">Course Date: <?php echo $new_course_date; ?></p>
<p class="">Course Duration: <?php echo get_post_meta( get_the_ID(), 'course_duration', true ); ?> <i class="fa fa-clock-o" aria-hidden="true"></i></p>


