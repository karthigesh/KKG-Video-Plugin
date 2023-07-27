<?php
/*
Plugin Name: KKG Video Plugin
Description: This is my first plugin! It used to create a Video link!
Author: Karthigesh
*/
// If this file is called directly, abort.

use function Composer\Autoload\includeFile;

if ( ! defined( 'WPINC' ) ) {
    die;
}

require_once plugin_dir_path( __FILE__ ) . 'kkg_videos_list.php';
require_once plugin_dir_path( __FILE__ ) . 'kkgvp_videos_page.php';
define( 'KKG_VIDEO_TABLE', 'kkg_video_submissions' );

/*
*Register activation hook
*/
global $kkgvp_db_version;
$kkgvp_db_version = '1.0';
register_activation_hook( __FILE__, 'kkgvp_install' );

function kkgvp_install() {
    global $wpdb;
    global $kkgvp_db_version;

    $table_name = $wpdb->prefix . KKG_VIDEO_TABLE;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
		sub_id bigint(9) NOT NULL AUTO_INCREMENT,
        video_title text NOT NULL,
        sub_videourl longtext NOT NULL,
        mtype ENUM('1','2') NOT NULL,
		created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
		PRIMARY KEY  (sub_id)
	) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );

    add_option( 'kkgvp_db_version', $kkgvp_db_version );
}

/*
* Add my new menu to the Admin Control Panel
*/
// Hook the 'admin_menu' action hook, run the function named 'mfp_Add_My_Admin_Link()'
add_action( 'admin_menu', 'kkgvp_Add_Menu_Link' );
// Add a new top level menu link to the ACP

function kkgvp_Add_Menu_Link()
 {
    add_menu_page(
        esc_html__( 'KKG Videos', 'ct-admin' ),
        esc_html__( 'KKG Videos', 'ct-admin' ),
        'manage_options', // Capability requirement to see the link
        'kkg_videos', // The 'slug' - file to display when clicking the link
        'kkg_videos_page_list',
        'dashicons-video-alt2',
        6
    );
    add_submenu_page(
        'kkg_videos',
        'Add Video', //page title
        'Add Video', //menu title
        'manage_options', //capability,
        'add_video', //menu slug
        'kkg_videos_page_add' //callback function
    );
    add_submenu_page(
        'kkg_videos',
        'Upload Video', //page title
        'Upload Video', //menu title
        'manage_options', //capability,
        'up_video', //menu slug
        'kkg_videos_page_up' //callback function
    );
    add_submenu_page(
        '',
        'View Video', //page title
        'View Video', //menu title
        'manage_options', //capability,
        'view_video', //menu slug
        'kkg_videos_view' //callback function
    );
}

function kkgvp_scripts() {
    wp_enqueue_style( 'vdfa-css-file', plugin_dir_url( __FILE__ ) . 'fontawesome/css/all.css' );
    wp_enqueue_script( 'vdfa-js-file', plugin_dir_url( __FILE__ ) . 'fontawesome/js/all.js' );
    wp_enqueue_style( 'vdcss-file', plugin_dir_url( __FILE__ ) . 'css/css-file.css' );
    wp_enqueue_style( 'vdcss-bootstrap', plugin_dir_url( __FILE__ ) . 'bootstrap/css/bootstrap.min.css' );
    wp_enqueue_script( 'vdjs-bootstrap', plugin_dir_url( __FILE__ ) . 'bootstrap/js/bootstrap.min.js' );
    wp_enqueue_script( 'vdjs-scripts', plugin_dir_url( __FILE__ ) . 'js/video_scripts.js' );
}
add_action( 'admin_enqueue_scripts', 'kkgvp_scripts' );

function kkg_videos_page_list()
 {
    ob_start();
    if ( filter_has_var( INPUT_GET, 'action' ) ) {
        $subId = filter_input( INPUT_GET, 'element', FILTER_SANITIZE_SPECIAL_CHARS );
        $action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS );
        videosAction( $subId, $action );
    }
    listvpHtml();
}

function kkg_videos_page_add()
 {
    ob_start();
    addvpHtml();
}

function rendervp_input( $inputType, $name, $id, $value = '', $required = FALSE )
 {
    $html = '';
    $requiredAttr = ( $required ) ? 'required' : '';
    switch( $inputType ) {
        case 'text':
        $html = '<input type="text" id="' .$id . '" name="' . $name . '" class="form-control" value="' . $value . '" ' . $requiredAttr . '>';
        break;
        case 'url':
        $html = '<input type="url" id="' .$id . '" name="' . $name . '" class="form-control" value="' . $value . '" ' . $requiredAttr . '>';
        break;
        case 'textarea':
        $html = '<textarea name="' . $name . '" id="' .$id . '" class="form-control" ' . $requiredAttr . '></textarea>';
        break;
        case 'select':
        $html = '';
        break;
        default:
        $html = '';
        break;
    }

    return $html;
}

function listvpHtml() {
    echo "<div class='wrap'>
            <h1>Welcome to KKG Video App!</h1>
            <div class='row'><div class='col-md-12'>";
    echo( '<a href="/wp-admin/admin.php?page=add_video" class="page-title-action">Add new</a>' );
    echo( '<a href="/wp-admin/admin.php?page=up_video" class="page-title-action">Upload</a>' );
    echo '</div></div>';
    if ( isset( $_GET[ 'status' ] ) ) {
        echo "<div class='row mt-2'><div class='col-md-12'>";
        switch( $_GET[ 'status' ] ) {
            case 'success':
            echo  "<div class='alert alert-success' role='alert'>
                        The Video Url has been saved successfully!
                    </div>";
            break;
            case 'failure':
            echo  "<div class='alert alert-warning' role='alert'>
                        There has been issue on saving the Video Url! try again!".$GLOBALS[ 'uploadError' ]." 
                    </div>";

            break;
            case 'delete':
            echo  "<div class='alert alert-danger' role='alert'>
                        The Video Url has been deleted successfully!
                    </div>";
            break;
            case 'deletefail':
            echo  "<div class='alert alert-warning' role='alert'>
                        The Video Url has not been deleted! try again!
                    </div>";

            break;
        }
        echo '</div></div>';
    }
    $myListTable = new kkg_videos_list_Table();
    $myListTable->prepare_items();
    echo '<form method="post">
            <input type="hidden" name="page" value="kkg_videos" />';
    $myListTable->search_box('Search Videos', 'search');
    echo '</form>';
    $myListTable->display();
    echo  '</div><!--wrap-->';
}

function addvpHtml() {
    if ( $_POST ) {
        kkg_videos_save();
    } else {
        print_r($_GET);exit;
        if ( isset( $_GET[ 'element' ] ) ) {
            $data = getkkgvideos( filter_input( INPUT_GET, 'element' ) );
            $mtype = $data[ 'mtype' ];
            if ( $mtype == 1 ) {
                $html = kkg_videos_form();
            } else {
                $html = kkg_videos_up();
            }
        } else {
            $html = kkg_videos_form();
        }
        echo $html;
    }

}

function kkg_videos_form() {
    $videoUrl = $videoTitle = '';
    $title = __( 'kindly Enter the Video Streaming URL', 'kkg_music' );
    if ( filter_has_var( INPUT_GET, 'action' ) ) {
        $title = __( 'kindly Update the Video Streaming URL', 'kkg_music' );
        $data = getkkgvideos( filter_input( INPUT_GET, 'element' ) );
        $videoUrl = ( isset( $data[ 'sub_videourl' ] ) )?$data[ 'sub_videourl' ]:'';
        $videoTitle = ( isset( $data[ 'video_title' ] ) )?$data[ 'video_title' ]:'';
    }
    $GLOBALS[ 'formvideoTitle' ] = $title;
    $GLOBALS[ 'formvideoUrl' ] = rendervp_input( 'url', 'videoUrl', 'videoUrl', $videoUrl, TRUE );
    $GLOBALS[ 'formvideoContent' ] = rendervp_input( 'text', 'videoTitle', 'videoTitle', $videoTitle, TRUE );
    $html = includeFile( plugin_dir_path( __FILE__ ) . 'inner/kkg_videos_url.php' );
    return $html;
}

function kkg_videos_save() {
    global $wpdb;
    $POST = array_map( 'stripslashes_deep', $_POST );
    if ( isset( $POST[ 'action' ] ) && $POST[ 'action' ] == 'kkg_videos_save' ) {
        $url = $POST[ 'videoUrl' ];
        $title = $POST[ 'videoTitle' ];
        if ( !filter_var( $url, FILTER_VALIDATE_URL ) === false ) {
            if ( filter_has_var( INPUT_GET, 'action' ) ) {
                $musicId = filter_input( INPUT_GET, 'element' );
                $wpdb->update(
                    $wpdb->base_prefix.KKG_VIDEO_TABLE,
                    array( 'sub_videourl' => $url, 'mtype'=>'1', 'video_title'=>$title ),
                    array( 'sub_id' => $musicId )
                );
            } else {
                $wpdb->insert(
                    $wpdb->base_prefix.KKG_VIDEO_TABLE,
                    array( 'sub_videourl' => $url, 'mtype'=>'1', 'video_title'=>$title ),
                    array( '%s' ),
                );
            }
            wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_videos&status=success' ) );
            die;
        } else {
            wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_videos&status=failure' ) );
            die;
        }
    } else {
        kkg_videos_upload();
    }

}

function getkkgvideos( $id = 0 ) {
    if ( $id != 0 ) {
        $sVideo = new kkgvp_video();
        $sVideo->setVideoId( $id );
        return $sVideo->getSingleVideo();
    } else {
        wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_videos&staus=failure' ) );
        die;
    }

}

function videosAction( $id = 0, $action = '' ) {
    global $wpdb;
    if ( $id != 0 && $action != '' ) {
        if ( $action == 'delete' ) {
            $sVideo = new kkgvp_video();
            $sVideo->setVideoId( $id );
            $deleted = $sVideo->deleteVideo();
            if ( $deleted ) {
                wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_videos&status=delete' ) );
                die;
            } else {
                wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_videos&status=failure' ) );
                die;
            }

        } else {
            wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_videos&status=failure' ) );
            die;
        }
    } else {
        wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_videos&status=failure' ) );
        die;
    }
}

function kkg_videos_page_up()
 {
    ob_start();
    upvpHtml();
}

function kkg_videos_up() {
    $videoUrl = $videoTitle = '';
    if ( filter_has_var( INPUT_GET, 'action' ) ) {
        $title = __( 'kindly Update the Video Streaming URL', 'kkg_music' );
        $data = getkkgvideos( filter_input( INPUT_GET, 'element' ) );
        $videoUrl = ( isset( $data[ 'sub_videourl' ] ) )?$data[ 'sub_videourl' ]:'';
        $videoTitle = ( isset( $data[ 'video_title' ] ) )?$data[ 'video_title' ]:'';
    }
    $GLOBALS[ 'formvideoContent' ] = rendervp_input( 'text', 'videoTitle', 'videoTitle', $videoTitle, TRUE );
    $GLOBALS[ 'formvideoUrl' ] = $videoUrl;
    $html = includeFile( plugin_dir_path( __FILE__ ) . 'inner/kkg_videos_upload.php' );
    return $html;
}

function kkg_videos_upload() {
    global $wpdb;
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    $POST = array_map( 'stripslashes_deep', $_POST );
    $action = $POST[ 'action' ];
    if ( $action == 'kkg_videos_upload' ) {
        if ( isset( $_FILES[ 'choosevideoFile' ] ) && $_FILES[ 'choosevideoFile' ][ 'name' ] != '' ) {
            $uploadedfile = $_FILES[ 'choosevideoFile' ];

            $upload_overrides = array(
                'test_form' => false
            );

            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

            if ( $movefile && !isset( $movefile[ 'error' ] ) ) {
                $url = $movefile[ 'url' ];
                $videoTitle = $POST[ 'videoTitle' ];
                if ( filter_input( INPUT_GET, 'element' ) ) {
                    $videosId = filter_input( INPUT_GET, 'element' );
                    $wpdb->update(
                        $wpdb->base_prefix.KKG_VIDEO_TABLE,
                        array( 'video_title' => $videoTitle, 'sub_videourl' => $url, 'mtype'=>'2' ),
                        array( 'sub_id' => $videosId )
                    );
                } else {
                    $wpdb->insert(
                        $wpdb->base_prefix.KKG_VIDEO_TABLE,
                        array( 'video_title' => $videoTitle, 'sub_videourl' => $url, 'mtype'=>'2' ),
                        array( '%s' ),
                    );
                }
                wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_videos&status=success' ) );
                die;

            } else {
                /**
                * Error generated by _wp_handle_upload()
                * @see _wp_handle_upload() in wp-admin/includes/file.php
                */
                $GLOBALS[ 'uploadError' ] = $movefile[ 'error' ];
                wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_videos&status=failure' ) );
                die;
            }
        } else if ( filter_input( INPUT_GET, 'element' ) ) {
            $videoTitle = $POST[ 'videoTitle' ];
            $videosId = filter_input( INPUT_GET, 'element' );
            $wpdb->update(
                $wpdb->base_prefix.KKG_VIDEO_TABLE,
                array( 'video_title' => $videoTitle, 'mtype'=>'2' ),
                array( 'sub_id' => $videosId )
            );
        }
        wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_videos&status=success' ) );
        die;

    }
    wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_videos&status=save' ) );
    die;
}

function upvpHtml() {
    if ( $_POST ) {
        kkg_videos_upload();
    } else {
        kkg_videos_up();
    }
}

function kkg_videos_view() {
    $action = $_GET;
    if ( isset( $action[ 'page' ] ) && $action[ 'page' ] == 'view_video' ) {
        if ( isset( $action[ 'action' ] ) && $action[ 'action' ] == 'view' ) {
            $musicContent = getkkgvideos( $action[ 'element' ] );
            if ( $musicContent ) {
                $videoUrl = $musicContent[ 'sub_videourl' ];
                $GLOBALS[ 'viewVideoTitle' ] = $musicContent[ 'video_title' ];
                $GLOBALS[ 'viewVideoContent' ] = $videoUrl;
                $end = array_slice(explode('/', $videoUrl), -1)[0];
                $ext = array_slice(explode('.', $end), -1)[0];
                $GLOBALS[ 'viewVideoExt' ] ='video/'.$ext;
                $html = include( plugin_dir_path( __FILE__ ) . 'inner/kkg_videos_view.php' );
            } else {
                wp_redirect( site_url( '/wp-admin/admin.php?page=kkg_videos' ) );
                die;

            }
        }
    }
}