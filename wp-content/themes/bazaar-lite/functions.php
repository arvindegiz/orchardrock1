<?php

/**
 *
 * Bazaar Theme Functions
 *
 * This is your standard WordPress
 * functions.php file.
 *
 * @author  Alessandro Vellutini
 *
*/

    require_once get_template_directory() . '/core/main.php';
    


    // Display Fields
add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');
// Save Fields
add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');
function woocommerce_product_custom_fields()
{
    global $woocommerce, $post;
    echo '<div class="product_custom_field">';
    // Custom Product Text Field
    woocommerce_wp_text_input(
        array(
            'id' => 'course_date',
            'label' => __('Course Date', 'woocommerce'),
            'type' => 'date',
        )
    );
    //Custom Product Number Field
    woocommerce_wp_text_input(
        array(
            'id' => 'course_duration',
            'placeholder' => 'Enter course duration',
            'label' => __('Course Duration', 'woocommerce'),
        )
    );
    // Custom Product Text Field for venue
   woocommerce_wp_text_input(
        array(
            'id' => 'course_venue',
            'placeholder' => 'Enter course venue',
            'label' => __('Course Venue', 'woocommerce'),
        )
    );
    
    echo '</div>';
}


function woocommerce_product_custom_fields_save($post_id)
{
    // Custom Product Text Field course date
    $course_date_field = $_POST['course_date'];
    if (!empty($course_date_field))
        update_post_meta($post_id, 'course_date', esc_attr($course_date_field));

    
// Custom Product Number Field Duration
    $course_duration_field = $_POST['course_duration'];
    if (!empty($course_duration_field))
        update_post_meta($post_id, 'course_duration', esc_attr($course_duration_field));

// Custom Product Number Field Venue

    $course_venue_field = $_POST['course_venue'];
    if (!empty($course_venue_field))
        update_post_meta($post_id, 'course_venue', esc_attr($course_venue_field));
}

// filter product

function skyverge_add_postmeta_ordering_args( $sort_args ) {
        
    $orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
    switch( $orderby_value ) {
    
        // Name your sortby key whatever you'd like; must correspond to the $sortby in the next function
        case 'course_venue':
            $sort_args['orderby']  = 'meta_value';
            // Sort by meta_value because we're using alphabetic sorting
            $sort_args['order']    = 'asc';
            $sort_args['meta_key'] = 'course_venue';
            // use the meta key you've set for your custom field, i.e., something like "course_venue" or "_wholesale_price"
            break;
                
        case 'points_awarded':
            $sort_args['orderby'] = 'meta_value_num';
            // We use meta_value_num here because points are a number and we want to sort in numerical order
            $sort_args['order'] = 'desc';
            $sort_args['meta_key'] = 'points';
            break;
        
    }
    
    return $sort_args;
}
add_filter( 'woocommerce_get_catalog_ordering_args', 'skyverge_add_postmeta_ordering_args' );


add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 50, 50, true ); // Normal post thumbnails
add_image_size( 'small_thumbnail', 50, 100, true);

// Add these new sorting arguments to the sortby options on the frontend
function skyverge_add_new_postmeta_orderby( $sortby ) {
    
    // Adjust the text as desired
    $sortby['course_venue'] = __( 'Sort by venue', 'woocommerce' );
    $sortby['points_awarded'] = __( 'Sort by points for purchase', 'woocommerce' );
    
    return $sortby;
}
add_filter( 'woocommerce_default_catalog_orderby_options', 'skyverge_add_new_postmeta_orderby' );
add_filter( 'woocommerce_catalog_orderby', 'skyverge_add_new_postmeta_orderby' );


add_action( 'woocommerce_before_shop_loop', 'woocommerce_product_defualt_search' );


function woocommerce_product_defualt_search(){

    echo '<input type="text" value="" name="product_search" id="product_search" placeholder="Search Venue" /><button type="button" id="search_venue">Search</button>';

    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="date" value="" name="course_date" id="course_date"/><button type="button" id="search_course_date">Search</button>';
}


add_action( 'wp_footer', 'my_ajax_without_file' );

function get_course_venue() {
  

    
}//end nelio_notify_on_post_published()

add_action( 'publish_post', 'get_course_venue', 10, 2 );

function my_ajax_without_file() { ?>

    <script type="text/javascript" >
    jQuery(document).ready(function($) {
        jQuery("#search_venue").click(function(){
            var search_val = jQuery('#product_search').val();
        alert(search_val);
        ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ) ?>'; // get ajaxurl

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
    </script> 
    <?php
}

add_action("wp_ajax_get_filtered_products" , "get_filtered_products");


function get_filtered_products(){

 

  $args = array( 
    'post_type'      => 'product', // product, not products
    'post_status'    => 'publish',
    'meta_key' => 'course_venue',
    'meta_value' => $_POST['search_val'],
    'posts_per_page' => 12 // change this based on your needs
  );
  $ajaxposts = new WP_Query( $args );

  $response = '';

  if ( $ajaxposts->posts ){ 
    while ( $ajaxposts->have_posts() ) { 
      $ajaxposts->the_post(); 
      $response .= wc_get_template_part( 'content', 'product' ); // use WooCommerce function to get html
    } 
  } else { 
    // handle not found by yourself or
    // perhaps do_action( 'woocommerce_no_products_found' ); could do the trick?
  }

  return $response;
  exit;
   
} 



// Add Trim Description 
function get_excerpt(){
    $excerpt = get_the_content();
    $excerpt = preg_replace(" ([.*?])",'',$excerpt);
    $excerpt = strip_shortcodes($excerpt);
    $excerpt = strip_tags($excerpt);
    $excerpt = substr($excerpt, 0, 30);
    $excerpt = substr($excerpt, 0, strripos($excerpt, " "));
    $excerpt = trim(preg_replace( '/\s+/', ' ', $excerpt));
    $excerpt = $excerpt.'... <a href="'.get_the_permalink().'">more</a>';
    return $excerpt;
    }


  
?>