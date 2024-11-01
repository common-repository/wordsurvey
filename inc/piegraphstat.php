<?php
require_once('../lib/SVGGraph/SVGGraph.php');
require_once('../lib/tcpdf/tcpdf.php');
include("../../../../wp-config.php");
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD,DB_NAME);

global $wpdb;

$s = $_REQUEST["s"];


class MYPDF extends TCPDF {

    //Page header
    public function Header() {

        $this->SetFont('helvetica', 'B', 20);
				
				$this->SetY(50);

    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-35);
        // Set font
        $this->SetFont('helvetica', 'I', 8);

    }
}

$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetFont('dejavusans', '', 10);

$pdf->SetAutoPageBreak(true, 24);

$pdf->AddPage();

$sondaggio = $wpdb->get_results("SELECT sounding_id,sounding_name,sounding_expiration_date FROM ".$wpdb->wordsurvey." WHERE sounding_id = ".$s);
foreach($sondaggio as $sound){
	$pdf->SetFont('dejavusans', 'B', 16);
	$pdf->writeHTMLCell(190, 10,$pdf->GetX(), $pdf->GetY()+15, '<p align="center">'.$sound->sounding_name.'<br><font size="7">Statistiche</font></p>', 0, 1, false);
	
	$domande = $wpdb->get_results("SELECT sounding_question_id, sounding_question_question, sounding_question_type, sounding_question_num,sounding_question_totvote FROM ".$wpdb->wordsurvey_question." WHERE sounding_question_soundid = ".(int)$sound->sounding_id);
	foreach($domande as $qstats){
		$col = array('#00A0E0','#00E100','#D2BFFF','#FF9F71','#B1B1D2','#FF9FCF','#82C0FF','#FFFF35','#00C196','#C082FF');
		$bar = array();
		$arr = array();
		$colours = array();
		$val = array();
		$q = '';
		$i = 0;
		$c2 = array();
		
		$tbl = '<table cellspacing="0" cellpadding="5" border="0" nobr="true">'.
		'<tr style="background-color:#EFEFEF;">'.
			'<td>'.$qstats->sounding_question_question.' </td>'.
		'</tr></table>';
		$pdf->SetFont('dejavusans', '', 10);
		$pdf->writeHTMLCell(190, 5,$pdf->GetX(), $pdf->GetY()+10, $tbl, 0, 1, false);
		$y = $pdf->GetY();
		
		$pdf->SetFont('dejavusans', '', 9);
		$tblr = '<table border="0" cellspacing="0" cellpadding="0" nobr="true">';
		$risposte = $wpdb->get_results("SELECT sounding_answer_id, sounding_answer_answers,sounding_answer_votes FROM ".$wpdb->wordsurvey_answer." WHERE sounding_answer_questid = ".(int)$qstats->sounding_question_id);
		foreach($risposte as $astats){
			if($qstats->sounding_question_totvote != 0){
				$percent = (($astats->sounding_answer_votes*100)/$qstats->sounding_question_totvote);
			} else {
				$percent = 0;
			}
			$tblr .= '<tr>';
			if($qstats->sounding_question_type != 1){
				$tblr .= '<td width="12"><div style="background-color:'.$col[$i].';"></div></td>';
				$tblr .= '<td width="320">  '. round($percent,2).'% ('.$astats->sounding_answer_votes.' of '.$qstats->sounding_question_totvote.' votes) <b>'.$astats->sounding_answer_answers.'</b></td>';
				
			}

			if($qstats->sounding_question_type == 1){
				$text = $wpdb->get_results("SELECT sounding_vote_text FROM ".$wpdb->wordsurvey_vote." WHERE sounding_vote_answerid = ".(int)$astats->sounding_answer_id);
				
				$tblr .= '<td width="120">  '. round($percent,2).'% ('.$astats->sounding_answer_votes.' of '.$qstats->sounding_question_totvote.' votes) <b>'.$astats->sounding_answer_answers.'</b></td>';
				$tblr .= '<td width="5"> </td>';
				$tblr .= '<td width="407"><table border="0" cellspacing="0" cellpadding="7">';
				
				$bkg = "#F9F9F9";
				foreach($text as $atstats){

					$tblr .= '<tr style="background-color:'.$bkg.';"><td>"'.$atstats->sounding_vote_text.'"</td></tr>';
					
					
					if($bkg == '#F9F9F9'){
						$bkg = "#FFFFFF";						
					} else {
						$bkg = "#F9F9F9";
					}
					
					
				}
				$tblr .= '</table></td>';
			}
			
			$p = round($percent,2);
			if((int)$p != 0){
				$c2[] = $col[$i];
			}
			
			$tblr .= '</tr>';
			
			$val[$astats->sounding_answer_answers] = round($percent,2);
			
			if($i > 9)
				$i = 0;
			else
				$i++;
			
			
		}
		$tblr .= '</table>';
		
		
		$pdf->writeHTMLCell('10','5',$pdf->GetX(),$pdf->GetY()+5,$tblr, 0,0,false);

			if($qstats->sounding_question_type != 1){
				$settings = array(
					'stroke_colour' => '#fff',
					'back_stroke_width' => 0,  'back_stroke_colour' => '#eee',
					'stroke_width' => 1,		'show_label_percent' => false,
					'pad_right' => 20,         'pad_left' => 20,
					'show_labels' => false, 	'sort' => false
				);
				$graph = new SVGGraph(150, 150,$settings);
				$graph->colours = $c2;
				$graph->Values($val);
				$g = $graph->Fetch('PieGraph',false);
				
				
				$pdf->ImageSVG('@' . $g, $pdf->GetX()+120, $y+5, '', '','', '', '', 0, false);	
			}
		
		
		
		$pdf->SetY($pdf->GetY()+50);
		$pdf->MultiCell(190, 4, '',0, 'L', false);

	}

}

$pdf->Output('example.pdf', 'I');
?>