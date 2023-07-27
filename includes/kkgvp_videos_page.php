<?php
/*
Plugin Name: KKG Video Plugin
Description: This is my first plugin! It used to create a Video link!
Author: Karthigesh
*/
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class kkgvp_video {

    private $videoId = 0;

    public function setVideoId( $id = 0 ) {
        $this->videoId = $id;
    }

    public function getSingleVideo() {
        global $wpdb;
        return $wpdb->get_row(
            $wpdb->prepare(
                'SELECT * FROM '.$wpdb->base_prefix.KKG_VIDEO_TABLE." WHERE sub_id = $this->videoId"
            ),
            ARRAY_A );
        }

    public function deleteVideo() {
        global $wpdb;
        return $wpdb->query(
          $wpdb->prepare(
            'DELETE FROM '.$wpdb->base_prefix.KKG_VIDEO_TABLE.' WHERE sub_id = %d;',
            array( $this->videoId )
          )
        );
    }
}