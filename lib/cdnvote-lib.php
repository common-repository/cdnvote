<?php

class cdnvote{

	function install(){
		global $wpdb;
		$table_name = $wpdb->prefix . "cdnvote";
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			$sql = "CREATE TABLE " . $table_name . " (
				id INT(10) UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
				post_id INT UNSIGNED UNIQUE NOT NULL,
				vote_point INT UNSIGNED NOT NULL,
				vote_count INT UNSIGNED NOT NULL,
				vote_active BOOLEAN NOT NULL,
				recent_ip VARCHAR(20)
				);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			add_option("wpvote_db_version", "1.0");
		}
	}
	
	function uninstall(){
		global $wpdb;
		$table_name = $wpdb->prefix . "cdnvote";
		if($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE '$table_name'")) == $table_name) {
			$sql = "DROP TABLE " . $table_name . ";";
			$wpdb->query($sql);
		}
	}
	
	function is_recent_vote($post_id,$user_ip_address){
		global $wpdb;
		$table_name = $wpdb->prefix . "cdnvote";
		$result = $wpdb->query($wpdb->prepare("SELECT * FROM " . $table_name . " WHERE post_id = " . $post_id . " AND recent_ip = '" . $user_ip_address . "'"));
		return $result;
	}
	
	function is_active($post_id){
		global $wpdb;
		$table_name = $wpdb->prefix . "cdnvote";
		$result = $wpdb->get_var($wpdb->prepare("SELECT vote_active FROM " . $table_name . " WHERE post_id = " . $post_id ));
		return $result;
	}
	
	function is_exist($post_id){
		global $wpdb;
		$table_name = $wpdb->prefix . "cdnvote";
		$result = $wpdb->query($wpdb->prepare("SELECT * FROM " . $table_name . " WHERE post_id = " . $post_id));
		return $result;
	}
	
	function show_list($list_num){
		global $wpdb;
		$table_name = $wpdb->prefix . "cdnvote";
		$pageposts = $wpdb->get_results("SELECT $wpdb->posts.ID,$wpdb->posts.post_title,vote_point/vote_count as average_point FROM $table_name LEFT JOIN $wpdb->posts ON $table_name.post_id = $wpdb->posts.ID WHERE $table_name.vote_count > 0 AND $table_name.vote_active = TRUE ORDER BY vote_point/vote_count DESC LIMIT " . $list_num);

		$cdnvote_list_obj = "<ol class='cdnvote_list'>\n";
		foreach ($pageposts as $post){
			if(strlen($post->average_point) > 3){
				$average_point = substr($post->average_point,0,3);
			}else{
				$average_point = $post->average_point;
			}
			$cdnvote_list_obj = $cdnvote_list_obj . "\t<li><a href='" . get_permalink($post->ID) . "'>" . $post->post_title . "</a> (" . $average_point . ")</li>\n";
		}
		$cdnvote_list_obj = $cdnvote_list_obj . "</ol>\n";
		return $cdnvote_list_obj;
	}
}

?>
