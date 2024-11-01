<?php
if(!defined('ABSPATH')){
	exit;	
}

function wrdsrv_frontend_save_vote(){
	global $wpdb, $current_user;
	
	
	//$sid = $_POST["sid"];
	if(is_array($_POST["check"])){
		if(empty($_POST["check"])){
			$check = array();
		} else {
			$check = $_POST["check"];
		}
	} else {
		$check = sanitize_text_field($_POST["check"]);
	}
	
	if(is_array($_POST["sel"])){
		if(empty($_POST["sel"])){
			$sel = array();
		} else {
			$sel = $_POST["sel"];
		}
	} else {
		$sel = sanitize_text_field($_POST["sel"]);
	}
	
	if(is_array($_POST["rad"])){
		if(empty($_POST["rad"])){
			$rad = array();
		} else {
			$rad = $_POST["rad"];
		}
	} else {
		$rad = sanitize_text_field($_POST["rad"]);
	}
	
	if(is_array($_POST["txt"])){
		if(empty($_POST["txt"])){
			$txt = array();
		} else {
			$txt = $_POST["txt"];
		}
	} else {
		$txt = sanitize_text_field($_POST["txt"]);
	}
	
	$message = '';

	if($check){
		for($c = 0; $c<count($check); $c++){
			if(sanitize_text_field($check[$c]) != ''){
				$ans = explode('_',$check[$c]);
				$add_question_vote = $wpdb->query("UPDATE ".$wpdb->wordsurvey_question." SET sounding_question_totvote = sounding_question_totvote +1 WHERE sounding_question_id = ".$ans[0]);
				if(!$add_question_vote && $add_question_vote != 0){
					$message .= '<span class="alert alert-warning">' . sprintf(__('Error In Adding Votes \'%s\'.', 'wp-wordsurvey'), '') . '</span>';
				}
				$add_answer_vote = $wpdb->query("UPDATE ".$wpdb->wordsurvey_answer." SET sounding_answer_votes = sounding_answer_votes +1 WHERE sounding_answer_id = ".$ans[1]);
				if(!$add_answer_vote && $add_answer_vote != 0){
					$message .= '<span class="alert alert-warning">' . sprintf(__('Error In Adding Votes \'%s\'.', 'wp-wordsurvey'), '') . '</span>';
				}
			}
		}
	}
	if($rad){
		for($r = 0; $r<count($rad); $r++){
			if(sanitize_text_field($rad[$r]) != ''){
				$ans1 = explode('_',$rad[$r]);
				$add_question_vote = $wpdb->query("UPDATE ".$wpdb->wordsurvey_question." SET sounding_question_totvote = sounding_question_totvote +1 WHERE sounding_question_id = ".$ans1[0]);
				if(!$add_question_vote && $add_question_vote != 0){
					$message .= '<span class="alert alert-warning">' . sprintf(__('Error In Adding Votes \'%s\'.', 'wp-wordsurvey'), '') . '</span>';
				}
				$add_answer_vote = $wpdb->query("UPDATE ".$wpdb->wordsurvey_answer." SET sounding_answer_votes = sounding_answer_votes +1 WHERE sounding_answer_id = ".$ans1[1]);
				if(!$add_answer_vote && $add_answer_vote != 0){
					$message .= '<span class="alert alert-warning">' . sprintf(__('Error In Adding Votes \'%s\'.', 'wp-wordsurvey'), '') . '</span>';
				}
			}
		}
	}
	
	if($sel){
		for($s = 0; $s<count($sel); $s++){
			if(sanitize_text_field($sel[$s]) != ''){
				$ans2 = explode('_',$sel[$s]);
				$add_question_vote2 = $wpdb->query("UPDATE ".$wpdb->wordsurvey_question." SET sounding_question_totvote = sounding_question_totvote +1 WHERE sounding_question_id = ".$ans2[0]);
				if(!$add_question_vote2 && $add_question_vote2 != 0){
					$message .= '<span class="alert alert-warning">' . sprintf(__('Error In Adding Votes \'%s\'.', 'wp-wordsurvey'), '') . '</span>';
				}
				$add_answer_vote2 = $wpdb->query("UPDATE ".$wpdb->wordsurvey_answer." SET sounding_answer_votes = sounding_answer_votes +1 WHERE sounding_answer_id = ".$ans2[1]);
				if(!$add_answer_vote2 && $add_answer_vote2 != 0){
					$message .= '<span class="alert alert-warning">' . sprintf(__('Error In Adding Votes \'%s\'.', 'wp-wordsurvey'), '') . '</span>';
				}
			}
		}
	}
	
	if($txt){
		foreach($txt as $t => $text){
			if(sanitize_text_field($txt[$t]) != ''){
				$ans3 = explode('_',$t);
			
				$add_question_vote3 = $wpdb->query("UPDATE ".$wpdb->wordsurvey_question." SET sounding_question_totvote = sounding_question_totvote +1 WHERE sounding_question_id = ".$ans3[0]);
				if(!$add_question_vote3 && $add_question_vote3 != 0){
					$message .= '<span class="alert alert-warning">' . sprintf(__('Error In Adding Votes \'%s\'.', 'wp-wordsurvey'), '') . '</span>';
				}
				$add_answer_text = $wpdb->query("INSERT INTO ".$wpdb->wordsurvey_vote." VALUES (0,".$ans3[1].", '".$text."', now())");
				if(!$add_answer_text){
					$message .= '<span class="alert alert-warning">' . sprintf(__('Error In Adding Text Votes \'%s\'.', 'wp-wordsurvey'), '') . '</span>';
				}
				$add_answer_vote3 = $wpdb->query("UPDATE ".$wpdb->wordsurvey_answer." SET sounding_answer_votes = sounding_answer_votes +1 WHERE sounding_answer_id = ".$ans3[1]);
				if(!$add_answer_vote3 && $add_answer_vote3 != 0){
					$message .= '<span class="alert alert-warning">' . sprintf(__('Error In Adding Text Votes \'%s\'.', 'wp-wordsurvey'), '') . '</span>';
				}
			}
		}
	}
	
	if($message == ''){
		$message .= '<p class="alert alert-success">'. sprintf(__('Thank you for your Votes!', 'wp-wordsurvey'), '') .'</p>';
	}
	
	$return[] = array("msg" => $message);
	echo json_encode($return);
	exit;

}
?>