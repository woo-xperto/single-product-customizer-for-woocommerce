<?php 
/**
* Plugin Name:       Single Product Customizer
* Plugin URI:        https://www.wooxperto.com/single-product-page-customizer/
* Description:       An esential helper tool for woocommerce single product page. Borderless freedom to customize single product page. 
* Version:           1.0.0
* Requires at least: 4.0
* Author:            WooXperto
* Author URI:        https://www.wooxperto.com/
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:       single-product-customizer
* Domain Path:       /languages
*/


if( ! defined( 'ABSPATH' ) ){
    exit();
} 


add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'sppcfw_plugin_action_links' );
function sppcfw_plugin_action_links( $links ) {
    $action_links = array(
        'settings' => '<a href="' . admin_url( 'edit.php?post_type=product&page=sppcfw-single-product-customizer' ) . '" aria-label="' . esc_attr__( 'View Single Product Customizer Settings', 'single-product-customizer' ) . '">' . esc_html__( 'Settings', 'single-product-customizer' ) . '</a>',
    );

    return array_merge( $action_links, $links );
}

define("SPPCFW_DEV", 1);
define("SPPCFW_VERION", '1.0.0');
define("SPPCFW_DIR_URL", plugin_dir_url(__FILE__) );
define("SPPCFW_DIR_PATH", plugin_dir_path(__FILE__) );
$sppcfw_basic=get_option('sppcfw_basic');
define('SPPCFW_BASIC',$sppcfw_basic); // basic settings



$sppcfw_advanced=get_option('sppcfw_advanced');

define('SPPCFW_ADVANCED',$sppcfw_advanced); // advanced settings

define('SPPCFW_PRO_ACTIVE',true);

$SPPCFW_INDIVIDUAL=array();// global var
add_action('wp',function(){
    if(!is_admin()){
        if(is_singular( 'product' )){
            global $post;
            $product_id=$post->ID;
            global $SPPCFW_INDIVIDUAL;
            $sppcfw_individual_product_settings=get_post_meta($product_id,'sppcfw_product',true);
            $SPPCFW_INDIVIDUAL=$sppcfw_individual_product_settings;
        }
    }
});


include(SPPCFW_DIR_PATH.'/backend/resources/hook-list.php');
include(SPPCFW_DIR_PATH.'/common/util.php');
include(SPPCFW_DIR_PATH.'/backend/backend-master.php');
include(SPPCFW_DIR_PATH.'/frontend/frontend-master.php');

register_activation_hook(__FILE__, 'sppcfw_plugin_activate');
function sppcfw_plugin_activate(){
    add_option('sppcfw_plugin_do_activation_redirect', true);
    $now = strtotime( "now" );
    add_option( 'sppcfw_myplugin_activation_date', $now );
}

add_action('admin_init', 'sppcfw_plugin_redirect');

function sppcfw_plugin_redirect() {
    if (get_option('sppcfw_plugin_do_activation_redirect', false)) {
        delete_option('sppcfw_plugin_do_activation_redirect');
        wp_redirect(admin_url( 'edit.php?post_type=product&page=sppcfw-single-product-customizer' ));
        exit();
    }
}

/*
* Check date on admin initiation and add to admin notice if it was over 10 days ago.
*
* @link   https://www.winwar.co.uk/2014/10/ask-wordpress-plugin-reviews-week/?utm_source=codesnippet
*
* @return null
*/
function sppcfw_check_installation_date() {
 
    $install_date = get_option( 'sppcfw_myplugin_activation_date' );
    $review_dismissed  = get_option( 'sppcfw_review_dismissed' );
    $past_date = strtotime( '-7 days' );
    if ( $past_date == $install_date && !$review_dismissed ) {
 
        add_action( 'admin_notices', 'sppcfw_display_admin_notice' );
 
    }
 
}
add_action( 'admin_init', 'sppcfw_check_installation_date' );
 
/**
* Display Admin Notice, asking for a review
*
* @return null
*/


function sppcfw_display_admin_notice() {

    // Review URL - Change to the URL of your plugin on WordPress.org
    $review_url = esc_url('http://wordpress.org/');
    $dismiss_url = esc_url(get_admin_url() . '?dismiss-review=1');
    // Plugin image URL
    $logo_url = esc_url(plugin_dir_url(__FILE__) . 'backend/resources/images/logo.webp');

    // Escaping message for proper display with a line break
    $message = esc_html__('Hello! Seems like you have used Single Product Customizer for this website — Thanks a lot!', 'single-product-customizer') . '<br>' .
        esc_html__('Could you please do us a big favor and give it a 5-star rating on WordPress? This would boost our motivation and help other users make a comfortable decision while choosing the Single Product Customizer.', 'single-product-customizer');

    echo '<div id="sppcfw-review-notice" class="updated sppcfw_sreview_notices">';
    
    printf(
        '<span class="logo"><img src="%s" alt="%s"/></span> <ul class="right_contes"><li>%s</li> <li class="button_wrap">
        <a href="%s" target="_blank">%s</a> 
        <button type="button" id="sppcfw-dismiss-btn"><i class="fas fa-check-circle"></i> %s</button> 
        <a href="%s" target="_blank"><i class="fas fa-life-ring"></i> %s</a>
        <button type="button" id="sppcfw-not-good-enough-btn"><i class="fas fa-thumbs-down"></i> %s</button>',
        esc_attr($logo_url),
        esc_attr__('Plugin Logo', 'single-product-customizer'),
        esc_attr($message),
        esc_attr($review_url),
        esc_html__('Ok, you deserved it', 'single-product-customizer'),
        esc_html__('I already did', 'single-product-customizer'),
        esc_attr($dismiss_url),
        esc_html__('I need support', 'single-product-customizer'),
        esc_html__('No, not good enough', 'single-product-customizer')
    );

    echo '</div>';
}


/**
* Set the plugin to no longer bug users if user asks not to be.
*
* @return null
*/
function sppcfw_set_no_review() {

    $sppcfw_review_dismissed  = "";

    if ( isset( $_GET['dismiss-review'] ) ) {
        $sppcfw_review_dismissed = sanitize_text_field( $_GET['dismiss-review'] );
    }

    if ( intval($sppcfw_review_dismissed) === 1 ) {

        add_option( 'sppcfw_review_dismissed', TRUE );

    }

} add_action( 'admin_init', 'sppcfw_set_no_review', 5 );


function sppcfw_enqueue_scripts() {
    wp_enqueue_script(
        'sppcfw_backend-notices-js',
        plugin_dir_url(__FILE__) . 'backend/resources/js/admin-notices.js',
        ['jquery'],
        '1.0',
        true
    );

    // Localize script with nonce and AJAX URL
    wp_localize_script( 'sppcfw_backend-notices-js', 'sppcfw_obj', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'sppcfw_nonce' )
    ));
}
add_action( 'admin_enqueue_scripts', 'sppcfw_enqueue_scripts' );

// Handle AJAX request dismiss review notice
add_action('wp_ajax_sppcfw_dismiss_review_notice', 'sppcfw_dismiss_review_notice_callback');
function sppcfw_dismiss_review_notice_callback() {

    check_ajax_referer('sppcfw_nonce', 'nonce');

    update_option('sppcfw_review_dismissed', true);

    wp_send_json_success(['message' => 'Notice dismissed successfully.']);
}

// Handle AJAX request and send notification to the admin email
add_action('wp_ajax_sppcfw_send_admin_notification', 'sppcfw_send_admin_notification_callback');

function sppcfw_send_admin_notification_callback() {
    // Check nonce for security
    check_ajax_referer('sppcfw_nonce', 'nonce');

    // Sanitize and retrieve the message from the request
    $feedback_message = sanitize_text_field($_POST['message'] ?? '');

    if (empty($feedback_message)) {
        wp_send_json_error(['message' => 'Message is empty.']);
    }

    // Set email recipient, subject, and message
    $admin_email = get_option('admin_email');
    $to_email = 'support@wooxperto.com';
    $subject = 'Plugin Feedback: Single Product Customizer';
    $email_message = 'A user provided the following feedback: ' . $feedback_message;

    // Set headers for "From" to be the admin email
    $headers = [
        'From: ' . get_bloginfo('name') . ' <' . $admin_email . '>',
        'Content-Type: text/html; charset=UTF-8'
    ];

    // Send the email
    $mail_sent = wp_mail($to_email, $subject, $email_message, $headers);

    if ($mail_sent) {
        wp_send_json_success(['message' => 'Feedback sent successfully.']);
    } else {
        wp_send_json_error(['message' => 'Failed to send feedback email.']);
    }

    wp_die();
}





