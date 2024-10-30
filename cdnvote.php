<?php
/*
Plugin Name: cdnvote
Version: 0.4.2
Plugin URI: http://www.crossdrive.net/archives/686
Description: wordpressの記事に投票機能を追加するプラグイン。Add a vote function to article.
Author: Nakahira
Author URI: http://www.crossdrive.net

Copyright (c) 2009
Released under the GPL license
http://www.gnu.org/licenses/gpl.txt

INSTALL: 
   1. Upload cdnvote dirctory to your WordPress /wp-content/plugins/ directory or install through the auto-installer
   2. Activate the plugin through the ‘Plugins’ menu in WordPress
   3. Please insert a tag of cdnvote displaying a vote form or a result in a template.
*/

require_once(dirname(__FILE__).'/lib/cdnvote-lib.php');


//Output HTML form element.This all id attribute is delicate.
function show_cdnvote_form(){
	global  $id;
	$cdnvote = new cdnvote;
	
	if($cdnvote->is_active($id) || !($cdnvote->is_exist($id))){
		$user_ip_address = $_SERVER['REMOTE_ADDR'];
		echo '<div class="cdnvote">';
		echo '<p>Was this post helpful to you ?  (5 point scale)</p>';
		echo '<form method="post" action="' .get_bloginfo('wpurl') . '/wp-content/plugins/cdnvote/cdnvote-post.php" id="cdnvote_form_' . $id .'">';
		echo '<ul class="cdnvote_vote_item">';
		echo '<li><input type="radio" name="cdnvote_point" value="5" id="cdnvote_point_' . $id . '_5" /><label for="cdnvote_point_' . $id . '_5">Very Good (5point)</label></li>';
		echo '<li><input type="radio" name="cdnvote_point" value="4" id="cdnvote_point_' . $id . '_4" /><label for="cdnvote_point_' . $id . '_4">Good (4point)</label></li>';
		echo '<li><input type="radio" name="cdnvote_point" value="3" id="cdnvote_point_' . $id . '_3" checked="checked" /><label for="cdnvote_point_' . $id . '_3">Normal (3point)</label></li>';
		echo '<li><input type="radio" name="cdnvote_point" value="2" id="cdnvote_point_' . $id . '_2" /><label for="cdnvote_point_' . $id . '_2">Bad (2point)</label></li>';
		echo '<li><input type="radio" name="cdnvote_point" value="1" id="cdnvote_point_' . $id . '_1" /><label for="cdnvote_point_' . $id . '_1">Very Bad (1point)</label></li>';
		echo '</ul>';
		echo '<input type="hidden" name="cdnvote_post_id" value="' . $id . '" />';
		if(!$cdnvote->is_recent_vote($id,$user_ip_address)){
			echo '<button type="button" onclick="cdnvote_post(document.getElementById(\'cdnvote_form_' . $id . '\'))" class="cdnvote_post_button" id="cdnvote_form_button_' . $id . '">Vote</button>';
		}else{
			echo '<p>Already voted.</p>';
		}
		echo '</form>';
		echo '</div>';
	}
}

//
function show_cdnvote_count() {
	global $wpdb,$id;
	$cdnvote = new cdnvote;
	if($cdnvote->is_active($id)){
		$table_name = $wpdb->prefix . "cdnvote";
		$result = $wpdb->get_var($wpdb->prepare("SELECT vote_count FROM $table_name WHERE post_id = $id"));
		if (!is_null($result)) {
			echo "votes:" . $result;
		}
	}
}

//
function show_cdnvote_average() {
	global $wpdb,$id;
	$cdnvote = new cdnvote;
	if($cdnvote->is_active($id)){
		$table_name = $wpdb->prefix . "cdnvote";
		$result = $wpdb->get_var($wpdb->prepare("SELECT vote_point/vote_count FROM $table_name WHERE post_id = $id"));
		if(strlen($result) > 3){
			$result = substr($result,0,3);
		}
		echo "average:" . $result;
	}
}

//Output top10 order list.
function show_cdnvote_list() {
	$cdnvote = new cdnvote;
	echo $cdnvote->show_list(10);//10 is default number of list value.
}

//
function activate_cdnvote(){
	$cdnvote = new cdnvote;
	$cdnvote->install();
}
register_activation_hook(__FILE__,'activate_cdnvote');

//
function deactivate_cdnvote(){
	$cdnvote = new cdnvote;
	$cdnvote->uninstall();
}
register_deactivation_hook(__FILE__,'deactivate_cdnvote');

//
function delete_post_cdnvote($post_id){
	global $wpdb;
	$table_name = $wpdb->prefix . "cdnvote";
	$wpdb->query($wpdb->prepare('DELETE FROM ' . $table_name . ' WHERE post_id = ' . $post_id));
	return $post_id;
}
add_action('delete_post', 'delete_post_cdnvote');

//
function add_head_cdnvote_jsandcss(){
	$wp_url = get_bloginfo('wpurl') . "/";
	echo "\n\t<!-- Added By cdnvote Plugin. -->";
	echo "\n\t<script type='text/javascript' src='{$wp_url}wp-content/plugins/cdnvote/js/cdnvote.js'></script>";
	echo "\n\t<link rel='stylesheet' href='{$wp_url}wp-content/plugins/cdnvote/css/cdnvote.css' type='text/css' />\n";
}
add_action('wp_head', 'add_head_cdnvote_jsandcss');

function widget_cdnvote_init(){
	if (!function_exists('register_sidebar_widget')){
		return;
	}
	
	function widget_cdnvote( $args ){
		$cdnvote = new cdnvote;

		extract( $args );
		$options = get_option( 'widget_cdnvote' );
		$title = $options['title'];
		$list_num = $options['list_num'];
		
		echo $before_widget; 
		echo $before_title . $title . $after_title; 
		echo $cdnvote->show_list($list_num); 
		echo $after_widget;
	}
	register_sidebar_widget('cdnvote','widget_cdnvote');
	
	function widget_cdnvote_control(){
		$options = get_option('widget_cdnvote');
		if (!is_array( $options )){
			$options = array(
				'title' => 'Highest',
				'list_num' => '10'
			);
		}
		
		$title = $options['title'];
		$list_num = $options['list_num'];
		
		if ($_POST['cdnvote_submit']) {
			$options['title'] = stripslashes($_POST['cdnvote_title']);
			if(is_numeric(stripslashes($_POST['cdnvote_list_num'])) && stripslashes($_POST['cdnvote_list_num']) <= 50){//maximum number of list is 50.
				$list_num = stripslashes($_POST['cdnvote_list_num']);
			}else{
				$list_num = 10;//default number of list is 10.
			}
			$options['list_num'] = $list_num;
			update_option('widget_cdnvote', $options);
		}
	
		echo '<p>';
		echo '	<label for="cdnvoteTitle">Title : </label>';
		echo '	<input type="text" id="cdnvote_title" name="cdnvote_title" value="' . $title . '" />';
		echo '</p>';
		echo '<p>';
		echo '	<label for="cdnvoteListNum">Number of list : </label>';
		echo '	<input type="text" style="width: 30px;" maxlength="2" id="cdnvote_list_num" name="cdnvote_list_num" value="' . $list_num . '" />';
		echo '	<br /><small>(maximum 50)</small>';
		echo '	<input type="hidden" id="cdnvote_submit" name="cdnvote_submit" value="1" />';
		echo '</p>';
	}
	register_widget_control('cdnvote','widget_cdnvote_control');
}
add_action('plugins_loaded','widget_cdnvote_init');

//
function edit_form_cdnvote(){ 
	$cdnvote = new cdnvote;
	echo '<div id="test_tab" class="postbox ' . postbox_classes('test_tab', 'page') . '">';
	echo '<h3>cdnvote</h3>';
	echo '<div class="inside">';
	if(isset($_GET['post']) && !$cdnvote->is_active($_GET['post'])){//(!$cdnvote->is_active($post_id)){
		echo '
		<ul>
		<li><input type="radio" name="cdnvote_is_active" value="1" id="cdnvote_active" /><label for="cdnvote_active">ON</label></li>
		<li><input type="radio" name="cdnvote_is_active" value="0" id="cdnvote_deactive" checked="checked"  /><label for="cdnvote_deactive">OFF</label></li>
		</ul>
	';
	}else{
		echo '
		<ul>
		<li><input type="radio" name="cdnvote_is_active" value="1" id="cdnvote_active" checked="checked" /><label for="cdnvote_active">ON</label></li>
		<li><input type="radio" name="cdnvote_is_active" value="0" id="cdnvote_deactive" /><label for="cdnvote_deactive">OFF</label></li>
		</ul>
	';
	}
	echo '</div>';
	echo '</div>';
}
add_action('edit_form_advanced', 'edit_form_cdnvote' );
add_action('edit_page_form', 'edit_form_cdnvote' );

//
function publish_post_cdnvote($post_id){
	global $wpdb;
	$table_name = $wpdb->prefix . "cdnvote";
	$is_active = 0;
	if(($_POST['visibility'] == 'public') && ($_POST['cdnvote_is_active'])) {
		$is_active = $_POST['cdnvote_is_active'];
	}
	$wpdb->query($wpdb->prepare("INSERT INTO " . $table_name . "(post_id,vote_point,vote_count,vote_active)VALUE(" . $post_id . ",0,0," . $is_active . ") ON DUPLICATE KEY UPDATE vote_active = " . $is_active));
	return $post_id;
}
add_action('publish_post', 'publish_post_cdnvote' );
add_action('publish_page', 'publish_post_cdnvote' );

?>