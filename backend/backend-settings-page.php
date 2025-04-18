<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once('resources/ui-class.php');
if ( class_exists( 'SPPCFW_backend_ui' ) ) {
    $sppcfw_back_ui_obj = new SPPCFW_backend_ui();

	include('basic/basic-settings.php');
	include('advanced/advanced-settings.php');

} // end class exists check

