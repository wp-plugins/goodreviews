<?php
/*
Plugin Name: GoodReviews
Plugin URI: http://www.timetides.com/goodreviews-plugin-wordpress
Description: Retrieves Goodreads.com reviews for books you choose to display on your Wordpress blog.
Version: 1.1.2
Author: James R. Hanback, Jr.
Author URI: http://www.timetides.com
License: GPL3

/*  Copyright 2011	James R. Hanback, Jr.  (email : james@jameshanback.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Load plugin files and configuration
$grplugin = plugin_basename(__FILE__); 
$greviewspath = plugin_dir_path(__FILE__);
$greviewspath .= '/goodreviews-functions.php';
include_once($greviewspath);

// Register default stylesheet
$gr_alt_style = get_option('goodreviews-alt-style');
$grdefaults = plugins_url('goodreviews/goodreviews.css');

function gr_enqueues() {
  // If it exists and is a URL, register alternate stylesheet
  if(preg_match('/^(http|https):\/\//i',get_option('goodreviews-alt-style'))) {
    wp_register_style('gr-alternate-style',get_option('goodreviews-alt-style'));
    wp_enqueue_style('gr-alternate-style');
  } else {
    // Register default stylesheet
    wp_register_style('gr-default-style',plugins_url('goodreviews/goodreviews.css'));
    wp_enqueue_style('gr-default-style');
  }
}  

add_action('wp_enqueue_scripts', 'gr_enqueues');

// Create admin page and settings, if required
if (is_admin()) {
   add_action('admin_init','goodreviews_register_settings');
   add_action('admin_menu','goodreviews_admin_add_page');
}

// Create a settings link on the plugin page
add_filter("plugin_action_links_$grplugin", 'goodreviews_settings_link' );

// Add shortcode functionality
add_shortcode( 'goodreviews', 'goodreviews_shortcode' );

?>