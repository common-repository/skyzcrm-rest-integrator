<?php

//If this file is called directly, abort.
//if ( ! defined( 'WPINC' ) ) {
//	die;
//}

function skyz_db_record_lead($selector, $datatext, $tourl) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'skyz_records';
    $wpdb->insert(
            $table_name, array(
        'time' => current_time('mysql'),
        'selector' => $selector,
        'datatext' => $datatext,
        'url' => $tourl,
            )
    );
    $recid = $wpdb->insert_id;
    return $recid;
}

function skyz_db_update_lead($id,$reqresult) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'skyz_records';
    $wpdb->update(
            $table_name, array(
        'rresult' => $reqresult,
            ), array(
        'id' => $id,
            )
    );
}

function skyz_db_remove_record_table() {
    // drop a skyz_records table
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}skyz_records");
}
