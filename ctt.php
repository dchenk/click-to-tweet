<?php
/*
Plugin Name: Click To Tweet
Description: This plugin integrates with the Click To Tweet web app (located at clicktotweet.com) and allows you to insert Click To Tweet boxes in your blog posts.
Version: 3.0.0
Author: ClickToTweet.com and Wider Webs
Author URI: https://github.com/dchenk
Plugin URI: https://github.com/dchenk/click-to-tweet
*/
if (!defined('WPINC')) {
	die;
}

function activate_plug_ctt() {
	require_once plugin_dir_path(__FILE__) . 'includes/plugin-activation.php';
	ctt_activation::activate();
}
register_activation_hook(__FILE__, 'activate_plug_ctt');

function call_ctt_class() {
	$dirPath = plugin_dir_path(__FILE__);
	require_once $dirPath . 'includes/ctt-class.php';
	require_once $dirPath . 'includes/ctt-shortcode.php';
	require_once $dirPath . 'includes/setting-action-ajax.php';
	new ctt_load_ajax;
	return new CTTshortcode();
}

call_ctt_class();
