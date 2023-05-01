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
$category = get_the_terms( get_the_ID(), 'product_cat' ); 

$args = array(
	'post_type' => 'product',
	'post_status' => 'publish',
	'posts_per_page' => 10,
	'post__not_in' => array(get_the_ID()),
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


$category_related_args = array(
	'post_type' => 'product',
	'post_status' => 'publish',
	'tax_query' => array(
		'relation' => 'AND',
		array(
			'taxonomy' => 'product_cat',
			'field' => 'slug',
			'terms' => $category[0]->slug
		)
	)
);

$total_products = new WP_Query($category_related_args);

$category_related_args['posts_per_page'] = 4;
$category_related_loop = new WP_Query($category_related_args); ?>

</div>
	<div class="col-md-4 related_venue_courses" id="related_course_venue_product_<?php the_ID(); ?>">
	
		<h5 class="venue_course_heading">Similar Venue Related Courses</h5>
		<table  class="table table-striped table-bordered table-container-related-venue-courses" style="overflow-x:auto;">
			<tbody>	
				<?php if($loop->post_count != 0)
						{
							while ( $loop->have_posts() ) : $loop->the_post(); 
							$new_course_date = '';
							$course_date = get_post_meta( get_the_ID(), 'course_date', true );
							if(isset($course_date) && !empty($course_date)) {
								$timestamp = strtotime($course_date);
								$new_course_date = date('M d, Y', $timestamp);
							}
							?>
				<tr>
					<td><a href="<?php echo get_the_permalink(); ?>"><?php the_title(); ?></a>
							<p class="custom_date"><?php echo $new_course_date; ?></p>
				</td>
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
		<h1>Related Courses</h1>
		<?php	if($category_related_loop->post_count != 0)  {	
			while ( $category_related_loop->have_posts() ) : $category_related_loop->the_post();  ?>
			<div class="col-md-3 related-product">
				<div class="related-product-inner">
					<div class="related-post-image">
							<?php if ( has_post_thumbnail() ) {
									the_post_thumbnail("small_thumbnail");
									} else {
									echo '<img src="'.get_site_url().'/wp-content/uploads/woocommerce-placeholder.png" width="50" hieght="50"/>';
							}; ?>
					</div>
					<div class="related-product-content">
						<a class="related-product-title" href="<?php echo get_the_permalink(); ?>"><?php the_title(); ?></a>
						<?php $product = wc_get_product( get_the_ID() ); /* get the WC_Product Object */ ?>
						<p><?php echo $product->get_price_html(); ?></p>
					</div>
					<?php woocommerce_template_loop_add_to_cart();?>
				</div>
			</div>
		<?php endwhile;
		} else { ?>
			<span class="No_Record" colspan="8">No Course Found</span>
		<?php
		}
		wp_reset_postdata();?>
	</div>
	<?php	if($total_products->post_count > 4)  {	?>
		<div class="all_realated_products">
			<a class="" href="<?php echo get_site_url(); ?>/public-courses/?searchByCategory=<?php echo $category[0]->slug; ?>">More</a>
		</div>
	<?php } ?>

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
.related-products {
	float:left;
	width:100%;
	text-align:center;
}
.related-products h1 {
	color:#00B4E0;
	margin-bottom:40px;
}
.related-product a.button.wp-element-button.product_type_simple.add_to_cart_button.ajax_add_to_cart {
	margin-top:5px!important;
}
.related-product .related-product-content {
    margin-top: 10px;
    padding: 0 10px;
}
.related-product-inner {
	border: 1px solid #d1d1d1;
    float: left;
    width: 100%;
	margin-bottom: 20px;
}
.all_realated_products {
	float: left;
    width: 100%;
    text-align: center;
    margin: 30px 0 0;
}
.all_realated_products a {
    color: #fff!important;
    background: #00B4E0;
    padding: 8px 15px;
	text-decoration:none;
}
.related-product-title{
	color:#0FBB56;
} 
</style>