<?php
/*
Plugin Name: KKG Videos Plugin
Description: This is my first plugin! It used to create a video link!
Author: Karthigesh
*/
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class kkg_videos_list_Table extends WP_List_Table {

    // Here we will add our code
    // define $table_data property
    private $table_data;

    // Define table columns

    function get_columns()
 {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'video_title'          => __( 'Title', 'kkg_videos' ),
            'sub_videourl'          => __( 'Music URL', 'kkg_videos' )
        );
        return $columns;
    }


    // Adding action links to column
    function column_video_title($item)
    {
        $type = $item[ 'mtype' ];
        $edit = ($type == 1)?'add_video':'up_video';
        $actions = array(
                'edit'      => sprintf('<a href="?page=%s&action=%s&element=%s">' . __('Edit', 'kkg_videos') . '</a>', $edit, 'edit', $item[ 'sub_id' ]),
                'delete'    => sprintf('<a href="?page=%s&action=%s&element=%s">' . __('Delete', 'kkg_videos') . '</a>', $_REQUEST['page'], 'delete', $item[ 'sub_id' ]),
                'view'    => sprintf('<a href="?page=%s&action=%s&element=%s">' . __('View', 'kkg_videos') . '</a>', 'view_video', 'view', $item[ 'sub_id' ]),
        );
        return sprintf('%1$s %2$s', $item['video_title'], $this->row_actions($actions));
    }

    // Bind table with columns, data and all

    function prepare_items()
 {
        //data
        if ( isset($_POST['s']) && isset($_POST['page']) && $_POST['page'] == 'kkg_videos') {
            $this->table_data = $this->get_table_data($_POST['s']);
        } else {
            $this->table_data = $this->get_table_data();
        }

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $primary = 'sub_videourl';
        $this->_column_headers = array( $columns, $hidden, $sortable,$primary );
        /* pagination */
        $per_page = 5;
        $current_page = $this->get_pagenum();
        $total_items = count($this->table_data);

        $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
                'total_items' => $total_items, // total number of items
                'per_page'    => $per_page, // items to show on a page
                'total_pages' => ceil( $total_items / $per_page ) // use ceil to round up
        ));
        $this->items = $this->table_data;
    }

    // Get table data

    private function get_table_data($s="") {
        global $wpdb;

        $table = $wpdb->prefix . 'kkg_video_submissions';
        $query = "SELECT * from {$table}";
        if($s != ""){
            $query .= " where video_title like '%{$s}%'";
        }
        return $wpdb->get_results(
            $query,
            ARRAY_A
        );
    }

    function column_default( $item, $column_name )
 {
        switch ( $column_name ) {
            case 'id':
            case 'sub_videourl':
            default:
            return $item[ $column_name ];
        }
    }

    function column_cb( $item )
 {
        return sprintf(
            '<input type="checkbox" name="element[]" value="%s" />',
            $item[ 'sub_id' ]
        );
    }

    protected function get_sortable_columns()
 {
        $sortable_columns = array(
            'sub_videourl'  => array( 'sub_videourl', false )
        );
        return $sortable_columns;
    }

}