<?php

//If this file is called directly, abort.
//if ( ! defined( 'WPINC' ) ) {
//	die;
//}

require_once dirname(__DIR__) . '/includes/dbfuncutils.php';

function skyz_plc_get_option() {
    $sslverifyhost = get_option('skyz_field_sslvalidation');
    $timeout = intval(get_option('skyz_field_timeout'));
    if (!$sslverifyhost) { // If no value exists
        $sslverifyhost = 'true'; // Set to our default
    }
    if (!$timeout) { // If no value exists
        $timeout = 5; // Set to our default
    }
    return array(
        'endpoint' => get_option('skyz_field_server'),
        'endpoint_port' => get_option('skyz_field_server_port'),
        'appid' => get_option('skyz_field_server_appid'),
        'appsec' => get_option('skyz_field_server_appsecret'),
        'sslverifyhost' => $sslverifyhost,
        'timeout' => $timeout
    );
}

function skyz_plc_rest_post($plc_token, $category, $data) {
    $json = '';
    $plc_ep_option = skyz_plc_get_option();

    $url = "https://" . $plc_ep_option['endpoint'] . ($plc_ep_option['endpoint_port'] === "443" ? "" : ":" . $plc_ep_option['endpoint_port'] ) . "/planc2/rest_api/CategoryModel_" . $category;
    $localrecid = skyz_db_record_lead($category, $data, $url);
    $pload = array(
        'method' => 'POST',
        'timeout' => $plc_ep_option['timeout'], //Optional timeout value
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'sslverify' => ($plc_ep_option['sslverifyhost'] === 'true' ? true : false),
        'headers' => array('Authorization' => 'Bearer ' . $plc_token, 'Content-type' => 'application/json; charset=UTF-8'),
        'body' => $data,
        'cookies' => array()
    );
    $response = wp_remote_post($url, $pload);
    if (!is_wp_error($response)) {
        $json = json_decode($response['body'], true);
    }
    return array('json' => $json, 'localrecid' => $localrecid);
}

function skyz_plc_parceresult($json, $find) {
    $ret = '0';
    $jsonloc = $json['Update_Status'];
    foreach ($jsonloc as $obj) {
        if ($obj['old_value'] === $find) {
            $ret = $obj['new_value'];
            break;
        }
    }
    return $ret;
}

function skyz_plc_rest_tokenize() {
    $plc_token = "";
    $plc_ep_option = skyz_plc_get_option();

    $data = "client_id=" . $plc_ep_option['appid'] . "&client_secret=" . $plc_ep_option['appsec'] . "&grant_type=client_credentials";
    $url = "https://" . $plc_ep_option['endpoint'] . ($plc_ep_option['endpoint_port'] === "443" ? "" : ":" . $plc_ep_option['endpoint_port'] ) . "/planc2/api/token";
    $pload = array(
        'method' => 'POST',
        'timeout' => $plc_ep_option['timeout'], //Optional timeout value
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'sslverify' => ($plc_ep_option['sslverifyhost'] === 'true' ? true : false),
        'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
        'body' => $data,
        'cookies' => array()
    );
    $response = wp_remote_post($url, $pload);
    if (!is_wp_error($response)) {
        $json = json_decode($response['body'], true);
        $plc_token = $json['access_token'];
    }
    return $plc_token;
}

function skyz_Lead($name, $notes, $phone, $email, $ownerid, $campaignid) {
    $phob = '';
    if (isset($phone) && $phone !== '') {
        $phob = '{"field_4077":"' . $phone . '","field_7027":"plc_const_comm_type_personal","recid":"1002","entityTypeId":"2110404000000","isLink":false}';
    }
    $mailob = '';
    if (isset($email) && $email !== '') {
        $mailob = '{"field_1053":"' . $email . '","field_7027":"plc_const_email_class_personal","recid":"1003","entityTypeId":"2110403000000" ,"isLink":false}';
    }
    $ownerob = '';
    if (isset($ownerid) && $ownerid !== '') {
        $ownerob = '"cat_7186":[{"field_7187":"200000028449","recid":"1004","entityTypeId":"2111102000000" ,"isLink":true } ],';
    }
    $campob = '';
    if (isset($campaignid) && $campaignid !== '') {
        $campob = '"cat_7149":[{"field_7143":"plc_const_link_stage_first","field_7150":"200002691884" ,"recid":"1005","entityTypeId":"2111103000000","isLink":true}],';
    }
    $notes = json_encode(array('field_4052' => $notes));
    $notes = preg_replace('/{/', '', $notes, 1);
    $notes = substr($notes, 0, -1);
    return '{"field_1016":"' . $name . '",' . $notes . ',"cat_7087":[' . $phob . ($phob !== '' ? ',' : '') . $mailob . '],' . $campob . '' . $ownerob . '"recid":"1001","entityTypeId":"2111403020000" ,"isLink":false}';
}

function skyz_sendrestform($contact_form) {
    $submission = WPCF7_Submission::get_instance();
    // Get the post data and other post meta values
    if ($submission) {
        $posted_data = $submission->get_posted_data();
        $remote_ip = $submission->get_meta('remote_ip');
        $url = $submission->get_meta('url');
        $timestamp = gmdate("Y-m-d H:i:s", $submission->get_meta('timestamp'));
        $title = wpcf7_special_mail_tag('', '_post_title', '');

        // If you have checkboxes or other multi-select fields, make sure you convert the values to a string  
        // $mycheckbox1 = implode(", ", $posted_data["checkbox-465"]);
        //get selector if specified as defined in option
        $onstage = get_option('skyz_field_stage');
        if (isset($onstage) && isset($posted_data[$onstage]) && $posted_data[$onstage] !== '') {
            $onstagevalue = $posted_data[$onstage];
            
            if ($posted_data["your-name"]) {
                $name = trim($posted_data["your-name"]);
            }

            $notes = $posted_data["your-message"];
            $ownerid = $posted_data["plc-owner-id"];
            $campaignid = $posted_data["plc-campaign-id"];
            $phone = $posted_data["your-phone"];
            $email = $posted_data["your-email"];
            $category = '';
            $data = '';
            switch ($onstagevalue) {
                case "plc_lead":
                    $category = '2111403020000';
                    $data = skyz_Lead($name, $notes, $phone, $email, $ownerid, $campaignid);
                    break;
                case "plc_tiket":
                    $category = '';
                    //not implemented yet                
                    break;
                default:
                    //here log
                    break;
            }
            if ($category !== '') {
                $token = skyz_plc_rest_tokenize();
                // Finally send the data to your endpoint
                skyz_plc_rest_post($token, $category, $data);
            }
        }
    }
}

function skyz_sendrestform_drv($contact_form) {
    skyz_sendrestform($contact_form);
    return;
}
