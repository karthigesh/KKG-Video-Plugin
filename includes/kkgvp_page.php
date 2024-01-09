<?php

/*
  Plugin Name: KKG Music Plugin
  Description: This is my first plugin! It used to create a music link!
  Author: Karthigesh
 */
// If this file is called directly, abort.
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class kkgvideo_vid {

    private $vidId = 0;

    public function setVideoId($id = 0) {
        $this->vidId = $id;
    }

    public function getSingleVideo() {
        $videList[] = get_post_meta($this->vidId,'_kkgvideo',true);
        return implode(',',$videList);
    }

    public function getAllVideo() {
        $posts = get_posts(array(
            'post_type'   => 'kkg_videos',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids'
            )
        );
        $videList = [];
        //loop over each post
        foreach($posts as $p){
            //get the meta you need form each post
            $videList[] = get_post_meta($p,'_kkgvideo',true);
            //do whatever you want with it
        }
        return implode(',',$videList);
    }
    
}