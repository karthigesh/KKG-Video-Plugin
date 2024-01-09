<?php
/*
Plugin Name: KKG Video Plugin
Description:  It used to create a Video link!
Author: Karthigesh
*/
if ( !defined( 'ABSPATH' ) )
exit;
// Exit if accessed directly
if ( !function_exists( 'wp_get_current_user' ) ) {
    include( ABSPATH . 'wp-includes/pluggable.php' );
}
require_once plugin_dir_path( __FILE__ ) . 'kkgvp_page.php';

add_action( 'plugins_loaded', 'kkgvideo_checkPerm' );

function kkgvideo_checkPerm() {
    if ( is_user_logged_in() ) {

        $user = wp_get_current_user();

        $roles = ( array ) $user->roles;
        if ( !in_array( 'administrator', $roles ) ) {
            die;
        }

    }

}

/*
* Add my new menu to the Admin Control Panel
*/
// Hook the 'init' action hook, run the function named 'kkgvideo_postType()'

add_action( 'init', 'kkgvideo_postType' );

function kkgvideo_postType() {
    $supports = array(
        'title', // post title
        'thumbnail', // featured images
        'post-formats', // post formats
        'tags'
    );
    $labels = array(
        'name' => _x( 'KKG video', 'plural' ),
        'singular_name' => _x( 'KKG video', 'singular' ),
        'menu_name' => _x( 'KKG video', 'admin menu' ),
        'name_admin_bar' => _x( 'KKG video', 'admin bar' ),
        'add_new' => _x( 'Add New KKG video', 'add new' ),
        'add_new_item' => __( 'Add New KKG video' ),
        'new_item' => __( 'New KKG video' ),
        'edit_item' => __( 'Edit KKG video' ),
        'view_item' => __( 'View KKG video' ),
        'all_items' => __( 'All KKG video' ),
        'search_items' => __( 'Search KKG video' ),
        'not_found' => __( 'No KKG video found.' ),
    );
    $args = array(
        'supports' => $supports,
        'labels' => $labels,
        'public' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'kkgvideos' ),
        'has_archive' => true,
        'hierarchical' => false,
    );
    register_post_type( 'kkg_videos', $args );
}

function kkgvideo_meta_box_callback( $post ) {
    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'kkgvideo_nonce', 'kkgvideo_nonce' );
    $videomode = get_post_meta( $post->ID, '_kkgvideo_mode', true );
    $choosen_url = $choosen_up = '';
    $choosen_url_display = $choosen_up_display = 'style="display: none;"';
    if ( $videomode == 'url' ) {
        $choosen_url = 'checked';
        $choosen_url_display = '';
    } else if ( $videomode == 'upload' ) {
        $choosen_up = 'checked';
        $choosen_up_display = '';
    }
    $videourl = get_post_meta( $post->ID, '_kkgvideo', true );
    $videofilename = get_post_meta( $post->ID, '_kkgvideo_filename', true );
    include( plugin_dir_path( __FILE__ ) . 'inner/kkgvp_metabox.php' );
}

function kkgvideo_meta_box_shortcode( $post ) {
    // Add a nonce field so we can check for it later.
    $str = '<div class="mt-3 mb-3">Use the shortcode <b>[viewkkgvideo id='.$post->ID.']</b> to display the video in your pages or posts</div>';
    $allow = array( 'b' => array(),'div' => array('class'=>array()));
    echo wp_kses( $str, $allow );
}

function kkgvideo_meta_box() {

    $screens = array( 'kkg_videos' );

    foreach ( $screens as $screen ) {
        add_meta_box(
            'KKG-Video',
            __( 'Video Field', 'kkg-video' ),
            'kkgvideo_meta_box_callback',
            $screen,
            'advanced',
            'high'
        );
        add_meta_box(
            'KKG-Video1',
            __( 'Video ShortCode', 'kkg-video' ),
            'kkgvideo_meta_box_shortcode',
            $screen,
            'advanced',
            'high'
        );
    }
}

add_action( 'add_meta_boxes', 'kkgvideo_meta_box' );

/**
* When the post is saved, saves our custom data.
*
* @param int $post_id
*/

function kkgvideo_save_meta_box_data( $post_id ) {

    // Check if our nonce is set.
    if ( ! isset( $_POST[ 'kkgvideo_nonce' ] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST[ 'kkgvideo_nonce' ], 'kkgvideo_nonce' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST[ 'post_type' ] ) && 'kkg_videos' == $_POST[ 'post_type' ] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

    } else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    /* OK, it's safe for us to save the data now. */

    // Make sure that it is set.
    if ( ! isset( $_POST['kkgvideo_nonce'] ) ) {
        return;
    }
    $selectData = sanitize_text_field( $_POST['kkgvideo_chooseType'] );
    $updateUrl = '';
    update_post_meta( $post_id, '_kkgvideo_mode', $selectData );
    if($selectData == 'url'){  
        // Sanitize user input.      
        $updateUrl = sanitize_text_field( $_POST['kkgvideo_url'] );
    }else{
        $updateUrl = sanitize_text_field( $_POST['kkgvideo_file'] );
        $updateUrlName = sanitize_text_field( $_POST['kkgvideo_filename'] );
        update_post_meta( $post_id, '_kkgvideo_filename', $updateUrlName );
    } 
    // Update the meta field in the database.
    update_post_meta( $post_id, '_kkgvideo', $updateUrl );
}

add_action( 'save_post', 'kkgvideo_save_meta_box_data' );

add_action( 'admin_enqueue_scripts', 'kkgvideo_scripts' );

function kkgvideo_scripts() {
    wp_enqueue_style( 'kkgvp-fa', plugin_dir_url( __FILE__ ) . 'fontawesome/css/all.css' );
    wp_enqueue_script( 'kkgvp-fa', plugin_dir_url( __FILE__ ) . 'fontawesome/js/all.js' );
    wp_enqueue_style( 'kkgvp', plugin_dir_url( __FILE__ ) . 'css/css-file.css' );
    wp_enqueue_style( 'kkgvp-bs', plugin_dir_url( __FILE__ ) . 'bootstrap/css/bootstrap.min.css' );
    wp_enqueue_script( 'kkgvp-bs', plugin_dir_url( __FILE__ ) . 'bootstrap/js/bootstrap.min.js' );
    wp_enqueue_script( 'kkgvp', plugin_dir_url( __FILE__ ) . 'js/kkgvp_scripts.js' );
}


function kkgvideo_getfront($element) {
    $sVideo = new kkgvideo_vid();
    if($element != 0){
        $sVideo->setVideoId( $element );
        return $sVideo->getSingleVideo();
    }else{        
        return $sVideo->getAllVideo();
    }
}

// The shortcode function

function kkgvideo_shortcode($atts) {
    extract(shortcode_atts(array(
        'id' => 0,
     ), $atts));
     return kkgvideo_displayVideo($id);
}
// Register shortcode
add_shortcode( 'viewkkgvideo', 'kkgvideo_shortcode' );

function kkgvideo_displayVideo($id){
    wp_enqueue_style( 'fa-css-file', plugin_dir_url( __FILE__ ) . 'fontawesome/css/all.css' );
    wp_enqueue_style( 'fa-kvcss1-file', plugin_dir_url( __FILE__ ) . 'frontend/css/kkgvp_style.css' );
    wp_enqueue_script( 'fa-kvjs-file', plugin_dir_url( __FILE__ ) . 'fontawesome/js/all.js' );
    wp_enqueue_script( 'fa-kvjs1-file', plugin_dir_url( __FILE__ ) . 'js/kkgvp_video.js', array(), false, true);

    $videoSect = kkgvideo_getfront($id);
    $html = "<div style = 'width: 50px;
    height: 50px;
    '></div>
    <div class = 'video-player'>
    <video width='320' height='240' controls controlsList='nodownload noremoteplayback'>
        <source src='". esc_attr( $videoSect )."' type='video/mp4'>
        Your browser does not support the video tag.
        </video>
    </div>";
  return $html;
}

add_action( 'the_post', 'kkgvideo_wpautop' );
function kkgvideo_wpautop( $post ) {
    if( 'kkg_videos' == $post->post_type ) {
        if( is_main_query() ) {
            add_filter( 'the_content', 'kkgvideo_wpautopcontent' );
        }
    }
    
  }
function kkgvideo_wpautopcontent($content) {
    global $post;
    if( is_singular() ) {
        if( 'kkg_videos' == $post->post_type ) {
            $postId = get_the_ID();
            remove_filter( 'the_content', 'kkgvideo_wpautopcontent' );
            $wpse_261935_meta = kkgvideo_displayVideo($postId);
            return  $content.$wpse_261935_meta;
        }
    }
}
add_action( 'wp_content', 'kkgvideo_wpautop' );

