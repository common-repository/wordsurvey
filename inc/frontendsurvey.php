<?php
if(!defined('ABSPATH')){
	exit;	
}

function WRDsrv_printSurvey($cod){
global $wpdb;

$req = '';
$bodyshortcode = '';

$sounding_preview = $wpdb->get_results("SELECT sounding_id, sounding_name,sounding_expiration_date FROM ".$wpdb->wordsurvey." WHERE sounding_id = ".$cod);

if($sounding_preview) {

	foreach($sounding_preview as $sprev) {
		if(date('d-m-Y H:i:s') < $sprev->sounding_expiration_date or $sprev->sounding_expiration_date == ''){
		
			$bodyshortcode .= '<div id="sondaggio_'.$sprev->sounding_id.'" action="post">';
			$bodyshortcode .= '<div width="100%" class="titleprev"><h2>'.$sprev->sounding_name.'</h2>';
			$bodyshortcode .= '<input type="hidden" id="surveyid" value="'.$sprev->sounding_id.'"></div>';
			$bodyshortcode .= '<div class="frontend">';
			$sounding_question_preview = $wpdb->get_results("SELECT sounding_question_id, sounding_question_question, sounding_question_type, sounding_question_num, sounding_question_obbl FROM ".$wpdb->wordsurvey_question." WHERE sounding_question_soundid = ".(int)$sprev->sounding_id);
			
			if($sounding_question_preview){
			
						foreach($sounding_question_preview as $sqprev){
							if($sqprev->sounding_question_obbl == 1)
								$ast = '*';
							else
								$ast = '';
							
							
							$sounding_answer_preview = $wpdb->get_results("SELECT sounding_answer_id, sounding_answer_answers FROM ".$wpdb->wordsurvey_answer." WHERE sounding_answer_questid = ".(int)$sqprev->sounding_question_id);
							if($sqprev->sounding_question_obbl == 1){
								$req = 'required="required"';
								$cls = 'class="req"';
							} else {
								$req = '';
								$cls = 'class="noreq"';
							}
							$bodyshortcode .= "<div id='question_".$sqprev->sounding_question_id."'>";
							if($sqprev->sounding_question_type == "2"){
									//radio
									$bodyshortcode .= '<fieldset>';
										$bodyshortcode .= "<legend>".$ast." ".$sqprev->sounding_question_question."</legend>";
										
											foreach($sounding_answer_preview as $sqaprev){
												
												$bodyshortcode .= '<input type="radio" '.$cls.' '.$req.' name="'.$sqprev->sounding_question_id.'" value="'.$sqprev->sounding_question_id.'_'.$sqaprev->sounding_answer_id.'" /> '.$sqaprev->sounding_answer_answers.'<br />';
												
											}
									$bodyshortcode .= "</fieldset>";
							} else if($sqprev->sounding_question_type == "4"){
									//checkbox
									$bodyshortcode .= '<fieldset>';
										$bodyshortcode .= "<legend>".$ast." ".$sqprev->sounding_question_question."</legend>";
											
											foreach($sounding_answer_preview as $sqaprev){
												
												$bodyshortcode .= '<input id="'.$sqprev->sounding_question_id.'_'.$sqaprev->sounding_answer_id.'" type="checkbox" '.$req.' '.$cls.' name="'.$sqprev->sounding_question_id.'" value="'.$sqprev->sounding_question_id.'_'.$sqaprev->sounding_answer_id.'" /> '.$sqaprev->sounding_answer_answers.'<br />';
												
											}
									$bodyshortcode .= "</fieldset>";
							} else if($sqprev->sounding_question_type == "1") {
								
								$bodyshortcode .= "<legend>".$ast." ".$sqprev->sounding_question_question."</legend>";
								foreach($sounding_answer_preview as $sqaprev){
									$bodyshortcode .= '<textarea '.$cls.' type="text" '.$req.' name="'.$sqprev->sounding_question_id.'_'.$sqaprev->sounding_answer_id.'" value="" ></textarea>';
								}				
								
							} else if($sqprev->sounding_question_type == "3"){
								
								$bodyshortcode .= "<fieldset>";
										$bodyshortcode .= "<legend>".$ast." ".$sqprev->sounding_question_question."</legend>";
										$bodyshortcode .= '<select '.$req.' id="selquest_'.$sqprev->sounding_question_id.'" >';
												$bodyshortcode .= '<option '.$cls.' value="">...select</option>';
											foreach($sounding_answer_preview as $sqaprev){
												
												$bodyshortcode .= '<option '.$cls.' value="'.$sqprev->sounding_question_id.'_'.$sqaprev->sounding_answer_id.'" >'.$sqaprev->sounding_answer_answers.'</option>';
												
											}
											$bodyshortcode .= "</select>";
									$bodyshortcode .= "</fieldset>";
								
								
							}
							$bodyshortcode .= "</div>";
							$bodyshortcode .= '<div class="alert alert-danger" id="questionalert_'.$sqprev->sounding_question_id.'" style="display: none;">Question required!</div>';
							
						} //ciclo question survey
			}
			$bodyshortcode .= '<div id="votadiv"><button type="button" id="vota" onclick="WRDsrvVoteSurvey()">Vote</button></div>';
			$bodyshortcode .= '</div>';
			$bodyshortcode .= '</div>';
	
	} else {
		$mess = sprintf(__('The survey is closed.', 'wp-wordsurvey'), '');
		
		$bodyshortcode .= '<div class="alert alert-info">'. $mess.'</div>';

	}
	}
}
return $bodyshortcode;

}


?>