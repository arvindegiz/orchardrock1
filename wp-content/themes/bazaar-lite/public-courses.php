<?php
/* Template name: Public Courses Page */

	get_header();
	
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
		'product_tag'    => array('public-course'),
		'posts_per_page' => 10,
		'meta_query' => array(
			array(
				'key' => 'course_date',
				'value' => date("Y-m-d"), 
				'compare' => '>=', 
				'type' => 'DATE' 
			)
		)
	);

	$serach_course_value = '';
	if(isset($_GET['searchByCourse']) && !empty($_GET['searchByCourse'])) {
		$serach_course_value = $_GET['searchByCourse'];
		$filter_row = array();
		$filter_row['type'] = 'searchByCourse';
		$filter_row['value'] = $_GET['searchByCourse'];
		$filter_row['label'] = $filter_row['value'];
		$search_filter_values[] = $filter_row;
		$args['s'] = $_GET['searchByCourse'];
	}

	if(isset($_GET['searchByVenue']) && !empty($_GET['searchByVenue']) && !isset($_GET['searchBydate']) && empty($_GET['searchBydate'])) {
		$filter_row = array();
		$filter_row['type'] = 'searchByVenue';
		$filter_row['value'] = $_GET['searchByVenue'];
		$filter_row['label'] = $filter_row['value'];
		$search_filter_values[] = $filter_row;

		$args['meta_query'] = array(
			'relation' => 'AND',
			array(
				array(
					'key'       => 'course_venue',
					'value'     => $_GET['searchByVenue'],
					'compare'   => '=',
				)
				),
		);   
	}
	if(isset($_GET['searchByCategory'])  && !empty($_GET['searchByCategory'])) {
		$filter_row = array();
		$search_filter = str_replace("-", " ", $_GET['searchByCategory']);
		$filter_row['type'] = 'searchByCategory';
		$filter_row['value'] = ucwords(strtolower($search_filter));
		$filter_row['label'] = $filter_row['value'];
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

	$serach_course_date_value = '';
	if(isset($_GET['searchBydate']) && !empty($_GET['searchBydate']) && !isset($_GET['searchByVenue']) && empty($_GET['searchByVenue'])) {
		$serach_course_date_value = $_GET['searchBydate'];
		$filter_row = array();
		$filter_row['type'] = 'searchBydate';
		$filter_row['value'] = $_GET['searchBydate'];
		$timestamp = strtotime($_GET['searchBydate']);
		$filter_row['label'] = date('M d, Y', $timestamp);
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

	if(isset($_GET['sortByAttribute']) && !empty($_GET['sortByAttribute'])) {
		$searchByValue = explode("_", $_GET['sortByAttribute']);
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
		$filter_row['type'] = 'sortByAttribute';
		$filter_row['value'] = ucfirst($searchByValue[0]).": ".ucfirst($searchByValue[1]);
		$filter_row['label'] = $filter_row['value'];
		$search_filter_values[] = $filter_row;

		$args['orderby'] = $sort_by;
		$args['order'] = $sort_order;
		if($is_meta_key) {
			$args['meta_key'] = $sort_by;
		}
	}

	if(isset($_GET['searchByVenue']) && !empty($_GET['searchByVenue']) && isset($_GET['searchBydate']) && !empty($_GET['searchBydate'])) {
		$filter_index = count($search_filter_values);
		$filter_venue_row = array();
		$filter_venue_row['type'] = 'searchByVenue';
		$filter_venue_row['value'] = $_GET['searchByVenue'];
		$filter_venue_row['label'] = $filter_venue_row['value'];
		$search_filter_values[$filter_index] = $filter_venue_row;

		$filter_date_row = array();
		$filter_date_row['type'] = 'searchBydate';
		$filter_date_row['value'] = $_GET['searchBydate'];
		$timestamp = strtotime($_GET['searchBydate']);
		$filter_date_row['label'] = date('M d, Y', $timestamp);
		$search_filter_values[$filter_index+1] = $filter_date_row;

		$args['meta_query'] = array(
			'relation' => 'AND',
			array(
				array(
					'key'       => 'course_venue',
					'value'     => $_GET['searchByVenue'],
					'compare'   => '=',
				)
				), 
				array(
					array(
						'key'       => 'course_date',
						'value'     => $_GET['searchBydate'],
						'compare'   => '=',
					)
				)
		);   
	}
	
    $loop = new WP_Query($args);
	
	?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
	
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<div class="container"> 
	<div class="course_heading my-5">
		<h1 class="fw-bold">Search Public Courses</h1>
	</div>
	<div class="row search_course my-5">
		<div class="col-md-3 top_search_bar input_box">
			<input class="form-control col-md-10" type="search" name="product_search" id="product_search" placeholder="Search Course" value="<?php echo $serach_course_value; ?>">
			<button class="text-white btn btn-info col-md-2" id="searchsubmit"><i class="fa fa-search" aria-hidden="true"></i></button>
		</div>
		<div class="col-md-2 top_search_bar">
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
		<div class="col-md-2 top_search_bar">
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
		<div class="col-md-2 top_search_bar">
			<select class="form-control" id="sortByAttribute" style="width:100% !important">
					<option value="price_asc" <?php if(isset($_GET['sortByAttribute']) && !empty($_GET['sortByAttribute'])) {  echo $_GET['sortByAttribute']=='price_asc'?'selected':''; } ?>>Sort by Price low to high</option>
					<option value="price_desc" <?php if(isset($_GET['sortByAttribute']) && !empty($_GET['sortByAttribute'])) {  echo $_GET['sortByAttribute']=='price_desc'?'selected':''; } ?>>Sort by Price high to low</option>
					<option value="name_asc" <?php if(isset($_GET['sortByAttribute']) && !empty($_GET['sortByAttribute'])) {  echo $_GET['sortByAttribute']=='name_asc'?'selected':''; } ?>>Sort by Name A - Z</option>
					<option value="name_desc" <?php if(isset($_GET['sortByAttribute']) && !empty($_GET['sortByAttribute'])) {  echo $_GET['sortByAttribute']=='name_desc'?'selected':''; } ?>>Sort by Name Z - A</option>
					<option value="date_asc" <?php if(isset($_GET['sortByAttribute']) && !empty($_GET['sortByAttribute'])) {  echo $_GET['sortByAttribute']=='date_sort'?'selected':''; } ?>>Sort by Date</option>
			</select>
		</div>
		<div class="col-md-3 top_search_bar d-flex input_box">
			<input type="date" name="course_search_date" id="course_date" class="form-control col-md-10" value="<?php echo $serach_course_date_value; ?>">
			<button class="text-white btn btn-info col-md-2" id="date_submit"><i class="fa fa-search" aria-hidden="true"></i></button>
		</div>
			<img class="hidden" id="spinner" src="<?php echo site_url(); ?>/wp-content/uploads/2023/04/Spin-1.3s-281px-1.svg" />

		<?php if(!empty($search_filter_values)) {  ?>
			<div class="col-md-12">
			<div class ="search_filtes_block">
			<?php
				foreach($search_filter_values as $filter) { 
					
						$filter_label = $filter['label'];
					?>
					<span class ="search_filter_value"><?php echo $filter_label; ?><i class="fa fa-times clear_filter" data-type = "<?php echo $filter['type']; ?>" aria-hidden="true"></i></span>
				<?php } ?>
			
				<a class = "clear_search_filter" href="javascript:void(0)">Clear Filter</a>
			</div>
		</div>
		<?php } ?>
		
	</div>
	<div class="row">
		<div class="col-md-12 ">
			<div class ="table-container fixed-tbl-footer table-responsive">
				<table class="table table-striped table-bordered table-container-public-courses"style="font-family:'Myriad Pro Light', Sans-serif;">
					<thead>
						<tr>
							<th class="fw-bold align-middle" scope="col">Image</th>
							<th class="fw-bold align-middle" scope="col" width="15%">Name</th>
							<th class="fw-bold align-middle" scope="col">Description</th>
							<th class="fw-bold align-middle" scope="col">Venue</th>
							<th class="fw-bold align-middle" scope="col" width="10%">Date</th>
							<th class="fw-bold align-middle" scope="col">Time</th>
							<th class="fw-bold align-middle" scope="col" width="8%">Price</th>
							<th class="fw-bold align-middle" scope="col" width="8%">Seat</th>
							<th class="fw-bold align-middle" scope="col">Book Now</th>
						</tr>
					</thead>
					<tbody>
						<?php
						// echo $loop->post_count;
						if($loop->post_count != 0)
						{
						while ( $loop->have_posts() ) : $loop->the_post(); 
						
						$new_course_date = '-';
						$course_date = get_post_meta(get_the_ID(), 'course_date', true);
						if(isset($course_date) && !empty($course_date)) {
							$timestamp = strtotime($course_date);
							$new_course_date = date('M d, Y', $timestamp);
						}

						$new_course_time = '';
						$course_time = get_post_meta( get_the_ID(), 'course_time', true );
						if(isset($course_time) && !empty($course_time)) {
							$timestamp = strtotime($course_time);
							$new_course_time = date('h:i A', $timestamp);
						}
						
						$seat_available_class = "";
						$avilable_seat_count = $product->get_stock_quantity();
						if($avilable_seat_count > 0) {
							$available_seat = $avilable_seat_count > 1 ? "Seats" : "Seat";
							$available_seat = $avilable_seat_count. " " . $available_seat;
							$seat_available_class = "seat_available";
						} else {
							$available_seat = "None";
							$seat_available_class = "seat_unavailable";
						}
						
						?>
								<tr>
									<td><?php if ( has_post_thumbnail() ) {
											the_post_thumbnail("small_thumbnail");
											} else {
											echo '<img src="'.get_site_url().'/wp-content/uploads/2023/03/download-1-1.png" width="50" hieght="50"/>';
									}; ?></td>
									<td width="15%"><a class="public-course-title" href="<?php echo get_the_permalink(); ?>"><?php the_title(); ?></a></td>
									<td> <?php echo get_excerpt(); ?></td>
									<td><?php echo get_post_meta(get_the_ID(), 'course_venue', true); ?></td>
									<td width="10%"><?php echo $new_course_date; ?></td>
									<td><?php echo $new_course_time." <span class='course_duration'>( ".get_post_meta(get_the_ID(), 'course_duration', true)." )"; ?></td>
									<td width="8%"><?php $product = wc_get_product( get_the_ID() ); ?>
										<p class="product-price-del"><?php echo $product->get_price_html(); ?></p></td>
									<td class="course_availablety <?php echo $seat_available_class; ?>" width="8%"><?php echo $available_seat; ?></td>
									<td class="view_detail_btn" ><?php if($avilable_seat_count > 0) { 
												woocommerce_template_loop_add_to_cart();
										} else { ?>
												<a href="<?php echo get_the_permalink(); ?>"><button class="view_detail button wp-element-button product_type_simplet">View Detail</button></a>
											<?php }
									?></td>
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
						'prev_text'    => __('<span class="direction-arrow"> < Previous </span>'),
						'next_text'    => __('<span class="direction-arrow"> Next > </span>'),
					) );
					?>
					</li>
				</ul>
			</nav>
		</div>
	</div>
</div>

<style>
.view_detail_btn{
	text-align:center;
}
td.No_Record {
	text-align:center;
}
.course_heading{
	font-family: 'Myriad Pro';
	font-size: 20px;
}
table, td{
	font-family: "Myriad Pro Light", Sans-serif;
	vertical-align: middle !important;
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
.col-md-3.top_search_bar.input_box {
	display: flex;
}
.top_search_bar{
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
/* book now button Add to cart */
a.button.wp-element-button.product_type_simple.add_to_cart_button.ajax_add_to_cart{
	margin: 10px auto 10px auto;
	width: 90px;
	/* background-color: #00B4E1; */
	border-radius: 5px ;
	border : 1px solid #d1d1d1;
}
	a.added_to_cart.wc-forward {
	display: none;
}

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
	color: white;
	border: 1px solid #fff;
}
ul.pagination li a:hover:not(.active) {
	background-color: #4CAF50;
	color:#fff;
}

div.center {text-align: center;}

a.prev.page-numbers {
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
span.woocommerce-Price-amount.amount {
	color: green;
    margin-left: 5px;
}
.product-price-del del span, .product-price-del del {
	color:red!important;
}
span.public-course-location {
    color: #000;
}
.view_detail{
	white-space: pre;
	border-radius: 5px ;
	border : 1px solid #d1d1d1;
	color: #ffffff;
	background-color: #333333;
    font-size: 14px;
    text-align: center;
    position: relative;
    display: inline-block; 
    margin: auto;
    width: auto;
    padding: 8px 12px;
}
.view_detail:hover{
	white-space: pre;
	color: #ffffff;
    background-color: #2ecc71;
	margin: 25px auto -10px auto;
	border: solid 1px #fff;
    font-size: 14px;
    text-align: center;
    position: relative;
    display: inline-block; 
    margin: auto;
    width: auto;
    padding: 8px 12px;
}
span.course_duration {
	float:left;
}
.seat_unavailable {
	color:#FF0000;
}
.seat_available {
	color:#0FBB56;
}

</style>
<script type="text/javascript" >
	// Search By Venue
    jQuery(document).ready(function($) {
        $(document).on("change", "#searchByVenue" ,function(){
			jQuery('body').css("opacity", "0.5");
			jQuery('#spinner').removeClass('hidden');
            var query_value = jQuery(this).val();
			prepare_redirect_URL('searchByVenue', query_value)
			
        });
		// Clear filter
		$(document).on("click", ".clear_filter" ,function(){
			jQuery('body').css("opacity", "0.5");
			jQuery('#spinner').removeClass('hidden');
            var filter_type = jQuery(this).data("type");
			const url = new URL(window.location);
			url.searchParams.delete(filter_type);
			window.location.href = url;
        });
		// Serach by Category
		$(document).on("change", "#searchByCategory" ,function(){
            var query_value = jQuery(this).val();
			if(query_value != '') {
				jQuery('body').css("opacity", "0.5");
				jQuery('#spinner').removeClass('hidden');
				prepare_redirect_URL('searchByCategory', query_value)
			}
			
        });
		// Search by Course
		$(document).on("click", "#searchsubmit" ,function(){
			jQuery('body').css("opacity", "0.5");
			jQuery('#spinner').removeClass('hidden');
            var query_value = document.getElementById('product_search').value;
			prepare_redirect_URL('searchByCourse', query_value)
        });
		// sort By Categoriy
		$(document).on("change", "#sortByAttribute" ,function(){
			jQuery('body').css("opacity", "0.5");
			jQuery('#spinner').removeClass('hidden');
            var query_value = jQuery(this).val();
			prepare_redirect_URL('sortByAttribute', query_value)
        });
		// Search by Date
		$(document).on("click", "#date_submit" ,function(){
			jQuery('body').css("opacity", "0.5");
			jQuery('#spinner').removeClass('hidden');
			var query_value = jQuery("#course_date").val();
            prepare_redirect_URL('searchBydate', query_value);
		});
		// clear filter
		$(document).on("click", ".clear_search_filter" ,function(){
			jQuery('body').css("opacity", "0.5");
			jQuery('#spinner').removeClass('hidden');
			var clear_filter = jQuery(".search_filter_value").val();
			var current_url = window.location.origin+window.location.pathname;
			window.location.href = current_url;	
		});

		jQuery(document).on("click", ".ajax_add_to_cart" ,function() {
			var title = jQuery(this).closest('tr').find('.public-course-title').text();
			var add_to_cart_message = '"'+title+'" added to cart successfully!';
			toastr["success"](add_to_cart_message)
		});

		toastr.options = {
			"closeButton": true,
			"debug": false,
			"newestOnTop": false,
			"progressBar": false,
			"positionClass": "toast-top-right",
			"preventDuplicates": false,
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "2000",
			"timeOut": "4000",
			"extendedTimeOut": "2000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		}
    });

	// Set filter URL Functon
	function prepare_redirect_URL(filter_type, query_value){
		const cut_url = new URL(window.location.href);
		var is_removed = false;
		if (cut_url.searchParams.has(filter_type)) {
			cut_url.searchParams.delete(filter_type);
			is_removed = true;
		}

		var matches = window.location.href.match(/[a-z\d]+=[a-z\d]+/gi);
		var count = matches? matches.length : 0;

		if(is_removed) {
			count = count-1;
		}
		
		var param_add = count > 0  ? "&" : "?";

		window.location.href = cut_url+param_add+filter_type+'='+query_value;
		
	}

    </script>
    <?php get_footer(); ?>
