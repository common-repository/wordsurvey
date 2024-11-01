<?php
if(!defined('ABSPATH')){
	exit;	
}

function wrdsrv_preview(){
global $wpdb;

$idsoun = $_POST["idsoun"];
$pr = '';
$sounding_preview = $wpdb->get_results("SELECT sounding_id, sounding_name FROM ".$wpdb->wordsurvey." WHERE sounding_id = ".(int)$idsoun);

if($sounding_preview) {

	foreach($sounding_preview as $sprev) {
		$pr .= '<div width="100%" class="titleprev"><h3>'.$sprev->sounding_name.'</h3></div>';
		$sounding_question_preview = $wpdb->get_results("SELECT sounding_question_id, sounding_question_question, sounding_question_type, sounding_question_num,sounding_question_obbl FROM ".$wpdb->wordsurvey_question." WHERE sounding_question_soundid = ".(int)$sprev->sounding_id);
		
		if($sounding_question_preview){
		
					foreach($sounding_question_preview as $sqprev){
						
						if($sqprev->sounding_question_obbl == 1)
							$ast = '*';
						else
							$ast = '';
						
						$sounding_answer_preview = $wpdb->get_results("SELECT sounding_answer_id, sounding_answer_answers FROM ".$wpdb->wordsurvey_answer." WHERE sounding_answer_questid = ".(int)$sqprev->sounding_question_id);
						$pr .= "<div>";
						if($sqprev->sounding_question_type == "2"){
								$pr .= "<fieldset>";
									$pr .= "<legend>".$ast." ".$sqprev->sounding_question_question."</legend>";
									
										foreach($sounding_answer_preview as $sqaprev){
											
											$pr .= '<input type="radio" name="'.$sqprev->sounding_question_id.'" value="'.$sqaprev->sounding_answer_id.'" /> '.$sqaprev->sounding_answer_answers.'<br />';
											
										}
								$pr .= "</fieldset>";
						} else if($sqprev->sounding_question_type == "4"){
								$pr .= "<fieldset>";
									$pr .= "<legend>".$ast." ".$sqprev->sounding_question_question."</legend>";
										
										foreach($sounding_answer_preview as $sqaprev){
											
											$pr .= '<input type="checkbox" name="'.$sqaprev->sounding_answer_id.'" value="'.$sqaprev->sounding_answer_id.'" /> '.$sqaprev->sounding_answer_answers.'<br />';
											
										}
								$pr .= "</fieldset>";
						} else if($sqprev->sounding_question_type == "1") {
							
							$pr .= "<legend>".$ast." ".$sqprev->sounding_question_question."</legend>";
							foreach($sounding_answer_preview as $sqaprev){
								$pr .= '<textarea type="text" name="'.$sqaprev->sounding_answer_id.'" value="" ></textarea>';
							}				
							
						} else if($sqprev->sounding_question_type == "3"){
							
							$pr .= "<fieldset>";
									$pr .= "<legend>".$ast." ".$sqprev->sounding_question_question."</legend>";
									$pr .= "<select>";
											
										foreach($sounding_answer_preview as $sqaprev){
											
											$pr .= '<option value="'.$sqaprev->sounding_answer_id.'" >'.$sqaprev->sounding_answer_answers.'</option>';
											
										}
										$pr .= "</select>";
								$pr .= "</fieldset>";
							
							
						}
						$pr .= "</div>";
						
					} //ciclo question survey
		}

	}//ciclo survey
	$return[] = array("html" => $pr);
}

	echo json_encode($return);
	exit;
}
?>