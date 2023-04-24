<?php 
/* Template name: Public Courses Page */

	get_header(); ?>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
 
	<!-- link bootstrap 5 -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">



 <!-- <div class="container">
 	<input type="text" value="" name="product_search" id="product_search" placeholder="Search Venue" /><button type="button" id="search_venue">Search</button>
 </div>
	 -->



<div class="container">
	<div>
		<h1 class="course_heading">Search Public Courses</h1>
	</div>
	<div class="row searching_public_course">
		<div class="col-md-2 course_srarch">
			<input type="text" value="" name="product_search" id="product_search" placeholder="Search..." />
			<button class="btn btn-info"><i class="fa fa-search" aria-hidden="true"></i></button>
		</div>
		<div class="col-md-2 custom-control">
			<select class="form-control">
					<option value="volvo">Select</option>
					<option value="volvo">Mohali</option>
					<option value="saab">Ambala</option>
					<option value="mercedes">Punjab</option>
					<option value="audi">Chandigarh</option>
			</select>
		</div>
		<div class="col-md-3 search_course_date">
			<input type="date" id="" name="" class="form-control custom_date_piker">
			<button class="btn btn-info"><i class="fa fa-search" aria-hidden="true"></i></button>
		</div>
		<div class="col-md-2">
			<select class="form-control">
					<option value="volvo">Sort by Popularity</option>
					<option value="volvo">Sort by Rating</option>
					<option value="saab">Sort by Price low to high</option>
					<option value="mercedes">Sort by Price high to low</option>
					<option value="audi">Sort by Name A - Z</option>
					<option value="audi">Sort by Name Z - A</option>
					<option value="audi">Sort by Date</option>
			</select>
		</div>

	</div>
	<div class="row">
		<div class="col-md-12 ">
			<div class ="table-container fixed-tbl-footer table-responsive">
				<table class="table table-striped table-bordered"style="font-family:'Myriad Pro Light', Sans-serif;">
					<thead>
						<tr>
							<th class="fw-bold" scope="col">Image</th>
							<th class="fw-bold" scope="col">Name</th>
							<th class="fw-bold" scope="col">Description</th>
							<th class="fw-bold" scope="col">Venue</th>
							<th class="fw-bold" scope="col">Course Date</th>
							<th class="fw-bold" scope="col">Course Duration</th>
							<th class="fw-bold" scope="col">Price</th>
							<th class="fw-bold" scope="col">BooK Now</th>
						</tr>
					</thead>
					<tbody>
					<tr>
						<th scope="row"></th>
						<td>MCA and DoLS</td>
						<td>This course is aimed at people caring for individuals who… </td>
						<td>Mohali</td>
						<td>21 April,2023</td>
						<td>3 Hrs</td>
						<td>$ 1.0</td>
						<td><button type="button" class="btn btn-info">BOOK</button></td>
					</tr>
					<tr>
						<th scope="row"></th>
						<td>MCA and DoLS</td>
						<td>This course is aimed at people caring for individuals who… </td>
						<td>Mohali</td>
						<td>21 April,2023</td>
						<td>3 Hrs</td>
						<td>$ 1.0</td>
						<td><button type="button" class="btn btn-info">BOOK</button></td>
					</tr>
						<tr>
						<th scope="row"></th>
						<td>MCA and DoLS</td>
						<td>This course is aimed at people caring for individuals who… </td>
						<td>Mohali</td>
						<td>21 April,2023</td>
						<td>3 Hrs</td>
						<td>$ 1.0</td>
						<td><button type="button" class="btn btn-info">BOOK</button></td>
					</tr>
					</tbody>
				</table>
			</div>	
		</div>
	</div>
</div>

<style>
	.searching_public_course{
		margin-bottom: 2rem;
	}
	.course_heading{
		font-weight: bold;
		text-decoration: none;
		font-family: 'Myriad Pro';
		font-size: 20px;
	}
	.course_srarch{
		display: flex;
		border: none;
		margin-top: 8px;
		margin-right: 16px;
		font-size: 17px;
	}
	#product_search{
		/* border-radius: 1px 1px 1px 0px; */
		/* width:70%; */
	}
	.custom_date_piker{
		width:70%;
	}
	.search_course_date{
		display: flex;
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