<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$course_venue = get_post_meta(get_the_ID(), 'course_venue', true);
$args = array(
	'post_type' => 'product',
	'post_status' => 'publish',
	'posts_per_page' => 10,
	'meta_query' => array(
		'relation' => 'AND',
		array(
			array(
				'key'       => 'course_venue',
				'value'     => $course_venue,
				'compare'   => '=',
			)
		)
	)
);

$loop = new WP_Query($args);

if ( $related_products ) : ?>
	</div>

	<div class="col-md-4 related_venue_courses" id="related_course_venue_product_<?php the_ID(); ?>">
		
			
		<h5 class="venue_course_heading">Similar Venue Related Courses</h5>
		<table  class="table table-striped table-bordered" style="overflow-x:auto;">
			<tbody>	
				<!-- <tr>
					<th colspan="3" style="background-color: #31BBEB; color:#fff;">Similar Venue Related Course</th>
				</tr> -->
				<?php if($loop->post_count != 0)
						{
							while ( $loop->have_posts() ) : $loop->the_post(); ?>
				<tr>
					<td><a href="<?php echo get_the_permalink(); ?>"><?php the_title(); ?></a>
							<p class="custom_date"><?php echo get_post_meta(get_the_ID(), 'course_date', true); ?></p>
				</td>
					<!-- <td><?php echo get_post_meta(get_the_ID(), 'course_date', true); ?></td> -->
					<td class="custom_add_to_cart"><?php woocommerce_template_loop_add_to_cart();?></td>		
					</tr>

					<?php endwhile;
					} else { ?>
					<tr>
						<td class="No_Record" colspan="8">No Record Found</td>
					</tr>
					<?php
					}
					wp_reset_postdata();
					?>
			</tbody>
		</table>


	</div>

    <div class="related-products">

	<section class="related products">

		<?php
		$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'bazaar-lite' ) );

		if ( $heading ) :
			?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		
		<?php woocommerce_product_loop_start(); ?>

			<?php foreach ( $related_products as $related_product ) : ?>

					<?php
					$post_object = get_post( $related_product->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

					wc_get_template_part( 'content', 'product' );
					?>

			<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>

	</section>
	<?php
endif;

wp_reset_postdata();
?>
<style>
.table-bordered > tbody > tr > td {
    border-bottom: 1px solid #d1d1;
    border-top: 0;
    border-left: 0;
    border-right: 0;
}
.table-bordered > tbody > tr > td {
    vertical-align: middle;
    }
.related-products {
	width:100%;
	float:left;
}
.venue_course_heading{
	background: #00B4E0;
    line-height: 50px;
    margin-bottom: 0;
    color: #fff;
    border: 1px solid #d1d1d1;
    padding: 0 8px;
}  
.custom_add_to_cart{
	line-height: 1.42857143;
}
a.button.wp-element-button.product_type_simple.add_to_cart_button.ajax_add_to_cart {
    margin-top: 10px;
    margin-bottom: 5px;
}

.custom_date {
    color: #2ecc71;
    font-size: 12px;
	margin-bottom: 0px;
}
</style>