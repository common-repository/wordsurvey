<?php
if(!defined('ABSPATH')){
	exit;	
}

?>

<div class="title"><h1><?php _e('Stats Survey', 'wp-wordsurvey'); ?></h1><a class="document" href="<?php echo plugins_url('../doc/WordSurvey_Plugin_gratis.pdf',__FILE__);?>" target="_blank"><?php _e('Documentazione', 'wp-wordsurvey'); ?></a></div>
<div class="sounding_edit">
	<?php

	$sounding_stats = $wpdb->get_results("SELECT sounding_id,sounding_name,sounding_expiration_date FROM ".$wpdb->wordsurvey." WHERE sounding_id = ".$cod);
	foreach($sounding_stats as $sound) {
		echo '<div id="sondaggio_'.$sound->sounding_id.'" style="float:left;width:100%;">';
		echo '<div style="padding-left: 1%;border-bottom: 2px solid #FCDEC3 !important;float:left;width:100%;"><div id="soundtitle" style="width:60%;float:left;"><h1>'.$sound->sounding_name.'</h1></div>'.
		'<div id="expbutton" style="width:30%;float:left;padding:1%;"><a href="'.plugins_url('piegraphstat.php',__FILE__).'/?s='.$sound->sounding_id.'" class="btn btn-primary" id="pdfexport" style="margin-right:1%;" target="_blank">Esporta PDF</a>'.
		''. __("For exports to CSV and XLS <a href=\"https://www.sersis.com/contatti/\" target=\"_blank\">BUY THE PRO VERSION</a>.", "wp-wordsurvey").'</div></div>';
		echo '<div class="stats">';
		$sounding_question_stats = $wpdb->get_results("SELECT sounding_question_id, sounding_question_question, sounding_question_type, sounding_question_num,sounding_question_totvote FROM ".$wpdb->wordsurvey_question." WHERE sounding_question_soundid = ".(int)$sound->sounding_id);
		
		if($sounding_question_stats){
		
			foreach($sounding_question_stats as $qstats){
					$col = array('#00A0E0','#00E100','#D2BFFF','#FF9F71','#B1B1D2','#FF9FCF','#82C0FF','#FFFF35','#00C196','#C082FF');
					$bar = array();
					$arr = array();
					$colours = array();
					$val = array();
					$c2 = array();
				?>
				<div style="width:100%;">
					<table border="0" style="width:80%;"><tr><td colspan="2" style="padding-left: 15px;"><h3><?php echo $qstats->sounding_question_question; ?></h3></td></tr>
						<tr><td style="width:50%;">
							<?php
							$sounding_answer_stats = $wpdb->get_results("SELECT sounding_answer_id, sounding_answer_answers,sounding_answer_votes FROM ".$wpdb->wordsurvey_answer." WHERE sounding_answer_questid = ".(int)$qstats->sounding_question_id);
							$i = 0;
							?>
							<ul>
							<?php
							foreach($sounding_answer_stats as $astats){
								if($qstats->sounding_question_totvote != 0){
									$percent = (($astats->sounding_answer_votes*100)/$qstats->sounding_question_totvote);
								} else {
									$percent = 0;
								}
								$n = array_rand($col, 1);
								$bar[$astats->sounding_answer_id] = array($col[$i] => $percent);
								?>
								<li>
									<?php
									if($qstats->sounding_question_type != 1){
										echo '<div style="float:left;margin-right:1%;width:20px;height:20px;background-color:'.$col[$i].'; ?>;"> </div>';
									}
									
									echo round($percent,2).'% ('.$astats->sounding_answer_votes.' of '.$qstats->sounding_question_totvote.' votes) '.$astats->sounding_answer_answers;

									if($qstats->sounding_question_type == 1){
										echo '<br>';
										$sounding_anstext_stats = $wpdb->get_results("SELECT sounding_vote_text FROM ".$wpdb->wordsurvey_vote." WHERE sounding_vote_answerid = ".(int)$astats->sounding_answer_id);
										foreach($sounding_anstext_stats as $atstats){
											?>
											<p style="background-color:#F8F8F8;padding:15px;"><?php echo '" '.$atstats->sounding_vote_text. ' "'; ?></p>
											<?php
										}
									}
									?>
								</li>
								<?php
									$val[$astats->sounding_answer_answers] = round($percent,2);
									$colours[] = $col[$i];
									
									$p = round($percent,2);
									if((int)$p != 0){
										$c2[] = $col[$i];
									}
								
									$arr[] = array("value" => round($percent,2), "color" => $col[$i], "highlight" => $col[$i], "label" => $astats->sounding_answer_answers ); 
									if($i > 9)
										$i = 0;
									else
										$i++;
								}

								?>
								</ul>
								</td>
								<td style="width:50%;">
								<?php
								if($qstats->sounding_question_type != 1){
								$settings = array(
									'back_colour' => '#fff',   'stroke_colour' => '#fff',
									'back_stroke_width' => 0,  'back_stroke_colour' => '#eee',
									'stroke_width' => 2,		'show_label_percent' => true,
									'pad_right' => 20,         'pad_left' => 20,
									'show_labels' => true,     'label_font' => 'Georgia',
									'label_font_size' => '15',	'label_colour' => '#000',
									'sort' => false
								);
								$graph = new SVGGraph(300, 350,$settings);
								$graph->colours = $c2;
								$graph->Values($val);
								$graph->Render('PieGraph',false,false);

								}
								?>
								</td>
						</tr>
					</table>
				</div>
				<hr>
				<?php

			}
		}
		echo '</div>';
		echo '</div>';

	}//ciclo survey

	?>

</div>