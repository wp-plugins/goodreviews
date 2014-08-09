<?php
/*
 * Plugin Name: GoodReviews
 * Plugin URI: http://www.timetides.com/goodreviews-plugin-wordpress
 * Description: Retrieves Goodreads.com reviews for books you choose to display on your Wordpress blog.
 * Version: 2.1.0-beta3
 * Author: James R. Hanback, Jr.
 * Author URI: http://www.timetides.com
 * License: GPL3
 * Text Domain: goodreviews
 * Domain Path: /lang/
 */

/*  
 * Copyright 2011-2014	James R. Hanback, Jr.  (email : james@jameshanback.com)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
 
// error_reporting(E_ALL);

// Load plugin files and configuration
$jhgrPlugin = plugin_basename(__FILE__); 
$jhgrPPath = plugin_dir_path(__FILE__);
$jhgrPPath .= '/jhgrclasses.php';
include_once($jhgrPPath);

$jhgrOpts = new jhgrWPOptions;
$jhgrReqs = new jhgrRequirements;
$jhgrShcd = new jhgrShortcode;

$jhgrOpts->jhgrRequireStyles();

// Localization
add_action('plugins_loaded', array(&$jhgrReqs, 'jhgrLoadLocal'));

if (is_admin()) {
    add_action('admin_init', array(&$jhgrOpts, 'jhgrRegisterSettings'));
    add_action('admin_init', array(&$jhgrReqs, 'jhgrHideNotices'));
    add_action('admin_menu', array(&$jhgrOpts, 'jhgrAddAdminPage'));
    add_action('admin_notices', array(&$jhgrReqs, 'jhgrShowNotices'));
    add_action('admin_notices', array(&$jhgrOpts, 'jhgrShowNPNotices'));
}

add_filter("plugin_action_links_$jhgrPlugin", array(&$jhgrOpts, 'jhgrOptionsLink'));

// Add shortcode functionality
add_shortcode( 'goodreviews', array(&$jhgrShcd, 'jhgrParseShortcode') );

// Add widgets
add_action('widgets_init',create_function('', 'return register_widget("jhgrBuyBookWidget");'));
add_action('widgets_init',create_function('', 'return register_widget("jhgrBookInfoWidget");'));
add_action('widgets_init',create_function('', 'return register_widget("jhgrReviewsWidget");'));
?>