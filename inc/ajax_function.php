<?php
if(!defined('ABSPATH')){
	exit;	
}

function wrdsrv_save_sounding(){
	global $current_user,$wpdb;
	
	$user = $current_user->user_login;
	$id = $current_user->ID;
	
	$message = '';
	$idtitolo = 0;
	$return = array();
	
	if(isset($_POST['idsounding'])){
		$idsounding = $_POST['idsounding'];
		$ty = $_POST['ty'];
		$title = $_POST['title'];
		$obbl = $_POST['obbl'];
		$num = $_POST['num'];
		$answer = $_POST['answer'];
		$act = $_POST['act'];
	} else {
		$expire = $_POST['expire'];
		$act = $_POST['act'];
		$sound_tit = $_POST['sound_tit'];
	}
	
	
	if($act == "q"){
	
			$question_title = addslashes(trim($title));
			$t = $idsounding. " - " .$ty ." - ".$title." - ".$obbl." - ".$num;
			
			$add_sounding_question = $wpdb->query("INSERT INTO ".$wpdb->wordsurvey_question." VALUES (0,'".$title."', '".$ty."', ".(int)$obbl.", ".(int)$num.", ".(int)$idsounding.",0, now())");
			
				if (!$add_sounding_question) {
					$message .= '<p style="color: red;">' . sprintf(__('Error In Adding Survey Question \'%s\'.', 'wp-wordsurvey'), stripslashes($question_title)) . '</p>';
				} else {
					$message .= '<p style="color: green;">' . sprintf(__('Success In Adding Survey Question \'%s\'.', 'wp-wordsurvey'), stripslashes($question_title)) . '</p>';
				}
					
				$id_sounding_question = $wpdb->get_results("SELECT sounding_question_id FROM ".$wpdb->wordsurvey_question." WHERE sounding_question_soundid = ".(int)$idsounding." ORDER BY sounding_question_ts DESC LIMIT 0,1");
				if($id_sounding_question) {
					foreach($id_sounding_question as $id_quest) {
							$id_question = $id_quest->sounding_question_id;
					}
				}
			
			for($j=0;$j<count($answer);$j++){
				$add_sounding_answer = $wpdb->query("INSERT INTO ".$wpdb->wordsurvey_answer." VALUES (0,".$id_question.", '".$answer[$j]."', 0)");
			}
					
			$return[] = array("idquest" => $id_question, "msg" => $message);
					
	} else if($act == "s"){
						
			$sounding_title = addslashes(trim($sound_tit));
			if(!empty($sound_tit)){
				$add_title_sounding = $wpdb->query("INSERT INTO ".$wpdb->wordsurvey." VALUES (0, '".$sounding_title."', '".$expire."', '".$user."', ".$id.", now())");
				if (!$add_title_sounding) {
					$message .= '<p style="color: red;">' . sprintf(__('Error In Adding Survey \'%s\'.', 'wp-wordsurvey'), stripslashes($sounding_title)) . '</p>';
				} else {
					$message .= '<p style="color: green;">' . sprintf(__('Success In Adding Survey \'%s\'.', 'wp-wordsurvey'), stripslashes($sounding_title)) . '</p>';
				}
			} else {
				$message .= '<p style="color: red;">' . sprintf(__('You can not place an untitled survey', 'wp-wordsurvey'),stripslashes($sounding_title)).'</p>';
			}
			
			$id_title_sounding = $wpdb->get_results("SELECT sounding_id FROM $wpdb->wordsurvey WHERE sounding_userid = $id ORDER BY sounding_ts DESC LIMIT 0,1");
			if($id_title_sounding) {
				foreach($id_title_sounding as $id_title) {
						$idtitolo = $id_title->sounding_id;
				}
			}
			
			$return[] = array("idtitolo" => $idtitolo, "msg" => $message);
	}
	
	echo json_encode($return);
	exit;
}

function wrdsrv_edit_sounding(){
	
	global $current_user,$wpdb;
	$user = $current_user->user_login;
	$id = $current_user->ID;
	
	$message = '';
	$idtitolo = 0;
	$return = array();
	
	$act = $_POST['act'];
	
	if($act == "q"){
	
		$cod = $_POST['cod'];
		if(isset($_POST['ty'])){
			$ty = $_POST['ty'];
			$title = $_POST['title'];
			$obbl = $_POST['obbl'];
			$num = $_POST['num'];
			if(isset($_POST['answer'])){
				$answer = $_POST['answer'];
			} else {
				$answer = array();
			}
		}
		$subact = $_POST['subact'];
		
		if($subact == "d"){
					
			$sounding_answer_del = $wpdb->get_results("SELECT sounding_answer_id FROM ".$wpdb->wordsurvey_answer." WHERE sounding_answer_questid = ".(int)$cod);
			foreach($sounding_answer_del as $sqadel){
				
				$del_answer = $wpdb->query("DELETE FROM ".$wpdb->wordsurvey_answer." WHERE sounding_answer_id = ".$sqadel->sounding_answer_id);
				if (!$del_answer) {
					$message .= '<p style="color: red;">' . sprintf(__('Error In Deleting Question \'%s\'.', 'wp-wordsurvey'), '') . '</p>';
				}
			}
			$del_question = $wpdb->query("DELETE FROM ".$wpdb->wordsurvey_question." WHERE sounding_question_id = ".$cod);
			if (!$del_question) {
				$message .= '<p style="color: red;">' . sprintf(__('Error In Deleting Question \'%s\'.', 'wp-wordsurvey'), '') . '</p>';
			} else {
				$message .= '<p style="color: green;">' . sprintf(__('Success In Deleting Question \'%s\'.', 'wp-wordsurvey'), '') . '</p>';
			}
					
			$return[] = array("msg" => $message);
			
			
		} else if($subact == "e") {
			
			$question_title = addslashes(trim($title));
			
			$edit_sounding_question = $wpdb->query("UPDATE ".$wpdb->wordsurvey_question." SET sounding_question_question = '".$title."', sounding_question_type = ".(int)$ty.", sounding_question_obbl = ".(int)$obbl.", sounding_question_num = ".(int)$num." WHERE sounding_question_id = ".$cod);
			
			
			if(!$edit_sounding_question && $edit_sounding_question != 0) {		
				$message .= '<p style="color: red;">' . sprintf(__('Error In Editing Sounding Question \'%s\'.', 'wp-wordsurvey'), stripslashes($question_title)) . '</p>';
			} else {
				$message .= '<p style="color: green;">' . sprintf(__('Success In Editing Sounding Question \'%s\'.', 'wp-wordsurvey'), stripslashes($question_title)) . '</p>';
			}
					
			$del_answer = $wpdb->query("DELETE FROM ".$wpdb->wordsurvey_answer." WHERE sounding_answer_questid = ".$cod);
			
			for($j=0;$j<count($answer);$j++){
				$add_sounding_answer = $wpdb->query("INSERT INTO ".$wpdb->wordsurvey_answer." VALUES (0,".$cod.", '".addslashes($answer[$j])."', 0)");
			}
					
			$return[] = array("msg" => $message);
			
		}
		
					
	} else if($act == "s"){
		$cod = $_POST['cod'];
		$subact = $_POST['subact'];
		$title = $_POST['title'];
		
		
		if($subact == 'd'){
			//cancello
			$sounding_title = addslashes(trim($title));
			if(!is_null($cod)){
				$del_sounding = $wpdb->query("DELETE FROM ".$wpdb->wordsurvey." WHERE sounding_id = ".$cod);
				if (!$del_sounding) {
					$message .= '<p style="color: red;">' . sprintf(__('Error In Deleting Survey \'%s\'.', 'wp-wordsurvey'), stripslashes($sounding_title)) . '</p>';
				} else {
					$message .= '<p style="color: green;">' . sprintf(__('Success In Deleting Survey \'%s\'.', 'wp-wordsurvey'), stripslashes($sounding_title)) . '</p>';
				}
			} else {
				$message .= '<p style="color: red;">' . sprintf(__('Impossible Deleting Survey \'%s\'.', 'wp-wordsurvey'), '') . '</p>';
				
			}
					
			$return[] = array("msg" => $message);
			
		} else if($subact == 'e'){
		
			$expire = $_POST['expire'];
			//modifico
			$sounding_title = addslashes(trim($title));
			if(!empty($title)){
				$edit_title_sounding = $wpdb->query("UPDATE ".$wpdb->wordsurvey." SET sounding_name = '".$sounding_title."',sounding_expiration_date='".$expire."' WHERE sounding_id = ".$cod);
				if (!$edit_title_sounding) {
					$message .= '<p style="color: red;">' . sprintf(__('Error In Editing Survey \'%s\'.', 'wp-wordsurvey'), stripslashes($sounding_title)) . '</p>';
				} else {
					$message .= '<p style="color: green;">' . sprintf(__('Success In Editing Survey \'%s\'.', 'wp-wordsurvey'), stripslashes($sounding_title)) . '</p>';
				}
			}
					
			$return[] = array("msg" => $message);
		}
	} else if($act == 'a'){
		
		$quest = $_POST['quest'];
		$tit = $_POST['tit'];
		
		$del_single_answer = $wpdb->query("DELETE FROM ".$wpdb->wordsurvey_answer." WHERE sounding_answer_answers = '".addslashes($tit)."' AND  sounding_answer_questid = ".$quest);
		if (!$del_single_answer) {
			$message .= '<p style="color: red;">' . sprintf(__('Error In Deleting Answer \'%s\'.', 'wp-wordsurvey'), stripslashes($tit)) . '</p>';
		} else {
			$up_question = $wpdb->query("UPDATE ".$wpdb->wordsurvey_question." SET sounding_question_num = sounding_question_num -1 WHERE sounding_question_id = ".$quest);
			
			$message .= '<p style="color: green;">' . sprintf(__('Success In Deleting Answer \'%s\'.', 'wp-wordsurvey'), stripslashes($tit)) . '</p>';
		}
		
		$return[] = array("msg" => $message);
	}
	
	echo json_encode($return);
	exit;	
	
}

function wrdsrv_masteredit_sounding(){
	global $wpdb;
	
	$cod = $_POST['cod'];
$act = $_POST['act'];
$message = '';

$sounding_masterdel = $wpdb->get_results("SELECT sounding_id, sounding_name,sounding_expiration_date FROM ".$wpdb->wordsurvey." WHERE sounding_id = ".$cod);

if($act == "d"){
	
	foreach($sounding_masterdel as $masterdel){

		$question_del = $wpdb->get_results("SELECT sounding_question_id FROM ".$wpdb->wordsurvey_question." WHERE sounding_question_soundid = ".$masterdel->sounding_id);
		foreach($question_del as $qdel){
			
			$answer_del = $wpdb->get_results("SELECT sounding_answer_id FROM ".$wpdb->wordsurvey_answer." WHERE sounding_answer_questid = ".$qdel->sounding_question_id);
			foreach($answer_del as $adel){
				
				$answer_mesterdel = $wpdb->query("DELETE FROM ".$wpdb->wordsurvey_answer." WHERE sounding_answer_id = ".$adel->sounding_answer_id);
				if (!$answer_mesterdel) {
					$message .= '<p style="color: red;">' . sprintf(__('Error In Deleting Sounding \'%s\'.', 'wp-wordsurvey'), '') . '</p>';
				}
			}
			$question_masterdel = $wpdb->query("DELETE FROM ".$wpdb->wordsurvey_question." WHERE sounding_question_id = ".$qdel->sounding_question_id);
			if (!$question_masterdel) {
					$message .= '<p style="color: red;">' . sprintf(__('Error In Deleting Sounding \'%s\'.', 'wp-wordsurvey'), '') . '</p>';
			}
		}
		$masterdeldel = $wpdb->query("DELETE FROM ".$wpdb->wordsurvey." WHERE sounding_id = ".$masterdel->sounding_id);
		if (!$masterdeldel) {
			$message .= '<p style="color: red;">' . sprintf(__('Error In Deleting Sounding \'%s\'.', 'wp-wordsurvey'), '') . '</p>';
		} else {
			$message .= '<p style="color: green;">' . sprintf(__('Success In Deleting Sounding \'%s\'.', 'wp-wordsurvey'), '') . '</p>';
		}
	}

	$return[] = array("msg" => $message);
	
} else if($act == "e"){
	
	if($sounding_masterdel){
	foreach($sounding_masterdel as $masterdel){
	?>
	<div id="form-sounding-create">

	<input id="sounding_id" type="hidden" value="<?php echo $masterdel->sounding_id; ?>">
<div class="wrap">
	<h3><?php _e('Title Sounding', 'wp-wordsurvey'); ?></h3>

	<input id="sounding_title" type="text" value="<?php echo $masterdel->sounding_name; ?>" name="sounding_title" size="80%">
	<div id="expiredate">
		<div id="datetimepicker" class="input-append date form_datetime">
			<input id="exdate" data-format="dd-MM-yyyy hh:mm:ss" type="text"><?php echo $masterdel->sounding_expiration_date; ?></input><span class="add-on"><i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i></span>
		</div>
		<script type="text/javascript">
			jQuery(function() {
				jQuery('#datetimepicker').datetimepicker();
			});
		</script>
	</div>
	<button id="sounding_edit" type="button" class="button-warning btn btn-warning" name="sounding-edit" onclick="EditSounding(<?php echo $masterdel->sounding_id; ?>)" ><?php _e('Edit Survey', 'wp-wordsurvey'); ?></button>
	<button id="sounding_del" type="button" class="button-danger btn btn-danger" name="sounding-del" onclick="DelSounding(<?php echo $masterdel->sounding_id; ?>)" ><?php _e('Delete Survey', 'wp-wordsurvey'); ?></button>
</div>

</div>

<!-- Question -->
<?php
$question_del = $wpdb->get_results("SELECT * FROM ".$wpdb->wordsurvey_question." WHERE sounding_question_soundid = ".$masterdel->sounding_id);
foreach($question_del as $quest){
?>
<div class="panel panel-default question-create" id="form-question-create_<?php echo $quest->sounding_question_id; ?>">
	<div class="panel-heading" id="headingOne">
      <h4 class="panel-title">
        <a id="questtitlecoll_<?php echo $quest->sounding_question_id; ?>" data-toggle="collapse" data-parent="#form-question-create" href="#collapseOne_<?php echo $quest->sounding_question_id; ?>" class="accordion-toggle collapsed" aria-expanded="true">
          <?php _e('Question', 'wp-wordsurvey'); ?>
        </a>
      </h4>
    </div>
	<div id="collapseOne_<?php echo $quest->sounding_question_id; ?>" class="collapse" aria-expanded="true">
		<div class="panel-body">
		<div class="add-quest-opt" id="div_sounding_question_title_<?php echo $quest->sounding_question_id; ?>">
			<label><?php _e('Question', 'wp-wordsurvey'); ?></label>
			<input id="sounding_question_title_<?php echo $quest->sounding_question_id; ?>" type="text" value="<?php echo $quest->sounding_question_question; ?>" name="sounding_question_title" size="70%"/>
		</div>
		<fieldset>
			<label><?php _e('Type', 'wp-wordsurvey'); ?></label>
			<select id="sounding_question_type_<?php echo $quest->sounding_question_id; ?>" name="sounding_question_type" aria-invalid="false" onchange="ShowQuestion()">
					<option value=""><?php _e('select...','wp-wordsurvey')?></option>
					<option value="1"><?php _e('text','wp-wordsurvey')?></option>
					<option value="2"><?php _e('radio','wp-wordsurvey')?></option>
					<option value="3"><?php _e('selection','wp-wordsurvey')?></option>
					<option value="4"><?php _e('checkbox','wp-wordsurvey')?></option>
			</select>
		</fieldset>
		<div class="add-quest-opt" id="div_sounding_question_obbl">
			<label><?php _e('Obligatory question', 'wp-wordsurvey'); ?></label>
			<input name="sounding_question_obbl_<?php echo $quest->sounding_question_id; ?>" type="checkbox" value="si" />
		</div>
		<div class="add-quest-opt" id="div_sounding_question_num_<?php echo $quest->sounding_question_id; ?>">
			<label><?php _e('Number of replies', 'wp-wordsurvey'); ?></label>
			<input id="sounding_question_num_<?php echo $quest->sounding_question_id; ?>" type="text" value="<?php echo $quest->sounding_question_num; ?>" name="sounding_question_num" size="5" onchange="ShowAnswer()" onpaste="this.onchange();" />

<?php			
			$answer_del = $wpdb->get_results("SELECT sounding_answer_id FROM ".$wpdb->wordsurvey_answer." WHERE sounding_answer_questid = ".$quest->sounding_question_id);
			foreach($answer_del as $ans){
?>
			
			<div class="answer-create" id="form-answer-create_<?php echo $quest->sounding_question_id; ?>">
				<input id="sounding_quest_id" type="hidden" value="<?php echo $quest->sounding_question_id; ?>">
				<div id="answer_1"><label><?php _e("Reply n. 1",'wp-wordsurvey')?></label><input id="sounding_answer" type="text" value="" name="sounding_answer_<?php echo $quest->sounding_question_id; ?>" size="65%"/></div>
			</div>
			<?php
			}
			?>
		</div>

		</div>
	</div>
</div>
<button id="masteredit_questionadd_button" type="button" class="button-primary btn btn-primary" name="masteredit_questionadd_button" onclick="MasterAddQuestion()" style="margin-top:3px;"><?php _e('Add Question','wp-wordsurvey')?></button>
	
	
<?php
}//fine foreach question
	}// fine each masterdel
	} //fine if masterdel
}


	echo json_encode($return);
	exit;
	
	
}


function wrdsrv_mastersave_question(){
	
	global $current_user,$wpdb;
	$user = $current_user->user_login;
	$id = $current_user->ID;
	
	$message = '';
	$idtitolo = 0;
	$return = array();
	
	if(isset($_POST['idsounding'])){
		$idsounding = $_POST['idsounding'];
		$ty = $_POST['ty'];
		$title = $_POST['title'];
		$obbl = $_POST['obbl'];
		$num = $_POST['num'];
		$answer = $_POST['answer'];
		$act = $_POST['act'];
	} else {
		$expire = $_POST['expire'];
		$act = $_POST['act'];
		$sound_tit = $_POST['sound_tit'];
	}
	
	
	if($act == "q"){
	
			$question_title = addslashes(trim($title));
			$t = $idsounding. " - " .$ty ." - ".$title." - ".$obbl." - ".$num;
			
			$add_sounding_question = $wpdb->query("INSERT INTO ".$wpdb->wordsurvey_question." VALUES (0,'".$title."', '".$ty."', ".(int)$obbl.", ".(int)$num.", ".(int)$idsounding.",0, now())");
			
				if (!$add_sounding_question) {
				
					$message .= '<p style="color: red;">' . sprintf(__('Error In Adding Survey Question \'%s\'.', 'wp-wordsurvey'), stripslashes($question_title)) . '</p>';
				} else {
					$message .= '<p style="color: green;">' . sprintf(__('Success In Adding Survey Question \'%s\'.', 'wp-wordsurvey'), stripslashes($question_title)) . '</p>';
				}
					
				$id_sounding_question = $wpdb->get_results("SELECT sounding_question_id FROM ".$wpdb->wordsurvey_question." WHERE sounding_question_soundid = ".(int)$idsounding." ORDER BY sounding_question_ts DESC LIMIT 0,1");
				if($id_sounding_question) {
					foreach($id_sounding_question as $id_quest) {
							$id_question = $id_quest->sounding_question_id;
					}
				}
			
			for($j=0;$j<count($answer);$j++){
				$add_sounding_answer = $wpdb->query("INSERT INTO ".$wpdb->wordsurvey_answer." VALUES (0,".$id_question.", '".$answer[$j]."', 0)");
			}
					
			$return[] = array("idquest" => $id_question, "msg" => $message);
					
	} else if($act == "s"){
						
			$sounding_title = addslashes(trim($sound_tit));
			if(!empty($sound_tit)){
				$add_title_sounding = $wpdb->query("INSERT INTO ".$wpdb->wordsurvey." VALUES (0, '".$sounding_title."', '".$expire."', '".$user."', ".$id.", now())");
				if (!$add_title_sounding) {
					$message .= '<p style="color: red;">' . sprintf(__('Error In Adding Survey \'%s\'.', 'wp-wordsurvey'), stripslashes($sounding_title)) . '</p>';
				} else {
					$message .= '<p style="color: green;">' . sprintf(__('Success In Adding Survey \'%s\'.', 'wp-wordsurvey'), stripslashes($sounding_title)) . '</p>';
				}
			} else {
				$message .= '<p style="color: red;">' . sprintf(__('You can not place an untitled survey', 'wp-wordsurvey'),stripslashes($sounding_title)).'</p>';
			}
			
			$id_title_sounding = $wpdb->get_results("SELECT sounding_id FROM $wpdb->wordsurvey WHERE sounding_userid = $id ORDER BY sounding_ts DESC LIMIT 0,1");
			if($id_title_sounding) {
				foreach($id_title_sounding as $id_title) {
						$idtitolo = $id_title->sounding_id;
				}
			}
			
			$return[] = array("idtitolo" => $idtitolo, "msg" => $message);
	}
	
	echo json_encode($return);
	exit;
}
?>