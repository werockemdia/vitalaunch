<?php
/*
 * Template Name: Store Setup Template
 * description: >-
  Page template without sidebar
 */

get_header(); 

$user = wp_get_current_user();
$user_info = get_userdata($user->ID);
$email = $user_info->user_email;


global $wpdb;    
$result = $wpdb->get_results( "SELECT domain FROM wp_blogs INNER JOIN wp_registration_log ON wp_blogs.blog_id = wp_registration_log.blog_id WHERE email='$email'");
print_r($result);


 get_footer(); ?>
