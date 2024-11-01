<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

require_once dirname(__DIR__) . '/includes/restapi.php';
require_once dirname(__DIR__) . '/includes/dbfuncutils.php';


/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */

/**
 * custom option and settings
 */
function skyz_settings_init() {
    // register a new setting for "skyz" page
    register_setting('skyz', 'skyz_field_stage');

    // register a new section in the "skyz" page
    add_settings_section(
            'skyz_section_developers', __('Contact7 forms to SkyzCRM integration.', 'skyz'), 'skyz_section_developers_cb', 'skyz'
    );


    // register a new field in the "skyz_section_developers" section, inside the "skyz" page
    add_settings_field(
            'skyz_field_stage', // as of WP 4.6 this value is used only internally
            // use $args' label_for to populate the id inside the callback
            __('On Stage', 'skyz'), 'skyz_field_stage_cb', 'skyz', 'skyz_section_developers', [
        'label_for' => 'skyz_field_stage',
        'class' => 'skyz_row',
        'skyz_custom_data' => 'custom',
            ]
    );

    register_setting('skyz', 'skyz_field_sslvalidation');
    // register a new field in the "skyz_section_developers" section, inside the "skyz" page
    add_settings_field(
            'skyz_field_sslvalidation', // as of WP 4.6 this value is used only internally
            // use $args' label_for to populate the id inside the callback
            __('SSL validation', 'skyz'), 'skyz_field_sslvalidate_cb', 'skyz', 'skyz_section_developers', [
        'label_for' => 'skyz_field_sslvalidation',
        'class' => 'skyz_row',
        'skyz_custom_data' => 'custom',
            ]
    );

    register_setting('skyz', 'skyz_field_server');
    // register a new field in the "skyz_section_developers" section, inside the "skyz" page
    add_settings_field(
            'skyz_field_server', // as of WP 4.6 this value is used only internally
            // use $args' label_for to populate the id inside the callback
            __('Server', 'skyz'), 'skyz_field_server_text0', 'skyz', 'skyz_section_developers', [
        'label_for' => 'skyz_field_server',
        'class' => 'skyz_row',
        'skyz_custom_data' => 'custom',
        'type' => 'text',
        'options' => false,
            ]
    );

    register_setting('skyz', 'skyz_field_server_port');
    // register a new field in the "skyz_section_developers" section, inside the "skyz" page
    add_settings_field(
            'skyz_field_server_port', // as of WP 4.6 this value is used only internally
            // use $args' label_for to populate the id inside the callback
            __('Port', 'skyz'), 'skyz_field_server_text1', 'skyz', 'skyz_section_developers', [
        'label_for' => 'skyz_field_server_port',
        'class' => 'skyz_row',
        'skyz_custom_data' => 'custom',
            ]
    );


    register_setting('skyz', 'skyz_field_timeout');
    // register a new field in the "skyz_section_developers" section, inside the "skyz" page
    add_settings_field(
            'skyz_field_timeout', // as of WP 4.6 this value is used only internally
            // use $args' label_for to populate the id inside the callback
            __('Timeout', 'skyz'), 'skyz_field_server_text2', 'skyz', 'skyz_section_developers', [
        'label_for' => 'skyz_field_timeout',
        'class' => 'skyz_row',
        'skyz_custom_data' => 'custom',
            ]
    );


    register_setting('skyz', 'skyz_field_server_appid');
    // register a new field in the "skyz_section_developers" section, inside the "skyz" page
    add_settings_field(
            'skyz_field_server_appid', // as of WP 4.6 this value is used only internally
            // use $args' label_for to populate the id inside the callback
            __('App ID', 'skyz'), 'skyz_field_server_text', 'skyz', 'skyz_section_developers', [
        'label_for' => 'skyz_field_server_appid',
        'class' => 'skyz_row',
        'skyz_custom_data' => 'custom',
            ]
    );

    register_setting('skyz', 'skyz_field_server_appsecret');
    // register a new field in the "skyz_section_developers" section, inside the "skyz" page
    add_settings_field(
            'skyz_field_server_appsecret', // as of WP 4.6 this value is used only internally
            // use $args' label_for to populate the id inside the callback
            __('App Secret', 'skyz'), 'skyz_field_server_textpass', 'skyz', 'skyz_section_developers', [
        'label_for' => 'skyz_field_server_appsecret',
        'class' => 'skyz_row',
        'skyz_custom_data' => 'custom',
            ]
    );
}

/**
 * Check if contact form 7 is active
 * @return [type] [description]
 */
function verify_cf7_existance() {
    if (!is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
        return false;
    } else {
        return true;
    }
}

/**
 * Check if contact form 7 skyz hook is active
 * @return [type] [description]
 */
function verify_skyzhook_existance() {

    if (!has_action('wpcf7_before_send_mail', 'skyz_sendrestform_drv')) {
        return false;
    } else {
        return true;
    }
}

/**
 * custom option and settings:
 * callback functions
 */
// developers section cb
// section callbacks can accept an $args parameter, which is an array.
// $args have the following keys defined: title, id, callback.
// the values are defined at the add_settings_section() function.


function skyz_section_developers_cb($args) {
    ?>
    <p id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e('Activation status for Send lead data on wpcf7_before_send_mail event', 'skyz'); ?></p>
    <?php
    $s = '';
    $q = 'is NOT';
    if (verify_cf7_existance()) {
        $q = '';
    }
    $s = $s . "<div><font color=\"green\">Contact7</font> is required and<span style=\"color:" . ($q === '' ? 'blue' : 'red') . "\"> " . $q . " installed</span> on this server " . ($q === '' ? 'OK :)' : '!!!') . "</div>";
    $q = 'is NOT';
    if (verify_skyzhook_existance()) {
        $q = '';
    }

    $s = $s . "<div><font color=\"green\">skyz_sendrestform_drv hook</font> is required and<span style=\"color:" . ($q === '' ? 'blue' : 'red') . "\"> " . $q . " installed</span> on this server " . ($q === '' ? 'OK :)' : '!!!') . "</div>";
    echo $s;
}

// stage field cb
// field callbacks can accept an $args parameter, which is an array.
// $args is defined at the add_settings_field() function.
// wordpress has magic interaction with the following keys: label_for, class.
// the "label_for" key value is used for the "for" attribute of the <label>.
// the "class" key value is used for the "class" attribute of the <tr> containing the field.
// you can add custom key value pairs to be used inside your callbacks.
function skyz_field_stage_cb($args) {
    // get the value of the setting we've registered with register_setting()
    $option = get_option('skyz_field_stage');
    if (!$option) { // If no value exists
        $option = 'type-selector'; // Set to our default
    }
    ?>
    <input type="text" 
           id="<?php echo esc_attr($args['label_for']); ?>" 
           name="<?php echo esc_attr($args['label_for']); ?>"
           value="<?php echo esc_attr('' . $option . '') ?>" />

    <p class="description">
        <?php esc_html_e(' This field defines in form to use as type selector.For each c7 form assign one of [lead,tiket]', 'skyz'); ?>
    </p>
    <?php
    register_setting('skyz', $args['label_for']);
}

function skyz_field_sslvalidate_cb($args) {
    // get the value of the setting we've registered with register_setting()
    $options = get_option('skyz_field_sslvalidation');
    if (!$options) { // If no value exists
        $options = 'false'; // Set to our default
    }
    // output the field
    ?>
    <select id="<?php echo esc_attr($args['label_for']); ?>"
            data-custom="<?php echo esc_attr($args['skyz_custom_data']); ?>"
            name="<?php echo esc_attr($args['label_for']); ?>"
            >
        <option value="true" <?php selected($options, 'true'); ?>>
            <?php esc_html_e('true', 'skyz'); ?>
        </option>
        <option value="false" <?php selected($options, 'false'); ?>>
            <?php esc_html_e('false', 'skyz'); ?>
        </option>
    </select>
    <p class="description">
        <?php esc_html_e('Depend on remote server certificate type. Use false if there is trusted but no valid or self signed .', 'skyz'); ?>
    </p>
    <?php
    register_setting('skyz', $args['label_for']);
}

function skyz_field_server_text0($args) {  // Textbox Callback
    $option = get_option('skyz_field_server');
    if (!$option) { // If no value exists
        $option = 'crm-erp.co.il'; // Set to our default
    }
    ?>
    <input type="text" 
           id="<?php echo esc_attr($args['label_for']); ?>" 
           name="<?php echo esc_attr($args['label_for']); ?>"
           value="<?php echo esc_attr('' . $option . '') ?>" />
           <?php
           register_setting('skyz', $args['label_for']);
       }

       function skyz_field_server_text1($args) {  // Textbox Callback
           $option = get_option($args['label_for']);
           if (!$option) { // If no value exists
               $option = '443'; // Set to our default
           }
           ?>
    <input type="text" 
           id="<?php echo esc_attr($args['label_for']); ?>" 
           name="<?php echo esc_attr($args['label_for']); ?>"
           value="<?php echo esc_attr('' . $option . '') ?>" />
           <?php
           register_setting('skyz', $args['label_for']);
       }

       function skyz_field_server_text2($args) {  // Textbox Callback
           $option = get_option($args['label_for']);
           if (!$option) { // If no value exists
               $option = '5'; // Set to our default
           }
           ?>
    <input type="number" 
           id="<?php echo esc_attr($args['label_for']); ?>" 
           name="<?php echo esc_attr($args['label_for']); ?>"
           value="<?php echo esc_attr('' . $option . '') ?>" />
           <?php
           register_setting('skyz', $args['label_for']);
       }

       function skyz_field_server_text($args) {  // Textbox Callback
           $option = get_option($args['label_for']);
           if (!$option) { // If no value exists
               $option = ''; // Set to our default
           }
           ?>
    <input type="text" 
           id="<?php echo esc_attr($args['label_for']); ?>" 
           name="<?php echo esc_attr($args['label_for']); ?>"
           value="<?php echo esc_attr('' . $option . '') ?>" />
           <?php
           register_setting('skyz', $args['label_for']);
       }

       function skyz_field_server_textpass($args) {  // Textbox Callback
           $option = get_option($args['label_for']);
           if (!$option) { // If no value exists
               $option = ''; // Set to our default
           }
           ?>
    <input type="password" 
           id="<?php echo esc_attr($args['label_for']); ?>" 
           name="<?php echo esc_attr($args['label_for']); ?>"
           value="<?php echo esc_attr('' . $option . '') ?>" />
           <?php
           register_setting('skyz', $args['label_for']);
       }

       /**
        * top level menu
        */
       function skyz_options_page() {
           // add top level menu page
           add_menu_page(
                   'Skyz', 'Skyz Rest Options', 'manage_options', 'skyz', 'skyz_options_page_html', get_site_url() . '/wp-content/plugins/skyz/admin/logosm.png'
           );
           global $menu;
           $url = 'http://crm-erp.co.il/';
           $menu[0] = array(__('SkyzCRM'), 'read', $url, 'skyz-logo', 'skyz-logo');
       }

       function skyz_admin_style() {
           $css = get_site_url() . '/wp-content/plugins/skyz/admin/';
           echo '<link rel="stylesheet" href="' . $css . 'admin_style.css" type="text/css" media="all" />';
       }

       /**
        * top level menu:
        * callback functions
        */
       function skyz_options_page_html() {
           // check user capabilities
           if (!current_user_can('manage_options')) {
               return;
           }

           // add error/update messages
           // check if the user have submitted the settings
           // wordpress will add the "settings-updated" $_GET parameter to the url
           if (isset($_GET['settings-updated'])) {
               // add settings saved message with the class of "updated"
               add_settings_error('skyz_messages', 'skyz_message', __('Settings Saved', 'skyz'), 'updated');
           }

           // show error/update messages
           settings_errors('skyz_messages');
           ?>
    <div class="wrap" dir="ltr">
        <div class="wp-skyz-logo" dir="ltr">
            <img class="skyz-logo" src="https://crm-erp.co.il/.well-known/logo.png" alt="<?php echo esc_html(get_admin_page_title()); ?>">
        </div>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "skyz"
            settings_fields('skyz');
            // output setting sections and their fields
            // (sections are registered for "skyz", each field is registered to a specific section)
            do_settings_sections('skyz');
            // output save settings button
            submit_button('Save Settings');
            ?>
        </form>
        <!--
        </div>
        <div class="wrap">-->
        <?php
        // Check whether the button has been pressed AND also check the nonce
        if (isset($_POST['skyz_test_button']) && check_admin_referer('skyz_test_button_clicked')) {
            // the button has been pressed AND we've passed the security check
            skyz_test_button_action();
        }
        echo '<form action="admin.php?page=skyz&skyz_func-test-button=skyz_func-test-button" method="post">';

        // this is a WordPress security feature - see: https://codex.wordpress.org/WordPress_Nonces
        wp_nonce_field('skyz_test_button_clicked');
        echo '<input type="hidden" value="true" name="skyz_test_button" />';
        $other_attributes = array('name' => 'btntest');
        submit_button('Test Tokenizer REST', '', '', true, $other_attributes);
        echo '</form>';
        ?>
    </div>
    <?php
}

function skyz_test_button_action() {
    $plc_token = skyz_plc_rest_tokenize();
    $color = (strlen($plc_token) > 0 ? 'green' : 'red');
    echo '<div id="message" class="updated fade"><p>
    Test 1. "Tokenizing" begin.got token:<font color="' . $color . '">' . $plc_token . '</font></p></div>';

    $category = '2111403020000';
    $data = skyz_Lead('testlead', 'notes', '0545700434', 'a@b.com', '', '');

    $reslt = skyz_plc_rest_post($plc_token, $category, $data);
    $newrecordid = skyz_plc_parceresult($reslt['json'], "1001");
    $color = ($newrecordid === '0' ? 'red' : 'green');
    $newrecordidmessage = ($newrecordid === '0' ? 'some thing wrong' : $newrecordid);
    echo '<div id="message2" class="updated fade"><p>
    Test 2. "Post fake data and get new record_id" got:<font color="' . $color . '">' . $newrecordidmessage . '</font></p></div>';
    echo '<div id="message3" class="updated fade"><p>
    Test 3. "Local record_id" got:<font color="' . $color . '">' . $reslt['localrecid'] . '</font></p></div>';
}
