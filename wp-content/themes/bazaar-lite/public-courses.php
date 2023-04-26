<?php 
/* Template name: Public Courses Page */

	get_header(); 
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$args = array(  
        'post_type' => 'product',
        'post_status' => 'publish',
        'paged' => $paged, 
        'posts_per_page' => 10
    );

    $loop = new WP_Query($args);
   
	?>

	 


<div class="container">
	<div class="course_heading my-5">
		<h1 class="fw-bold">Search Public Courses</h1>
	</div>
	<div class="row search_course my-5">
		<div class="col-md-3 my-2 input_box">
			<input class="form-control col-md-10" type="search" placeholder="Search Course"> 
			<button class="text-white btn btn-info col-md-2"><i class="fa fa-search" aria-hidden="true"></i></button>
		</div>
		<div class="col-md-3 my-2">
			<select class="form-control" style="width:100% !important">
					<option value="volvo">Select Venue</option>
					<option value="volvo">Mohali</option>
					<option value="saab">Ambala</option>
					<option value="mercedes">Punjab</option>
					<option value="audi">Chandigarh</option>
			</select>
		</div>
		<div class="col-md-3 my-2">
			<select class="form-control" style="width:100% !important">
					<option value="">Sort by Popularity</option>
					<option value="">Sort by Rating</option>
					<option value="">Sort by Price low to high</option>
					<option value="">Sort by Price high to low</option>
					<option value="">Sort by Name A - Z</option>
					<option value="">Sort by Name Z - A</option>
					<option value="">Sort by Date</option>
			</select>
		</div>
		<div class="col-md-3 my-2 d-flex input_box">
			<input type="date" id="" name="" class="form-control col-md-10">
			<button class="text-white btn btn-info col-md-2"><i class="fa fa-search" aria-hidden="true"></i></button>
		</div>

	</div>
	<div class="row">
		<div class="col-md-12 ">
			<div class ="table-container fixed-tbl-footer table-responsive">
				<table class="table table-striped table-bordered"style="font-family:'Myriad Pro Light', Sans-serif;">
					<thead>
						<tr>
							<th class="fw-bold align-middle" scope="col">Image</th>
							<th class="fw-bold align-middle" scope="col">Name</th>
							<th class="fw-bold align-middle" scope="col">Description</th>
							<th class="fw-bold align-middle" scope="col">Venue</th>
							<th class="fw-bold" scope="col">Course Date</th>
							<th class="fw-bold" scope="col">Course Duration</th>
							<th class="fw-bold align-middle" scope="col">Price</th>
							<th class="fw-bold" scope="col">Book Now</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						while ( $loop->have_posts() ) : $loop->the_post(); 
						 ?>
						
						<tr>
						<td><?php the_post_thumbnail("small_thumbnail"); ?></td>
						<td><?php the_title(); ?></td>
						<td><?php the_excerpt(); ?></td>
						<td><?php echo get_post_meta(get_the_ID(), 'course_venue', true); ?></td>
						<td><?php echo get_post_meta(get_the_ID(), 'course_date', true); ?></td>
						<td><?php echo get_post_meta(get_the_ID(), 'course_duration', true); ?></td>
						<td><?php $product = wc_get_product( get_the_ID() ); /* get the WC_Product Object */ ?>
							<p><?php echo $product->get_price_html(); ?></p></td>
						<td><?php woocommerce_template_loop_add_to_cart();?></td>
					</tr>
					
						<?php endwhile; 
						wp_reset_postdata(); ?>
					
					<!-- <tr>
						<td scope="row"></td>
						<td>MCA and DoLS</td>
						<td>This course is aimed at people caring for individuals who… </td>
						<td>Mohali</td>
						<td>2023-04-20</td>
						<td>3 Hrs</td>
						<td>$ 1.0</td>
						<td><button type="button" class="text-white fw-bold btn btn-info">BOOK</button></td>
					</tr>
						<tr>
						<td scope="row"></td>
						<td>MCA and DoLS</td>
						<td>This course is aimed at people caring for individuals who… </td>
						<td>Mohali</td>
						<td>2023-04-20</td>
						<td>3 Hrs</td>
						<td>$ 1.0</td>
						<td><button type="button" class="text-white fw-bold btn btn-info">BOOK</button></td>
					</tr> -->
					</tbody>
				</table>
				<nav>
					<ul>
						<li><?php previous_posts_link( '&laquo; PREV', $loop->max_num_pages) ?></li> 
						<li><?php next_posts_link( 'NEXT &raquo;', $loop->max_num_pages) ?></li>
					</ul>
				</nav>
			</div>	
		</div>
	</div>
</div>

<style>
	.course_heading{
		font-family: 'Myriad Pro';
		font-size: 20px;
	}
	table, td {
		font-family: "Myriad Pro Light", Sans-serif;
	} 
table, th {
		font-family: "Myriad Pro";
	} 
	.table-bordered td{
		border: none !important; 
		border-right: solid 1px #ccc !important;
		}
		.search_course{
			margin-top: 20px;
			margin-bottom: 40px;
			font-family: "Myriad Pro Light", Sans-serif;
		}
	.form-control {
       width: 100% ;
	}
	input.form-control.col-md-10 {
    border-radius: 2px 2px 0 0;
}
.col-md-3.my-2.input_box {
    display: flex; 
}
.my-2{
	margin-top: 20px;
	margin-bottom: 20px;
}
</style>


<!-- <script type="text/javascript" >
    jQuery(document).ready(function($) {
        jQuery("#search_venue").click(function(){
            var search_val = jQuery('#product_search').val();
        alert(search_val);
        ajaxurl = '<?php //echo admin_url( 'admin-ajax.php' ) ?>'; // get ajaxurl

        var data = {
            'action': 'get_filtered_products', // your action name 
            'search_val': search_val // some additional data to send
        };

        jQuery.ajax({
            url: ajaxurl, // this will point to admin-ajax.php
            type: 'POST',
            data: data,
            success: function (response) {
                console.log(response); 
                jQuery(".row.masonry").html(response)               
            }
        });

         });
    });
    </script>  -->



	
    <?php get_footer(); ?>