<?php

/**
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://skyz.co.il
 * @since             1.0.0
 * @package           skyz
 *
 * @wordpress-plugin
 * Plugin Name:       SkyzCrm Rest integrator
 * Plugin URI:        https://skyz.co.il/plugins/skyz/
 * Description:       Contact7 to SkyzCRM integration plugin.
 * Version:           1.0.3
 * Author:            Impact Software 1996 L.T.D.
 * Author URI:        https://skyz.co.il
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       skyz
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function skyz_install() {
    skyz_init();
}

register_activation_hook(__FILE__, 'skyz_install');

function skyz_deactivation() {
    skyz_uninit();
}

register_deactivation_hook(__FILE__, 'skyz_deactivation');

require_once plugin_dir_path(__FILE__) . 'includes/restapi.php';
require_once plugin_dir_path(__FILE__) . 'includes/dbfuncutils.php';

add_action('wpcf7_before_send_mail', 'skyz_sendrestform_drv', 10, 1);

global $skyz_db_version;
$skyz_db_version = "1.0";

function skyz_init() {
    global $wpdb;
    global $skyz_db_version;
    $table_name = $wpdb->prefix . "skyz_records";
    $charset_collate = $wpdb->get_charset_collate();
    
    $dbver = get_option('skyz_dbver');
    
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name) {
        $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        selector VARCHAR(20) DEFAULT '' NOT NULL,
        datatext text NOT NULL,
        url varchar(200) DEFAULT '' NOT NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        // save current database version for later use (on upgrade)
        add_option("skyz_dbver", $skyz_db_version);
    }
    
	if ($dbver!=$skyz_db_version){
		$sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        selector VARCHAR(20) DEFAULT '' NOT NULL,
        datatext text NOT NULL,
        url varchar(200) DEFAULT '' NOT NULL,
        rresult text NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        update_option("skyz_dbver", $skyz_db_version);
	}
}

function skyz_uninit() {
    remove_action('wpcf7_before_send_mail', 'skyz_sendrestform_drv');
}

if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'admin/index.php';
}
/**
 * register our skyz_settings_init to the admin_init action hook
 */
add_action('admin_init', 'skyz_settings_init');
/**
 * register our skyz_options_page to the admin_menu action hook
 */
add_action('admin_menu', 'skyz_options_page');

add_action('admin_head', 'skyz_admin_style');

