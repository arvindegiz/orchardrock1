<?php 
/* Template name: Public Courses Page */

	get_header(); 
	
	if(isset($_GET['searchByVenue']) && !empty($_GET['searchByVenue'])) {
		$args = array( 
			'post_type'      => 'product', // product, not products
			'post_status'    => 'publish',
			'meta_key' => 'course_venue',
			'meta_value' => $_GET['searchByVenue'],  
			'posts_per_page' => 5 // change this based on your needs
		  );
	} else {
		$args = array(  
			'post_type' => 'product',
			'post_status' => 'publish',
			'paged' => $paged, 
			'posts_per_page' => 5
		);
	}

    $loop = new WP_Query($args);
	?>

	 


<div class="container">
	<div class="course_heading my-5">
		<h1 class="fw-bold">Search Public Courses</h1>
	</div>
	<div class="row search_course my-5">
		<div class="col-md-3 my-2 input_box">
			<input class="form-control col-md-10" type="search" name="product_search" id="product_search" placeholder="Search Course"> 
			<button class="text-white btn btn-info col-md-2" id="searchsubmit"><i class="fa fa-search" aria-hidden="true"></i></button>
		</div>
		<div class="col-md-3 my-2">
			<select class="form-control" id="searchByVenue" style="width:100% !important">
					<option value="volvo">Select Venue</option>
					<option value="Mohali">Mohali</option>
					<option value="Ambala">Ambala</option>
					<option value="Punjab">Punjab</option>
					<option value="Chandigarh">Chandigarh</option>
			</select>
		</div>
		<div class="col-md-3 my-2">
			<select class="form-control" id="sortByAttribute" style="width:100% !important">
					<option value="price_asc">Sort by Price low to high</option>
					<option value="price_desc">Sort by Price high to low</option>
					<option value="name_asc">Sort by Name A - Z</option>
					<option value="name_desc">Sort by Name Z - A</option>
					<option value="date_sort">Sort by Date</option>
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
						while ( $loop->have_posts() ) : $loop->the_post();  ?>
						
						<tr>
						<td><?php the_post_thumbnail("small_thumbnail"); ?></td>
						<td><?php the_title(); ?></td>
						<td> <?php echo get_excerpt(); ?></td>
						<td><?php echo get_post_meta(get_the_ID(), 'course_venue', true); ?></td>
						<td><?php echo get_post_meta(get_the_ID(), 'course_date', true); ?></td>
						<td><?php echo get_post_meta(get_the_ID(), 'course_duration', true); ?></td>
						<td><?php $product = wc_get_product( get_the_ID() ); /* get the WC_Product Object */ ?>
							<p><?php echo $product->get_price_html(); ?></p></td>
						<td ><?php woocommerce_template_loop_add_to_cart();?></td>
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
				<nav class="center">
					<ul class="pagination">
						<li>
						<?php
						$big = 999999999;
						echo paginate_links( array(
							'base' => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
							'format' => '?paged=%#%',
							'current' => max( 1, get_query_var('paged') ),
							'total' => $loop->max_num_pages,
							'prev_text'    => __('<span class="direction-arrow"> < </span>'),
							'next_text'    => __('<span class="direction-arrow"> > </span>'),
						) );
						?>
						</li>
					</ul>
				</nav> 				
			</div>	
		</div>
	</div>
</div>

<style>
	/* Table css start */
	.course_heading{
		font-family: 'Myriad Pro';
		font-size: 20px;
	}
	table, td {
		font-family: "Myriad Pro Light", Sans-serif;
	} 
	table, th {
		font-family: "Myriad Pro";
		vertical-align: top;
	} 
	th{
		background-color: #273842;
		color:#fff;
		vertical-align: top;
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
/* add to cart  */
	a.button.wp-element-button.product_type_simple.add_to_cart_button.ajax_add_to_cart{
		margin: 10px auto 10px auto;
		width: 90px;
		background-color: #00B4E1;
		/* border-color: #d1d1d1; */
		/* font-weight: bold; */
		border-radius: 5px ;
		border : 1px solid #d1d1d1;
	}
	/* Table css start */

	/*Pagination CSS*/      

	ul.pagination {
		display: inline-block;
		padding: 0;
		margin: 10;
	}

	ul.pagination li {display: inline;}

	ul.pagination li a {
		color: white;
		float: left;
		padding: 8px 16px;
		text-decoration: none;
		transition: background-color .3s;
		border: 1px solid #ddd;
		background: #21C04E;
	}

	ul.pagination li a.active {
		background-color: #4CAF50;
		color: white;
		border: 1px solid #4CAF50;
	}

	ul.pagination li a:hover:not(.active) {background-color: #fff;}

	div.center {text-align: center;}

	a.prev.page-numbers {
		background-color: #21C04E!important;
		margin-right: 10px!important;
		
	}
	a.next.page-numbers {
		background-color: #21C04E!important;
		margin-left: 10px!important;
	}

	a.prev.page-numbers:hover {
		color: white;
		background-color: #21C04E!important;
		margin-right: 10px!important;
		
	}
	a.next.page-numbers:hover {
		color: white;
		background-color: #21C04E!important;
		margin-left: 10px!important;
	}
	a.page-numbers {
    margin-left: 5px!important;
    margin-right: 5px !important;
}

/*Pagination CSS Ends here*/
</style>
<script type="text/javascript" >
    jQuery(document).ready(function($) {
        $(document).on("change", "#searchByVenue" ,function(){
            var search_val = jQuery(this).val();
			var current_url = window.location.origin+window.location.pathname;
			alert(current_url);
			window.location.href = current_url+'?searchByVenue='+search_val;
             
        
        // ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ) ?>'; // get ajaxurl

        // var data = {
        //     'action': 'get_filtered_products', // your action name 
        //     'search_val': search_val // some additional data to send
        // };

        // jQuery.ajax({
        //     url: ajaxurl, // this will point to admin-ajax.php
        //     type: 'POST',
        //     data: data,
        //     success: function (response) {
        //         console.log(response); 
        //         jQuery(".row.masonry").html(response)               
        //     }
        // });


        // jQuery("#search_course_date").click(function(){
        //     var search_val = jQuery('#course_date').val();
        // alert(search_val);
        // ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ) ?>'; // get ajaxurl

        // var data = {
        //     'action': 'get_filtered_products', // your action name 
        //     'search_val': search_val // some additional data to send
        // };

        // jQuery.ajax({
        //     url: ajaxurl, // this will point to admin-ajax.php
        //     type: 'POST',
        //     data: data,
        //     success: function (response) {
        //         console.log(response); 
        //         jQuery(".row.masonry").html(response)               
        //     }
        // });

         });
    });
    </script> 



	
    <?php get_footer(); ?>