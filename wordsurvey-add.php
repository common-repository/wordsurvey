<?php

if(!defined('ABSPATH')){
	exit;	
}

if(!current_user_can('manage_options')) {
	die('Access Denied');
}

$base_name = plugin_basename(plugins_url('wordsurvey-manager.php',__FILE__));
$base_page = 'admin.php?page='.$base_name;

global $current_user,$wpdb;

$message = "";
$idtitolo = 0;

//get_currentuserinfo();
$user = $current_user->user_login;
$id = $current_user->ID;


$exdate = __('This is the date of term survey, after which it is no longer active. It is not mandatory.', 'wp-wordsurvey');
$create = __('Click the button to save title and date and start to insert the question', 'wp-wordsurvey');
$savequest = __('Click this button after you compile the question to save it.', 'wp-wordsurvey');

$numsurv = $wpdb->get_row("SELECT count(sounding_id) as c FROM ".$wpdb->wordsurvey);
?>
<div class="banner_pro" style="margin-left:-40px !important;"><a href="https://www.sersis.com/contatti/" target="_blank"><img src="<?php echo plugins_url( 'images/banner_pro.jpg', __FILE__ ); ?>"></a></div>
<?php
if($numsurv->c == get_option('wrdsrv_surveylimit')){?>
	<div class="title"><h1><?php _e('Add Survey', 'wp-wordsurvey'); ?></h1></div>
	<div style="background-color: #fff;border: 2px solid #fcdec3;border-radius: 3px;width: 100%;padding:1%;"><?php _e('You can not put more than one survey. <a href="http://www.sersis.com/wordsurvey/" target="_blank">BUY THE PRO VERSION<a>.', 'wp-wordsurvey'); ?></div>
	<?php
} else {

	echo '<!-- Last Action --><div id="message" class="alert"></div>';
	?>
	<div class="title"><h1><?php _e('Add Survey', 'wp-wordsurvey'); ?><a class="page_new_sounding" href="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>"><?php _e('Add New','wp-wordsurvey')?></a></h1></div>
	<div class="panel-group divleft" id="insertsurvey">
	<div id="form-sounding-create">
		<input id="sounding_id" type="hidden" value="">
		<div class="wrap">
			<h3><?php _e('Title Survey', 'wp-wordsurvey'); ?></h3>
			<!-- Title -->
			<input id="sounding_title" type="text" value="" name="sounding_title" size="80%">
			<div id="expiredate">
				<label><?php _e('Expire Date', 'wp-wordsurvey'); ?></label><a class="add-on infosgn" data-placement="right" data-content="<?php echo $exdate; ?>"><i class="glyphicon glyphicon-question-sign"></i></a>
				<div id="datetimepicker" class="input-append date form_datetime">
					<input id="exdate" data-format="dd-MM-yyyy hh:mm:ss" type="text"></input><span class="add-on"><i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i></span>
				</div>
				<script type="text/javascript">
					jQuery(function() {
						jQuery('#datetimepicker').datetimepicker();
					});
				</script>
			</div>
			<button id="sounding_button" type="button" class="button-primary btn btn-primary" name="sounding-button" onclick="WRDsrvSaveSoundingAjax()"><?php _e('Create Survey','wp-wordsurvey')?></button><a id="createtxt" class="add-on infosgn" data-placement="right" data-content="<?php echo $create; ?>"><i class="glyphicon glyphicon-question-sign"></i></a>
		</div>
	</div>

	<!-- Question -->
	<div class="panel panel-default question-create" id="form-question-create"  style="display:none;">
		<div class="panel-heading" id="headingOne">
		  <h4 class="panel-title">
			<a id="questtitlecoll" data-toggle="collapse" data-parent="#form-question-create" href="#collapseOne" class="accordion-toggle" aria-expanded="true">
			  <?php _e('Question', 'wp-wordsurvey'); ?>
			</a>
		  </h4>
		</div>
		<div id="collapseOne" class="collapse in" aria-expanded="true">
			<div class="panel-body">
			<div class="add-quest-opt" id="div_sounding_question_title">
				<label><?php _e('Question', 'wp-wordsurvey'); ?></label>
				<input id="sounding_question_title" type="text" value="" name="sounding_question_title" size="70%"/>
			</div>
			<fieldset>
				<label><?php _e('Type', 'wp-sounding'); ?></label>
				<select id="sounding_question_type" name="sounding_question_type" aria-invalid="false" onchange="ShowQuestion()">
						<option value=""><?php _e('select...','wp-wordsurvey')?></option>
						<option value="1"><?php _e('text','wp-wordsurvey')?></option>
						<option value="2"><?php _e('radio','wp-wordsurvey')?></option>
						<option value="3"><?php _e('selection','wp-wordsurvey')?></option>
						<option value="4"><?php _e('checkbox','wp-wordsurvey')?></option>
				</select>
			</fieldset>
			<div class="add-quest-opt" id="div_sounding_question_obbl">
				<label><?php _e('Obligatory question', 'wp-wordsurvey'); ?></label>
				<input id="sounding_question_obbl" name="sounding_question_obbl" type="checkbox" value="si" />
			</div>
			<div class="add-quest-opt" id="div_sounding_question_num" style="display:none;">
				<label><?php _e('Number of replies', 'wp-wordsurvey'); ?></label>
				<input id="sounding_question_num" type="text" value="1" name="sounding_question_num" size="5" onchange="ShowAnswer()" onpaste="this.onchange();" />
				<div class="answer-create" id="form-answer-create">
					<input id="sounding_quest_id" type="hidden" value="">
					<div id="answer_1"><label><?php _e("Reply n. 1",'wp-wordsurvey')?></label><a class="buttondel" id="buttondel_1" type="button" onclick="WRDsrvDelAnswer(1)" style="display:none;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a><input id="sounding_answer" type="text" value="" name="sounding_answer" size="65%"/></div>
					<div id="div_answeradd"><button id="sounding_answeradd_button" type="button" class="button-primary btn btn-primary" name="sounding_answeradd_button" onclick="AddAnswer()"><?php _e('Add Answer','wp-wordsurvey')?></button></div>
				</div>				
			</div>
			<button id="sounding_question_button" type="button" class="button-primary btn btn-primary" name="sounding-question-button" onclick="WRDsrvSaveQuestionAjax()" ><?php _e('Save Question','wp-wordsurvey')?></button><a id="infosgnquest" class="add-on infosgn" data-placement="right" data-content="<?php echo $savequest; ?>"><i class="glyphicon glyphicon-question-sign"></i></a>
			</div>
		</div>
	</div>
	<button id="sounding-questionadd-button" type="button" class="button-primary btn btn-primary" name="sounding-questionadd-button" onclick="AddQuestion()" style="display:none; margin-top:3px;"><?php _e('Add Question','wp-wordsurvey')?></button>
	</div>
	<div class="divleft" id="previewsurvey">
	<div class="previewsurv"><h3><?php _e('Preview', 'wp-wordsurvey'); ?></h3></div>
	<div id="bodyprevsurvey"></div>
	</div>
<?php
}
?>