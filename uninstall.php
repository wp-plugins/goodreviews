<?php
/* This uninstall.php file is part of the GoodReviews plugin for WordPress
 * 
 * This file is distributed as part of the GoodReviews plugin for WordPress
 * and is not intended to be used apart from that package. You can download
 * the entire ScrapeAZon plugin from the WordPress plugin repository at
 * http://wordpress.org/plugins/goodreviews/
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

if(!defined('ABSPATH')&& !defined('WP_UNINSTALL_PLUGIN'))
{
    exit();
}

$jhgrUser = wp_get_current_user();

// Remove settings stored in database
delete_option('goodreviews-api-key');
delete_option('goodreviews-getmethod');
delete_option('goodreviews-agree');
delete_option('goodreviews-alt-style');
delete_option('goodreviews-responsive-style');

// Remove ScrapeAZon hidden notices
$jhgrMeta_type  = 'user';
$jhgrUser_id    = 0;
$jhgrMeta_value = '';
$jhgrDelete_all = true;

delete_metadata( $jhgrMeta_type, $jhgrUser_id, 'goodreviews_ignore_FileGetEnabled', $jhgrMeta_value, $jhgrDelete_all );
delete_metadata( $jhgrMeta_type, $jhgrUser_id, 'goodreviews_ignore_CurlEnabled', $jhgrMeta_value, $jhgrDelete_all );
delete_metadata( $jhgrMeta_type, $jhgrUser_id, 'goodreviews_ignore_CurlDisabled', $jhgrMeta_value, $jhgrDelete_all );
?>