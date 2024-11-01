<?php

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

$option_names = array('field_server_appid', 'field_server_appsecret', 'field_stage', 'field_server', 'field_server_port', 'option', 'field_sslvalidation', 'field_timeout');
$arrlength = count($option_names);

for ($x = 0; $x < $arrlength; $x++) {
    try {
        $option_name = 'skyz_' . $option_names[$x];
        delete_option($option_name);
        // for site options in Multisite
        delete_site_option($option_name);
    } catch (Exception $e) {
        ;
    }
}

require_once dirname(__DIR__) . '/includes/dbfuncutils.php';
skyz_db_remove_record_table();
