<?php
/*
Plugin Name: Custom Database Table
Author: yogesh Thakur
Description: Creates a custom database table to store form data and creates a short code [custom_table] to display stored data.
Version: 1.0
*/

function addjQueryFromCDN() {

    wp_enqueue_script( 'jsdeliver', 'https://code.jquery.com/jquery-3.7.1.min.js' );    
}
add_action( 'wp_enqueue_scripts', 'addjQueryFromCDN' );

// Activation Hook
register_activation_hook(__FILE__, 'create_custom_table');

// Deactivation Hook
register_deactivation_hook(__FILE__, 'delete_custom_table');

function create_custom_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_table';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        first_name VARCHAR(255),
        last_name VARCHAR(255),
        email VARCHAR(255),
        company VARCHAR(255),
        country VARCHAR(255),
        city VARCHAR(255),
        state VARCHAR(255),
        phone_number VARCHAR(20),
        job VARCHAR(255),
        created_at DATETIME,  
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function delete_custom_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_table';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}


function handle_form_submission() {
    global $wpdb;

    if (isset($_POST['et_pb_contactform_submit_0'])) { 
        $data = array();
        $data['first_name'] = sanitize_text_field($_POST['et_pb_contact_name_0']);
        $data['last_name'] = sanitize_text_field($_POST['et_pb_contact_last_name_0']);
        $data['email'] = sanitize_email($_POST['et_pb_contact_email_0']);
        $data['company'] = sanitize_text_field($_POST['et_pb_contact_company_0']);
        $data['country'] = sanitize_text_field($_POST['et_pb_contact_country_0']);
        $data['city'] = sanitize_text_field($_POST['et_pb_contact_city_0']);
        $data['state'] = sanitize_text_field($_POST['et_pb_contact_state_0']);
        $data['phone_number'] = sanitize_text_field($_POST['et_pb_contact_phone_0']);
        $data['job'] = sanitize_text_field($_POST['et_pb_contact_job_role_0']);

        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['phone_number']) || empty($data['company']) || empty($data['country']) || empty($data['city']) ||
        empty($data['state']) || empty($data['job']))
        {            
            return; 
        }
        $current_datetime = current_time('mysql');

        $response = insert_data_into_database($data);

        $table_name = $wpdb->prefix . 'custom_table';
        $wpdb->insert(
            $table_name,
            array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'company' => $company,
                'country' => $country,
                'city' => $city,
                'state' => $state,
                'phone_number' => $phone_number,
                'job' => $job,
                'created_at' => $current_datetime,  
            )
        );
    }
}

add_action('init', 'handle_form_submission');

function custom_table_shortcode() {
    ob_start();
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_table';
 
    $data = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
 
    if (!empty($data)) {
        echo '<table style="width: 100%; border-collapse: collapse;">';
        echo '<thead>';
        echo '<tr>';
        echo '<th style="border: 1px solid #ccc; padding: 10px; text-align: left; background-color: #f2f2f2;">ID</th>';
        echo '<th style="border: 1px solid #ccc; padding: 10px; text-align: left; background-color: #f2f2f2;">First Name</th>';
        echo '<th style="border: 1px solid #ccc; padding: 10px; text-align: left; background-color: #f2f2f2;">Last Name</th>';
        echo '<th style="border: 1px solid #ccc; padding: 10px; text-align: left; background-color: #f2f2f2;">Email</th>';
        echo '<th style="border: 1px solid #ccc; padding: 10px; text-align: left; background-color: #f2f2f2;">Company</th>';
        echo '<th style="border: 1px solid #ccc; padding: 10px; text-align: left; background-color: #f2f2f2;">Country</th>';
        echo '<th style="border: 1px solid #ccc; padding: 10px; text-align: left; background-color: #f2f2f2;">State</th>';
        echo '<th style="border: 1px solid #ccc; padding: 10px; text-align: left; background-color: #f2f2f2;">Phone Number</th>';
        echo '<th style="border: 1px solid #ccc; padding: 10px; text-align: left; background-color: #f2f2f2;">Job</th>';
        echo '<th style="border: 1px solid #ccc; padding: 10px; text-align: left; background-color: #f2f2f2;">Created At</th>'; // Added this line
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
 
        foreach ($data as $row) {
            echo '<tr>';
            echo '<td style="border: 1px solid #ccc; padding: 10px; text-align: left;">' . $row['id'] . '</td>';
            echo '<td style="border: 1px solid #ccc; padding: 10px; text-align: left;">' . $row['first_name'] . '</td>';
            echo '<td style="border: 1px solid #ccc; padding: 10px; text-align: left;">' . $row['last_name'] . '</td>';
            echo '<td style="border: 1px solid #ccc; padding: 10px; text-align: left;">' . $row['email'] . '</td>';
            echo '<td style="border: 1px solid #ccc; padding: 10px; text-align: left;">' . $row['company'] . '</td>';
            echo '<td style="border: 1px solid #ccc; padding: 10px; text-align: left;">' . $row['country'] . '</td>';
            echo '<td style="border: 1px solid #ccc; padding: 10px; text-align: left;">' . $row['state'] . '</td>';
            echo '<td style="border: 1px solid #ccc; padding: 10px; text-align: left;">' . $row['phone_number'] . '</td>';
            echo '<td style="border: 1px solid #ccc; padding: 10px; text-align: left;">' . $row['job'] . '</td>';
            echo '<td style="border: 1px solid #ccc; padding: 10px; text-align: left;">' . $row['created_at'] . '</td>'; // Display the created_at column
            echo '</tr>';
        }
 
        echo '</tbody>';
        echo '</table>';
    } else {
        echo 'No data found.';
    }
 
    return ob_get_clean();
}

add_shortcode('custom_table', 'custom_table_shortcode');

function submit_form_data_callback() {
   
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $email = sanitize_email($_POST['email']);
    $company = sanitize_text_field($_POST['company']);
    $country = sanitize_text_field($_POST['country']);
    $state = sanitize_text_field($_POST['state']);
    $phone_number = sanitize_text_field($_POST['phone_number']);
    $job = sanitize_text_field($_POST['job']);
    $current_datetime = current_time('mysql');
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_table'; 

    $data = array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'company' => $company,
        'country' => $country,
        'state' => $state,
        'phone_number' => $phone_number,
        'job' => $job,
        'created_at' => $current_datetime,
    );

    $wpdb->insert($table_name, $data);

    wp_die();
}

add_action('wp_ajax_submit_form_data', 'submit_form_data_callback');
add_action('wp_ajax_nopriv_submit_form_data', 'submit_form_data_callback');