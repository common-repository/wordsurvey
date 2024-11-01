<?php
if(!defined('ABSPATH')){
	exit;	
}

if(!current_user_can('manage_options')) {
	die('Access Denied');
}

?>
<div class="banner_pro" style="margin-left:-40px !important;"><a href="https://www.sersis.com/contatti/" target="_blank"><img src="<?php echo plugins_url( 'images/banner_pro.jpg', __FILE__ ); ?>"></a></div>
<?php
if(isset($_REQUEST["act"]) && $_REQUEST["act"] == "e")
{
	$cod = $_REQUEST["cod"];
	$sounding_masterdel = $wpdb->get_results("SELECT sounding_id, sounding_name,sounding_expiration_date FROM ".$wpdb->wordsurvey." WHERE sounding_id = ".$cod);
	
	$exdatetxt = __('This is the date of term survey, after which it is no longer active. It is not mandatory.', 'wp-wordsurvey');
	$addans = __('After filling in the answer, remember to click the button "Edit Question" to save it.', 'wp-wordsurvey');
	
?>
<div class="title"><h1><?php _e('Edit Survey', 'wp-wordsurvey'); ?></h1></div>
<div class="sounding_edit">

<?php
	if($sounding_masterdel){
		foreach($sounding_masterdel as $masterdel){
		?>	
			<div id="form-sounding-create">
				<input id="sounding_id" type="hidden" value="<?php echo $masterdel->sounding_id; ?>">
				<div class="wrap">
					<h3><?php _e('Title Survey', 'wp-wordsurvey'); ?></h3>

					<input id="sounding_title" type="text" value="<?php echo $masterdel->sounding_name; ?>" name="sounding_title" size="80%">
					<div id="expiredate">
					<label><?php _e('Expire Date', 'wp-wordsurvey'); ?></label><a class="add-on infosgn" data-placement="right" data-content="<?php echo $exdatetxt; ?>"><i class="glyphicon glyphicon-question-sign"></i></a>
						<div id="datetimepicker" class="input-append date form_datetime">
							<input id="exdate" data-format="dd-MM-yyyy hh:mm:ss" type="text" value="<?php echo $masterdel->sounding_expiration_date; ?>"></input><span class="add-on"><i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i></span>
						</div>
						<script type="text/javascript">
							jQuery(function() {
								jQuery('#datetimepicker').datetimepicker({});
							});
						</script>
					</div>
					<button id="sounding_edit" type="button" class="button-warning btn btn-warning" name="sounding-edit" onclick="WRDsrvEditSounding(<?php echo $masterdel->sounding_id; ?>)" ><?php _e('Edit Survey','wp-wordsurvey')?></button>
					<button id="sounding_del" type="button" class="button-danger btn btn-danger" name="sounding-del" onclick="WRDsrvMasterDelete(<?php echo $masterdel->sounding_id; ?>,'edit')" ><?php _e('Delete Survey','wp-wordsurvey')?></button>
				</div>
			</div>

	<!-- Question -->
	<?php
		$question_del = $wpdb->get_results("SELECT sounding_question_id,sounding_question_question,sounding_question_num,sounding_question_obbl,sounding_question_type FROM ".$wpdb->wordsurvey_question." WHERE sounding_question_soundid = ".$masterdel->sounding_id);

			foreach($question_del as $quest)
			{
			$sel = $quest->sounding_question_type;

	?>
			<div class="panel panel-default question-create" id="form-question-create_<?php echo $quest->sounding_question_id; ?>">
				<div class="panel-heading" id="headingOne">
						<h4 class="panel-title">
							<a id="questtitlecoll_<?php echo $quest->sounding_question_id; ?>" data-toggle="collapse" data-parent="#form-question-create" href="#collapseOne_<?php echo $quest->sounding_question_id; ?>" class="accordion-toggle collapsed" aria-expanded="true">
								<?php echo $quest->sounding_question_question; ?>
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
						<select id="sounding_question_type_<?php echo $quest->sounding_question_id; ?>" name="sounding_question_type" aria-invalid="false" onchange="ShowQuestionWithCod(<?php echo $quest->sounding_question_id; ?>)">
								<option value=""><?php _e('select...','wp-wordsurvey')?></option>
								<option value="1" <?php if($sel == 1) : ?> selected="selected" <?php endif; ?> ><?php _e('text','wp-wordsurvey')?></option>
								<option value="2" <?php if($sel == 2) : ?> selected="selected" <?php endif; ?> ><?php _e('radio','wp-wordsurvey')?></option>
								<option value="3" <?php if($sel == 3) : ?> selected="selected" <?php endif; ?> ><?php _e('selection','wp-wordsurvey')?></option>
								<option value="4" <?php if($sel == 4) : ?> selected="selected" <?php endif; ?> ><?php _e('checkbox','wp-wordsurvey')?></option>
						</select>
					</fieldset>
					<div class="add-quest-opt" id="div_sounding_question_obbl">
						<label><?php _e('Obligatory question', 'wp-wordsurvey'); ?></label>
						<input id="sounding_question_obbl_<?php echo $quest->sounding_question_id; ?>" name="sounding_question_obbl_<?php echo $quest->sounding_question_id; ?>" type="checkbox" value="si" <?php if($quest->sounding_question_obbl == 1) : ?>checked="checked" <?php endif; ?> />
					</div>
					<div class="add-quest-opt" id="div_sounding_question_num_<?php echo $quest->sounding_question_id; ?>">
						<label><?php _e('Number of replies', 'wp-wordsurvey'); ?></label>
						<input id="sounding_question_num_<?php echo $quest->sounding_question_id; ?>" type="text" value="<?php echo $quest->sounding_question_num; ?>" name="sounding_question_num" size="5" onchange="ShowAnswerWithCod(<?php echo $quest->sounding_question_id; ?>)" onpaste="this.onchange();" />

			<?php			
						$answer_del = $wpdb->get_results("SELECT * FROM ".$wpdb->wordsurvey_answer." WHERE sounding_answer_questid = ".$quest->sounding_question_id);
						$i=1;
						?>
						<div class="answer-create" id="form-answer-create_<?php echo $quest->sounding_question_id; ?>">
						<input id="sounding_quest_id_<?php echo $quest->sounding_question_id; ?>" type="hidden" value="<?php echo $quest->sounding_question_id; ?>">
						<?php
						foreach($answer_del as $ans){
							
							if($quest->sounding_question_type == 1){
								
								?>
										<div id="answer_1"><label><?php _e("Reply",'wp-wordsurvey')?></label><br><textarea type="text" name="sounding_answer_<?php echo $quest->sounding_question_id; ?>" rows="3" cols="100"><?php echo $ans->sounding_answer_answers; ?></textarea></div>
								<?php
								
							} else {
			?>
								<div id="answer_<?php echo $i; ?>"><label><?php _e('Reply n. ','wp-wordsurvey')?><?php echo $i; ?></label><a class="buttondel" id="buttondel_<?php echo $quest->sounding_question_id; ?>_<?php echo $i; ?>" type="button" onclick="WRDsrvDelAnswerWithCod(<?php echo $i; ?>,<?php echo $quest->sounding_question_id; ?>)"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a> <input id="sounding_answer" type="text" value="<?php echo $ans->sounding_answer_answers; ?>" name="sounding_answer_<?php echo $quest->sounding_question_id; ?>" size="65%"/></div>
						<?php
							}
							$i++;
						}
						?>
						<div id="div_answeradd_<?php echo $quest->sounding_question_id; ?>"><button id="sounding_answeradd_button_<?php echo $quest->sounding_question_id; ?>_<?php echo $i; ?>" type="button" class="button-primary btn btn-primary" name="sounding_answeradd_button" onclick="AddAnswerWithCod(<?php echo $quest->sounding_question_id; ?>)"><?php _e('Add Answer','wp-wordsurvey')?></button><a class="add-on infosgn" data-placement="right" data-content="<?php echo $addans; ?>"><i class="glyphicon glyphicon-question-sign"></i></a></div>
						</div>
					</div>
						<button id="sounding_question_edit_<?php echo $quest->sounding_question_id; ?>" type="button" class="button-warning btn btn-warning" name="sounding-edit" onclick="WRDsrvEditQuestion(<?php echo $quest->sounding_question_id; ?>)" ><?php _e('Edit Question','wp-wordsurvey')?></button>
						<button id="sounding_question_del_<?php echo $quest->sounding_question_id; ?>" type="button" class="button-danger btn btn-danger" name="sounding-del" onclick="WRDsrvDelQuestion(<?php echo $quest->sounding_question_id; ?>)" ><?php _e('Delete Question','wp-wordsurvey')?></button>
					</div>
				</div>
			</div>
				
			<?php
			}//fine foreach question
			?>
			<button id="masteredit_questionadd_button" type="button" class="button-primary btn btn-primary" name="masteredit_questionadd_button" onclick="MasterAddQuestion()" style="margin-top:3px;"><?php _e('Add Question','wp-wordsurvey')?></button>
			<?php
		}// fine each masterdel
	} //fine if masterdel
			
	?>
</div>

<?php
	
} else if(isset($_REQUEST["act"]) && $_REQUEST["act"] == "st"){
	
	$cod = $_REQUEST["cod"];
	
	require_once(plugin_dir_path( __FILE__ ).'lib/SVGGraph/SVGGraph.php');
	include(plugin_dir_path( __FILE__ ).'inc/soundingstats.php');
	
} else {

	//Manager
	$base_name = plugin_basename(plugins_url('wordsurvey-manager.php',__FILE__));
	$base_page = 'admin.php?page='.$base_name;

	echo '<!-- Last Action --><div id="message" class="alert"></div>';
	?>
	<div class="title"><h1><?php _e('Manage Survey', 'wp-wordsurvey'); ?></h1></div>
	<div id="sounding_list" class="sounding_page">
	<table class="list">
		<thead>
			<th><?php _e('Survey', 'wp-wordsurvey');?></th><th><?php _e('Expire Date', 'wp-sounding');?></th><th><?php _e('Status', 'wp-sounding');?></th><th><?php _e('Shortcode', 'wp-sounding');?></th><th colspan="3"><?php _e('Action', 'wp-sounding');?></th>
		</thead>
		<tbody>

			<?php
			$sounding_list = $wpdb->get_results("SELECT sounding_id, sounding_name,sounding_expiration_date FROM ".$wpdb->wordsurvey);
			$i = 0;
			$cls = '';
			foreach($sounding_list as $sounding){
				if($i % 2 == 0)
					$cls = "white";
				else
					$cls = "grey";
				?>
					<tr id="<?php echo $sounding->sounding_id; ?>" class="<?php echo $cls; ?>">
						<td><?php echo $sounding->sounding_name; ?></td>
						<td><?php echo $sounding->sounding_expiration_date; ?></td>
						<td>
							<?php
							if($sounding->sounding_expiration_date < date('d-m-Y H:i:s') && $sounding->sounding_expiration_date != '')
								_e('Close', 'wp-wordsurvey');
							else
								_e('Open', 'wp-wordsurvey');
							?>
						</td>
						<td><input type="text" value="<?php echo "[wordsurvey id=".$sounding->sounding_id."]"; ?>" readonly="readonly" /></td>
						<td><a href="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>&act=st&cod=<?php echo $sounding->sounding_id; ?>"><span class="glyphicon glyphicon-stats"></span></a></td>
						<td><a href="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>&act=e&cod=<?php echo $sounding->sounding_id; ?>"><span class="glyphicon glyphicon-pencil"></span></a></td>
						<td><a id="delete_<?php echo $sounding->sounding_id; ?>" href="javascript:WRDsrvMasterDelete(<?php echo $sounding->sounding_id; ?>,'main')"><span class="glyphicon glyphicon-remove"></span></a></td>
					</tr>
				<?php
				$i++;
			}
			?>
		<tbody>
	</table>
	</div>
<?php
}
?>