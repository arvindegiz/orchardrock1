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
    // Custom Product Text Field for venue
   woocommerce_wp_text_input(
    array(
        'id' => 'course_location',
        'placeholder' => 'Enter course location',
        'label' => __('Course Location', 'woocommerce'),
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

// Custom Product Number Field Location

    $course_location_field = $_POST['course_location'];
    if (!empty($course_location_field))
        update_post_meta($post_id, 'course_location', esc_attr($course_location_field));

}


add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 50, 50, true ); // Normal post thumbnails
add_image_size( 'small_thumbnail', 50, 100, true);

add_action( 'woocommerce_before_shop_loop', 'woocommerce_product_defualt_search' );


function woocommerce_product_defualt_search(){

    echo '<input type="text" value="" name="product_search" id="product_search" placeholder="Search Venue" /><button type="button" id="search_venue">Search</button>';

    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="date" value="" name="course_date" id="course_date"/><button type="button" id="search_course_date">Search</button>';
}

function add_css() {
    // wp_enqueue_style( 'parent-style', get_stylesheet_directory_uri().'/style.css' );
    wp_enqueue_style( 'style', get_stylesheet_directory_uri().'/assets/css/custom.css' );
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
    $excerpt = $excerpt.'...';
    return $excerpt;
}


// Change add to cart text on single product page
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_add_to_cart_button_text_single' ); 
function woocommerce_add_to_cart_button_text_single() {
    return __( 'Book Now', 'woocommerce' ); 
}

// Change add to cart text on product archives page
add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_add_to_cart_button_text_archives' );  
function woocommerce_add_to_cart_button_text_archives() {
    return __( 'Book Now', 'woocommerce' );
}

function wc_empty_cart_redirect_url() {
	return get_site_url().'/public-courses/';
}
add_filter( 'woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url' );

add_filter('woocommerce_return_to_shop_text', 'prefix_store_button');
function prefix_store_button() {
        $store_button = "Return to Public Courses"; // Change text as required
        return $store_button;
}

// Update custom filed dat on CART and CHECKOUT page
add_action('wp_ajax_wdm_add_user_custom_data_options', 'wdm_add_user_custom_data_options_callback');
add_action('wp_ajax_nopriv_wdm_add_user_custom_data_options', 'wdm_add_user_custom_data_options_callback');

// Add the field to the product

// Store custom field
function save_my_custom_checkout_field( $cart_item_data, $product_id ) {
    
        $course_venue = get_post_meta( $product_id, 'course_venue', true );
        $course_duration = get_post_meta( $product_id, 'course_duration', true );
        $course_date = get_post_meta( $product_id, 'course_date', true );
        $course_location = get_post_meta( $product_id, 'course_location', true );
        
        if(isset($course_venue) && !empty($course_venue)) {
            $cart_item_data[ 'course_venue' ] = $course_venue;
        }

        if(isset($course_date) && !empty($course_date)) {
            $course_date = strtotime($course_date);
            $cart_item_data[ 'course_date' ] = date('M d, Y', $course_date);
        }

        if(isset($course_duration) && !empty($course_duration)) {
            $cart_item_data[ 'course_duration' ] = $course_duration;
        }
        if(isset($course_location) && !empty($course_location)) {
            $cart_item_data[ 'course_location' ] = $course_location;
        }
        
        $cart_item_data['unique_key'] = md5( microtime().rand() );

    return $cart_item_data;
}
add_action( 'woocommerce_add_cart_item_data', 'save_my_custom_checkout_field', 10, 2 );

// Render meta on cart and checkout
function render_meta_on_cart_and_checkout( $cart_data, $cart_item = null ) {
    $custom_items = array();
    /* Woo 2.4.2 updates */
    if( !empty( $cart_data ) ) {
        $custom_items = $cart_data;
    }
    if( isset( $cart_item['course_venue'] ) ) {
        $custom_items[] = array( "name" => 'Venue', "value" => $cart_item['course_venue'] );
    }
    if( isset( $cart_item['course_location'] ) ) {
        $custom_items[] = array( "name" => 'Location', "value" => $cart_item['course_location'] );
    }
    if( isset( $cart_item['course_date'] ) ) {
        $custom_items[] = array( "name" => 'Date', "value" => $cart_item['course_date'] );
    }
    if( isset( $cart_item['course_duration'] ) ) {
        $custom_items[] = array( "name" => 'Duration', "value" => $cart_item['course_duration'] );
    }
    return $custom_items;
}
add_filter( 'woocommerce_get_item_data', 'render_meta_on_cart_and_checkout', 10, 2 );

// Display as order meta
function my_field_order_meta_handler( $item_id, $values, $cart_item_key ) {
    if( isset( $values['course_venue'] ) ) {
        wc_add_order_item_meta( $item_id, "Venue", $values['course_venue'] );
    }
    if( isset( $values['course_location'] ) ) {
        wc_add_order_item_meta( $item_id, "Location", $values['course_location'] );
    }
    if( isset( $values['course_date'] ) ) {
        wc_add_order_item_meta( $item_id, "Date", $values['course_date'] );
    }
    if( isset( $values['course_duration'] ) ) {
        wc_add_order_item_meta( $item_id, "Duration", $values['course_duration'] );
    }
}
add_action( 'woocommerce_add_order_item_meta', 'my_field_order_meta_handler', 1, 3 );


// Update the user meta with field value
add_action('woocommerce_checkout_update_user_meta', 'my_custom_checkout_field_update_user_meta');
function my_custom_checkout_field_update_user_meta( $user_id ) {
    if ($user_id && $_POST['course_venue']) update_user_meta( $user_id, 'course_venue', esc_attr($_POST['course_venue']) );
    if ($user_id && $_POST['course_date']) update_user_meta( $user_id, 'course_date', esc_attr($_POST['course_date']) );
    if ($user_id && $_POST['course_duration']) update_user_meta( $user_id, 'course_duration', esc_attr($_POST['course_duration']) );
    if ($user_id && $_POST['course_location']) update_user_meta( $user_id, 'course_location', esc_attr($_POST['course_location']) );

}

// Update the order meta with field value

add_action('woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta');
function my_custom_checkout_field_update_order_meta( $order_id ) {
    if ($_POST['course_venue']) update_post_meta( $order_id, 'Venue', esc_attr($_POST['course_venue']));
    if ($_POST['course_date']) update_post_meta( $order_id, 'Date', esc_attr($_POST['course_date']));
    if ($_POST['course_duration']) update_post_meta( $order_id, 'Duration', esc_attr($_POST['course_duration']));
    if ($_POST['course_location']) update_post_meta( $order_id, 'Duration', esc_attr($_POST['course_location']));

}

add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );
function my_custom_checkout_field_display_admin_order_meta( $order ){
    $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
    
    $course_venue = get_post_meta( $order_id, 'course_venue', true );
    $course_date = get_post_meta( $order_id, 'course_date', true );
    $course_duration = get_post_meta( $order_id, 'course_duration', true );
    $course_location = get_post_meta( $order_id, 'course_location', true );

    if(isset($course_venue) && !empty($course_venue)) {
        echo '<p><strong>'.__('Venue').':</strong> ' . $course_venue . '</p>';
    }
    if(isset($course_duration) && !empty($course_duration)) {
        echo '<p><strong>'.__('Location').':</strong> ' . $course_location . '</p>';
    }

    if(isset($course_date) && !empty($course_date)) {
        echo '<p><strong>'.__('Date').':</strong> ' . $course_date . '</p>';
    }

    if(isset($course_duration) && !empty($course_duration)) {
        echo '<p><strong>'.__('Duration').':</strong> ' . $course_duration . '</p>';
    }
    
}
  //add custom WooCommerce checkout spinner button

add_action('wp_head', 'custom_woocommerce_checkout_spinner_blogies', 1000 );
function custom_woocommerce_checkout_spinner_blogies() {
    ?>
    <style>
    .woocommerce .blockUI.blockOverlay:before,
    .woocommerce .loader:before {
        height: 7em;
        width: 7em;
        position: fixed;
        left:0;
        right:0;
        margin:auto;
        display: block;
        -webkit-animation: none;
        -moz-animation: none;
        animation: none;
        background-image:url('http://localhost/orchardrock1/wp-content/uploads/2023/04/Spin-1.3s-281px-1.svg') !important;
    }
    
    </style>
    <?php
}
?>