<?php
/* Template name: Public Courses Page */

	get_header();
	
	echo get_site_url()."<br>";
	$actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	echo $actual_link;
	global $wpdb;
	//Get course venue list
	$query = "SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key = 'course_venue' ORDER BY meta_value;";
	$venues = $wpdb->get_results( $query );
	
	$cat_args = array(
		'orderby'    => 'name',
		'order'      => 'asc',
		'hide_empty' => true,
	);
 
	$product_categories = get_terms( 'product_cat', $cat_args );

	$search_filter_values = array();


    $args = array(
		'post_type' => 'product',
		'post_status' => 'publish',
		'paged' => $paged,
		'posts_per_page' => 10
	);

	if(isset($_GET['searchByCourse']) && !empty($_GET['searchByCourse'])) {
		$filter_row = array();
		$filter_row['type'] = 'searchByCourse';
		$filter_row['value'] = $_GET['searchByCourse'];
		$search_filter_values[] = $filter_row;
		$args['s'] = $_GET['searchByCourse'];
	}

	if(isset($_GET['searchByVenue']) && !empty($_GET['searchByVenue'])) {
		$filter_row = array();
		$filter_row['type'] = 'searchByVenue';
		$filter_row['value'] = $_GET['searchByVenue'];
		$search_filter_values[] = $filter_row;

		$args['meta_query'] = array(
			'relation' => 'AND',
			array(
				array(
					'key'       => 'course_venue',
					'value'     => $_GET['searchByVenue'],
					'compare'   => '=',
				)
			)
		);   
		
	}

	if(isset($_GET['searchByCategory'])  && !empty($_GET['searchByCategory'])) {
		$filter_row = array();
		$search_filter = str_replace("-", " ", $_GET['searchByCategory']);
		$filter_row['type'] = 'searchByCategory';
		$filter_row['value'] = ucwords(strtolower($search_filter));
		$search_filter_values[] = $filter_row;

		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'product_cat',
				'field' => 'slug',
				'terms' => $_GET['searchByCategory']
			)
		);
	}

	if(isset($_GET['searchBydate']) && !empty($_GET['searchBydate'])) {
		$filter_row = array();
		$filter_row['type'] = 'searchBydate';
		$filter_row['value'] = $_GET['searchBydate'];
		$search_filter_values[] = $filter_row;

		$args['meta_query'] = array(
			'relation' => 'AND',
			array(
				array(
					'key'       => 'course_date',
					'value'     => $_GET['searchBydate'],
					'compare'   => '=',
				)
			)
		);   
	}

	if(isset($_GET['searchBySort']) && !empty($_GET['searchBySort'])) {
		$searchByValue = explode("_", $_GET['searchBySort']);
		$sort_by = "";
		$sort_order = $searchByValue[1];
	
		$is_meta_key = false;
		if($searchByValue[0] == 'name') {
			$sort_by = "post_title";
		} else if($searchByValue[0] == 'price') {
			$is_meta_key = true;
			$sort_by = "_price";
		}else if($searchByValue[0] == 'date') {
			$sort_order == 'desc' ? 'asc': 'desc'; 
			$is_meta_key = true;
			$sort_by = "course_date";
		}

		$filter_row = array();
		$filter_row['type'] = 'searchBySort';
		$filter_row['value'] = ucfirst($searchByValue[0]).": ".ucfirst($searchByValue[1]);
		$search_filter_values[] = $filter_row;

		$args['orderby'] = $sort_by;
		$args['order'] = $sort_order;
		if($is_meta_key) {
			$args['meta_key'] = $sort_by;
		}
	}
	


	   



	// if(isset($_GET['searchByVenue']) && !empty($_GET['searchByVenue'])) {
	// 	$search_filter_value = $_GET['searchByVenue'];
	// 	$args = array(
	// 		'post_type'      => 'product', 
	// 		'post_status'    => 'publish',
	// 		'meta_key' => 'course_venue',
	// 		'meta_value' => $_GET['searchByVenue'],
	// 		'posts_per_page' => 10 
	// 	  );
	// }elseif(isset($_GET['searchByCourse']) && !empty($_GET['searchByCourse'])) {
	// 	$search_filter_value = $_GET['searchByCourse'];
	// 	$args = array(
	// 		'post_type'      => 'product', 
	// 		'post_status'    => 'publish',
	// 		's' => $_GET['searchByCourse'],
	// 		'posts_per_page' => 10 
	// 	  );
	// }elseif(isset($_GET['searchBySort']) && !empty($_GET['searchBySort'])) {
	// 	$searchByValue = explode("_", $_GET['searchBySort']);
	// 	$sort_by = "";
	// 	$sort_order = $searchByValue[1];
	// 	$search_filter_value = $_GET['searchBySort'];
	// 	$is_meta_key = false;
	// 	if($searchByValue[0] == 'name') {
	// 		$sort_by = "post_title";
	// 	} else if($searchByValue[0] == 'price') {
	// 		$is_meta_key = true;
	// 		$sort_by = "_price";
	// 	}else if($searchByValue[0] == 'date') {
	// 		$sort_order == 'desc' ? 'asc': 'desc'; 
	// 		$is_meta_key = true;
	// 		$sort_by = "course_date";
	// 	}

	// 	$args = array(
	// 		'post_type'      => 'product', 
	// 		'post_status'    => 'publish',
	// 		'orderby' => $sort_by,
	// 		'order' => $sort_order,
	// 		'posts_per_page' => 10 
	// 	);

	// 	if($is_meta_key) {
	// 		$args['meta_key'] = $sort_by;
	// 	}
	// }else if(isset($_GET['searchByCategory'])) {
	// 	$search_filter_value = str_replace("-", " ", $_GET['searchByCategory']);
	// 	$search_filter_value = ucwords(strtolower($search_filter_value));
	// 	$args = array(
	// 		'post_type'      => 'product', 
	// 		'post_status'    => 'publish',
	// 		'tax_query' => array(
	// 			'relation' => 'AND',
	// 			array(
	// 				'taxonomy' => 'product_cat',
	// 				'field' => 'slug',
	// 				'terms' => $_GET['searchByCategory']
	// 			)
	// 		),
	// 		'posts_per_page' => 10 
	// 	);
	// }else if(isset($_GET['searchBydate'])) {
	// 	$search_filter_value = $_GET['searchBydate'];
	// 	$args = array(
	// 		'post_type'      => 'product', 
	// 		'post_status'    => 'publish',
	// 		'meta_key' => 'course_date',
	// 		'meta_value' => $_GET['searchBydate'],
	// 		'posts_per_page' => 10 
	// 	);
	// }else{
	// 	$args = array(
	// 		'post_type' => 'product',
	// 		'post_status' => 'publish',
	// 		'paged' => $paged,
	// 		'posts_per_page' => 10
	// 	);
	// }

	// echo "<pre>";
	// print_r($args); die;
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
		<div class="col-md-2 my-2">
			<select class="form-control" id="searchByVenue" style="width:100% !important">
				<option value="" >Select Venue</option>
					<?php 
					if(!empty($venues)) {
						foreach($venues as $venue) { ?>
							<option value="<?php echo $venue->meta_value; ?>" <?php if(isset($_GET['searchByVenue']) && !empty($_GET['searchByVenue']) &&  $_GET['searchByVenue'] == $venue->meta_value ) { echo "selected"; } ?>><?php echo $venue->meta_value; ?></option>;
						<?php }
					}
					?>
			</select>
		</div>
		<div class="col-md-2 my-2">
			<select class="form-control" id="searchByCategory" style="width:100% !important">
				<option value="" >Select Category</option>
					<?php 
					if(!empty($product_categories)) {
						foreach($product_categories as $categories) { ?>
							<option value="<?php echo $categories->slug; ?>" <?php if(isset($_GET['searchByCategory']) && !empty($_GET['searchByCategory']) &&  $_GET['searchByCategory'] == $categories->slug ) { echo "selected"; } ?>><?php echo $categories->name; ?></option>;
						<?php }
					}
					?>
			</select>
		</div>
		<div class="col-md-2 my-2">
			<select class="form-control" id="sortByAttribute" style="width:100% !important">
					<option value="price_asc" <?php if(isset($_GET['searchBySort']) && !empty($_GET['searchBySort'])) {  echo $_GET['searchBySort']=='price_asc'?'selected':''; } ?>>Sort by Price low to high</option>
					<option value="price_desc" <?php if(isset($_GET['searchBySort']) && !empty($_GET['searchBySort'])) {  echo $_GET['searchBySort']=='price_desc'?'selected':''; } ?>>Sort by Price high to low</option>
					<option value="name_asc" <?php if(isset($_GET['searchBySort']) && !empty($_GET['searchBySort'])) {  echo $_GET['searchBySort']=='name_asc'?'selected':''; } ?>>Sort by Name A - Z</option>
					<option value="name_desc" <?php if(isset($_GET['searchBySort']) && !empty($_GET['searchBySort'])) {  echo $_GET['searchBySort']=='name_desc'?'selected':''; } ?>>Sort by Name Z - A</option>
					<option value="date_asc" <?php if(isset($_GET['searchBySort']) && !empty($_GET['searchBySort'])) {  echo $_GET['searchBySort']=='date_sort'?'selected':''; } ?>>Sort by Date</option>
			</select>
		</div>
		<div class="col-md-3 my-2 d-flex input_box">
			<input type="date" name="course_search_date" id="course_date" class="form-control col-md-10">
			<button class="text-white btn btn-info col-md-2" id="date_submit"><i class="fa fa-search" aria-hidden="true"></i></button>
		</div>
		<img class="hidden" id="spinner" src="<?php echo site_url(); ?>/wp-content/uploads/2023/04/Spin-1.3s-281px-1.svg" />

		<?php if(!empty($search_filter_values)) {  ?>
			<div class="col-md-12">
			<div class ="search_filtes_block">
			<?php
				foreach($search_filter_values as $filter) { ?>
					<span class ="search_filter_value"><?php echo $filter['value']; ?><i class="fa fa-times clear_filter" data-type = "<?php echo $filter['type']; ?>" aria-hidden="true"></i></span>
				<?php } ?>
			
				<a class = "clear_search_filter" href="javascript:void(0)">Clear Filter</a>
			</div>
		</div>
		<?php } ?>
		
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
							<th class="fw-bold align-middle" scope="col">Course Date</th>
							<th class="fw-bold align-middle" scope="col">Course Duration</th>
							<th class="fw-bold align-middle" scope="col">Price</th>
							<th class="fw-bold align-middle" scope="col">Book Now</th>
						</tr>
					</thead>
					<tbody>
						<?php
						// echo $loop->post_count;
						if($loop->post_count != 0)
						{
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
td.No_Record {
	text-align:center;
}
.course_heading{
	font-family: 'Myriad Pro';
	font-size: 20px;
}
table, td{
	font-family: "Myriad Pro Light", Sans-serif;
}
.clear_filter {
	margin-left:10px;
}
th.fw-bold.align-middle {
	font-family: "Myriad Pro";
	vertical-align: middle;
	background-color: #273842;
	color:#fff;
}

.table-bordered td{
	border: none !important;
	border-right: solid 1px #ccc !important;
	}
.search_course{
	margin-top: 20px;
	margin-bottom: 40px;
	font-family: "Myriad Pro Light", Sans-serif;
	position:relative;
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
#spinner {
	position: absolute;
	left: 0;
	right: 0;
	top: 0;
	bottom: 0;
	margin: auto;
}
/* add to cart  */
a.button.wp-element-button.product_type_simple.add_to_cart_button.ajax_add_to_cart{
	margin: 10px auto 10px auto;
	width: 90px;
	background-color: #00B4E1;
	border-radius: 5px ;
	border : 1px solid #d1d1d1;
}
	a.added_to_cart.wc-forward {
	display: none;
}
/* clear filter */
.search_filter_value{
	padding: 8px;
	color:#fff;
	margin: 10px auto 10px auto;
	background-color: #00B4E1;
	border-color: #d1d1d1;
	border-radius: 5px ;
	border : 1px solid #d1d1d1;
}
.clear_search_filter{
	padding: 8px;
	color:#fff;
	margin: 10px auto 10px 10px;
	background-color: #000;
	border-color: #d1d1d1;
	border-radius: 5px ;
	border : 1px solid #d1d1d1;
}
.clear_search_filter:hover{
	color:#fff !important;
}
/* Table css start */
/*Pagination CSS*/
	span.page-numbers.current{
	background-color: #4CAF50;
	color:#fff;
}
ul.pagination {
	display: inline-block;
	padding: 0;
	margin: 10;
}
ul.pagination li {display: inline;}

ul.pagination li a {
	float: left;
	padding: 8px 16px;
	text-decoration: none;
	transition: background-color .3s;
	border: 1px solid #ddd;	
}
ul.pagination li a.active {
	background-color: red;
	color: white;
	border: 1px solid #fff;
}
ul.pagination li a:hover:not(.active) {background-color: #4CAF50;}

div.center {text-align: center;}

a.prev.page-numbers {
	color:black;
	margin-right: 10px!important;
}
a.next.page-numbers {
	color:black;
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
			jQuery('body').css("opacity", "0.5");
			jQuery('#spinner').removeClass('hidden');
            var search_val = jQuery(this).val();
			var matches = window.location.href.match(/[a-z\d]+=[a-z\d]+/gi);
			var count = matches? matches.length : 0;
			var param_add = count > 0  ? "&" : "?";

			const cut_url = new URL(window.location.href);
			if (cut_url.searchParams.has('searchByVenue')) {
				cut_url.searchParams.delete('searchByVenue');
			}
			window.location.href = cut_url+param_add+'searchByVenue='+search_val;
        });

		$(document).on("click", ".clear_filter" ,function(){
			jQuery('body').css("opacity", "0.5");
			jQuery('#spinner').removeClass('hidden');
            var filter_type = jQuery(this).data("type");
			const url = new URL(window.location);
			url.searchParams.delete(filter_type);
			window.location.href = url;
        });

		$(document).on("change", "#searchByCategory" ,function(){
            var search_category = jQuery(this).val();
			if(search_category != '') {
				jQuery('body').css("opacity", "0.5");
				jQuery('#spinner').removeClass('hidden');

				var matches = window.location.href.match(/[a-z\d]+=[a-z\d]+/gi);
				var count = matches? matches.length : 0;
				var param_add = count > 0  ? "&" : "?";

				const cut_url = new URL(window.location.href);
				if (cut_url.searchParams.has('searchByCategory')) {
					cut_url.searchParams.delete('searchByCategory');
				}
				window.location.href = cut_url+param_add+'searchByCategory='+search_category;
			}
			
        });

		// Search by Course
		$(document).on("click", "#searchsubmit" ,function(){
			jQuery('body').css("opacity", "0.5");
			jQuery('#spinner').removeClass('hidden');
            var search_course = document.getElementById('product_search').value;
			
			var matches = window.location.href.match(/[a-z\d]+=[a-z\d]+/gi);
			var count = matches? matches.length : 0;
			var param_add = count > 0  ? "&" : "?";

			const cut_url = new URL(window.location.href);
			if (cut_url.searchParams.has('searchByCourse')) {
				cut_url.searchParams.delete('searchByCourse');
			}
			window.location.href = cut_url+param_add+'searchByCourse='+search_course;
        });
		// sort By Categoriy
		$(document).on("change", "#sortByAttribute" ,function(){
			jQuery('body').css("opacity", "0.5");
			jQuery('#spinner').removeClass('hidden');
            var search_val = jQuery(this).val();
			var matches = window.location.href.match(/[a-z\d]+=[a-z\d]+/gi);
			var count = matches? matches.length : 0;
			var param_add = count > 0  ? "&" : "?";

			const cut_url = new URL(window.location.href);
			if (cut_url.searchParams.has('searchBySort')) {
				cut_url.searchParams.delete('searchBySort');
			}
			window.location.href = cut_url+param_add+'searchBySort='+search_val;
        });
		// Search by Date
		$(document).on("click", "#date_submit" ,function(){
			jQuery('body').css("opacity", "0.5");
			jQuery('#spinner').removeClass('hidden');
			var search_date = jQuery("#course_date").val();

			var matches = window.location.href.match(/[a-z\d]+=[a-z\d]+/gi);
			var count = matches? matches.length : 0;
			var param_add = count > 0  ? "&" : "?";

			const cut_url = new URL(window.location.href);
			if (cut_url.searchParams.has('searchBydate')) {
				cut_url.searchParams.delete('searchBydate');
			}
			window.location.href = cut_url+param_add+'searchBydate='+search_date;	
		});
		// clear filter
		$(document).on("click", ".clear_search_filter" ,function(){
			jQuery('body').css("opacity", "0.5");
			jQuery('#spinner').removeClass('hidden');
			var clear_filter = jQuery(".search_filter_value").val();
			var current_url = window.location.origin+window.location.pathname;
			window.location.href = current_url;	
		});
    });
    </script>
    <?php get_footer(); ?>
