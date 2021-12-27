<?php
/**
 * Dashboard Administration Screen
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Bootstrap */
require_once __DIR__ . '/admin.php';

/** Load WordPress dashboard API */
/**require_once ABSPATH . 'wp-admin/includes/dashboard.php';*/

//wp_dashboard_setup();

wp_enqueue_script( 'dashboard' );