<?php

$cdnvote_flg = false;
//Load WP-Config File
if (!function_exists('add_action')){
	$cdnvote_flg = true;
	require_once("../../../wp-config.php");
}

// user interaction
if (isset($_POST['cdnvote_post_id']) && isset($_POST['cdnvote_point']) && $cdnvote_flg) {
	global $wpdb;
	$table_name = $wpdb->prefix . "cdnvote";
	$post_id = $_POST['cdnvote_post_id'];
	$vote_point = $_POST['cdnvote_point'];
	if(is_numeric($post_id) && is_numeric($vote_point)){
		$result = $wpdb->query($wpdb->prepare("INSERT INTO " . $table_name . "(post_id,vote_point,vote_count,recent_ip,vote_active)VALUE(" . $post_id . "," . $vote_point . ",1,'" . $_SERVER['REMOTE_ADDR'] . "',true) ON DUPLICATE KEY UPDATE vote_count = vote_count + 1,vote_point = vote_point + " . $vote_point . ",recent_ip = '" . $_SERVER['REMOTE_ADDR'] . "';"));
	}
}


?>