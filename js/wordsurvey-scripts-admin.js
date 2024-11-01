/*funzioni pagina di aggiunta sondaggi*/
jQuery(document).ready(function(){
	jQuery('.infosgn').popover();
});


function ShowQuestion(){
	var ty = jQuery("select#sounding_question_type").val();
	var n = jQuery("#sounding_question_num").val();
	var i;
	if((ty != "1") && (ty != "")){
		jQuery("#div_sounding_question_num").css("display","block");
	} else {
		jQuery("#div_sounding_question_num").val('1');
		jQuery("#div_sounding_question_num").css("display","none");
		jQuery("#form-answer-create").css("display","none");
	}
}

function ShowQuestionWithCod(cod){
	var ty = jQuery("select#sounding_question_type_"+cod).val();
	var n = jQuery("#sounding_question_num_"+cod).val();
	var i;
	if((ty != "1") && (ty != "")){
		jQuery("#div_sounding_question_num_"+cod).css("display","block");
	} else {
		jQuery("#div_sounding_question_num_"+cod).val('1');
		jQuery("#div_sounding_question_num_"+cod).css("display","none");
		jQuery("#form-answer-create_"+cod).css("display","none");
	}
}


function ShowAnswer(){
	var ty = jQuery("select#sounding_question_type").val();
	var n = jQuery("#sounding_question_num").val();
	var i;
	if((ty != "1") && (ty != "")){
		jQuery("#form-answer-create").css("display","block");
		jQuery("#form-answer-create div[id*='answer_']").each(function(){
			jQuery(this).remove();
		
		});
		for(i = 0; i <n; i++){
			jQuery("#div_answeradd").before('<div id="answer_'+(i + 1)+'"><label>Risposta n. '+(i + 1)+'</label><a class="buttondel" id="buttondel_'+(i + 1)+'" type="button" onclick="WRDsrvDelAnswer('+(i + 1)+')" style="display:none;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a><input type="text" value="" name="sounding_answer" size="65%" /></div>');
		}
	} else {
		jQuery("#form-answer-create").css("display","none");
	}
}

function ShowAnswerWithCod(cod){
	var ty = jQuery("select#sounding_question_type_"+cod).val();
	var n = jQuery("#sounding_question_num_"+cod).val();
	var i;
	if((ty != "1") && (ty != "")){
		jQuery("#form-answer-create_"+cod).css("display","block");
		jQuery("#form-answer-create_"+cod+" div[id*='answer_']").each(function(){
			jQuery(this).remove();
		
		});
		
		for(i = 0; i <n; i++){
			jQuery("#div_answeradd_"+cod).before('<div id="answer_'+(i + 1)+'"><label>Risposta n. '+(i + 1)+'</label><a class="buttondel" id="buttondel_'+cod+'_'+(i + 1)+'" type="button" onclick="WRDsrvDelAnswerWithCod('+(i + 1)+','+cod+')"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a> <input type="text" value="" name="sounding_answer_'+cod+'" size="65%" /></div>');
		}
	} else {
		jQuery("#form-answer-create_"+cod).css("display","none");
	}
}


function WRDsrvEditSounding(cod){

	var act = 's';
	var subact = 'e';
	var title = jQuery("#sounding_title").val();
	var expire = jQuery("#exdate").val();
	
	jQuery("#sounding_edit").attr("disabled","disabled");
	jQuery("#sounding_edit").after('<img id="loadgif" src="'+objectL10n.wrdsrv_image_path+'" width="25px" height="25px">');
	
	jQuery.post(objectL10n.ajaxurl,{  
			action: 'wrdsrva_edit_sounding',
			cod: cod,
			act:act,
			subact:subact,
			title:title,
			expire:expire},
    function(data){  
			var res = JSON.parse(data);
			var mess;
			jQuery.each(res,function(i){
				mess = res[i].msg;
			});
			jQuery("#message").append(mess);
			jQuery("#sounding_edit").removeAttr("disabled");
			jQuery("#loadgif").remove();

    }); 

}

function WRDsrvDelSounding(cod){
	
	var act = 's';
	var subact = 'd';
	var title = jQuery("#sounding_title").val();
	
	if(confirm("Are you sure to delete sounding "+title+"?")){
	
		jQuery("#sounding_del").attr("disabled","disabled");
		jQuery("#sounding_del").after('<img id="loadgif" src="'+objectL10n.wrdsrv_image_path+'" width="25px" height="25px">');
		
		jQuery.post(objectL10n.ajaxurl,{  
			action: 'wrdsrva_delete_sounding',
			cod: cod,
			act:act,
			subact:subact,
			title:title},
		function(data) {  
				var res = JSON.parse(data);
				var mess;
				jQuery.each(res,function(i){
					mess = res[i].msg;
				});
				jQuery("#message").append(mess);
				jQuery("#form-question-create").css("display","none");
				jQuery("#sounding-questionadd-button").css("display","none");
				jQuery("#sounding_del").remove();
				jQuery("#sounding_edit").remove();
				jQuery("#sounding_title").val('');
				jQuery("#expiredate").after('<button id="sounding_button" type="button" class="button-primary btn btn-primary" name="sounding-button" onclick="WRDsrvSaveSoundingAjax()">'+objectL10n.Create_Sounding+'</button>');

				jQuery("#loadgif").remove();

			});
	}
}


function WRDsrvEditQuestion(cod){
	var i;
	var btn;
	var answer = [];
	var obbl;
	
	if(jQuery("#form-question-create_"+cod).length){
		i = "_"+cod;
	} else {
		i = "";
	}
	
	var idsounding = jQuery("#sounding_id").val();
	var ty = jQuery("select#sounding_question_type"+i).val();
	var title = jQuery("#sounding_question_title"+i).val();

	
	if(jQuery("#sounding_question_obbl"+i).is(":checked")){
		obbl = 1;
	} else {
		obbl = 0;		
	}
	
	var num = jQuery("#sounding_question_num"+i).val();
	if(ty == "1")
		num = 0;
	
	var act = 'q';
	var subact = 'e';
	
	if(ty != 1){
		jQuery("input[name='sounding_answer"+i+"']").each(function(){
			console.log(jQuery(this));
			answer.push(jQuery(this).val());
		});
	} else {
		answer.push('');
	}
	
	
	if(jQuery("#sounding_question_edit_"+cod).length){
		btn = "_"+cod;
	} else {
		btn = "";
	}
	
	jQuery("#sounding_question_edit"+btn).attr("disabled","disabled");
	jQuery("#sounding_question_edit"+btn).after('<img id="loadgif" src="'+objectL10n.wrdsrv_image_path+'" width="25px" height="25px">');
	
	jQuery.post(objectL10n.ajaxurl,{  
			action: 'wrdsrva_edit_question_sounding',
			cod: cod,
			ty:ty,
			title:title,
			obbl:obbl,
			num:num,
			answer:answer,
			act:act,
			subact:subact},
    function(data) {  
		var res = JSON.parse(data);
		var mess;
		var idquestion;
		jQuery.each(res,function(i){
			mess = res[i].msg;
			idquestion = res[i].idquest;
		});

		jQuery("#loadgif").remove();
		jQuery("#sounding_question_edit"+btn).removeAttr("disabled");
			
		jQuery("a[id*='buttondel"+i+"']").each(function(){
			jQuery(this).css("display","block");
		});
			
		jQuery("#message").append(mess);
		jQuery("#questtitlecoll"+i).text(jQuery("#sounding_question_title"+i).val());
		WRDsrvPreviewLoad(idsounding);

    });
	
}

function WRDsrvDelQuestion(cod){
	var i;
	var btn;
	
	var act = 'q';
	var subact = 'd';
	
	var idsounding = jQuery("#sounding_id").val();
	
	if(jQuery("#form-question-create_"+cod).length){
		i = "_"+cod;
	} else {
		i = "";
	}
	
	if(jQuery("#sounding_question_del_"+cod).length){
		btn = "_"+cod;
	} else {
		btn = "";
	}
	
	if(confirm("Are you sure to delete question "+cod+"?")){
	
		jQuery("#sounding_question_del"+btn).attr("disabled","disabled");
		jQuery("#sounding_question_del"+btn).after('<img id="loadgif" src="'+objectL10n.wrdsrv_image_path+'" width="25px" height="25px">');
		
		jQuery.post(objectL10n.ajaxurl,{  
			action: 'wrdsrva_delete_question_sounding',
			cod: cod,
			act:act,
			subact:subact},
			function(data) {  
				var res = JSON.parse(data);
				var mess;
				jQuery.each(res,function(i){
					mess = res[i].msg;
				});
				jQuery("#loadgif").remove();
				jQuery("#form-question-create"+i).css("display","none");
				jQuery("#message").append(mess);

				WRDsrvPreviewLoad(idsounding);

			});
	}

}

function AddQuestion(){
	
	var questid = jQuery("#sounding_quest_id").val();
	jQuery("#sounding_question_title").attr('id', 'sounding_question_title_'+questid);
	jQuery("#sounding_question_obbl").attr('id', 'sounding_question_obbl_'+questid);
	jQuery("#sounding_question_num").attr('id', 'sounding_question_num_'+questid);
	jQuery("#sounding_question_type").attr('id', 'sounding_question_type_'+questid);
	jQuery("#div_sounding_question_num").attr('id', 'div_sounding_question_num_'+questid);
	jQuery("#form-answer-create").attr('id', 'form-answer-create_'+questid);
	jQuery("input[name='sounding_answer']").each(function(){
		jQuery("input[name='sounding_answer']").removeAttr('name').attr("name","sounding_answer_"+questid);
	});
	
	jQuery("#sounding_quest_id").attr('id', 'sounding_quest_id_'+questid);
	
	jQuery("#form-question-create").attr('id', 'form-question-create_'+questid);
	
	//cambio onchage della tipologia
	jQuery("#sounding_question_type_"+questid).removeAttr("onchange");
	jQuery("#sounding_question_type_"+questid).attr("onchange","ShowQuestionWithCod("+questid+")");
	
	//cambio onchange del numero di risposte
	jQuery("#sounding_question_num_"+questid).removeAttr("onchange");
	jQuery("#sounding_question_num_"+questid).attr("onchange","ShowAnswerWithCod("+questid+")");

	jQuery("a[id*='buttondel_']").each(function(){
		var idb = jQuery(this).attr('id');
		var s = idb.split('_');
		
		jQuery(this).removeAttr('onclick');
		jQuery(this).attr("onclick","WRDsrvDelAnswerWithCod("+s[1]+","+questid+")");
		
		jQuery(this).attr('id', 'buttondel_'+questid+'_'+s[1]);
	});
	
	
	jQuery('#div_answeradd').attr('id', 'div_answeradd_'+questid);
	jQuery('#sounding_answeradd_button').attr('id', 'sounding_answeradd_button_'+questid);
	
	jQuery('#sounding_answeradd_button_'+questid).attr("onclick","AddAnswerWithCod("+questid+")");
	
	//aggiungo i pulsanti di modifica e cancellazione
	jQuery("#sounding_question_edit").attr('id', 'sounding_question_edit_'+questid);
	jQuery("#sounding_question_del").attr('id', 'sounding_question_del_'+questid);
	
	//cambio lo stato dell'accordion
	jQuery('#questtitlecoll').addClass("collapsed");
	jQuery('#questtitlecoll').attr("href","#collapseOne_"+questid);
	jQuery('#questtitlecoll').attr("data-parent","form-question-create_"+questid);
	jQuery('#questtitlecoll').attr("aria-expanded",false);
	
	jQuery("#collapseOne").removeClass("in");
	jQuery("#collapseOne").attr("aria-expanded",false);
	jQuery('#collapseOne').attr("id","collapseOne_"+questid);
	jQuery('#questtitlecoll').attr("id","questtitlecoll_"+questid);
	

	//aggiungo la nuova domanda
	var addque = '<div class="panel panel-default question-create" id="form-question-create"><div class="panel-heading" id="headingOne"><h4 class="panel-title"><a id="questtitlecoll" data-toggle="collapse" data-parent="#form-question-create" href="#collapseOne" class="accordion-toggle" aria-expanded="true">'+objectL10n.Question+'</a></h4></div><div id="collapseOne" class="collapse in" aria-expanded="true"><div class="panel-body"><div class="add-quest-opt" id="div_sounding_question_title"><label>'+objectL10n.Question+'</label><input id="sounding_question_title" type="text" value="" name="sounding_question_title" size="70%"/></div><fieldset><label>'+objectL10n.Type+'</label><select id="sounding_question_type" name="sounding_question_type" aria-invalid="false" onchange="ShowQuestion()"><option value="">'+objectL10n.select+'</option><option value="1">text</option><option value="2">radio</option><option value="3">'+objectL10n.selection+'</option><option value="4">checkbox</option></select></fieldset><div class="add-quest-opt" id="div_sounding_question_obbl"><label>'+objectL10n.Obligatory_Question+'</label><input id="sounding_question_obbl" name="sounding_question_obbl" type="checkbox" value="si" /></div><div class="add-quest-opt" id="div_sounding_question_num"><label>'+objectL10n.Number_of_reply+'</label><input id="sounding_question_num" type="text" value="1" name="sounding_question_num" size="5" onchange="ShowAnswer()" onpaste="this.onchange();" /><div class="answer-create" id="form-answer-create"><input id="sounding_quest_id" type="hidden" value=""><div id="answer_1"><label>'+objectL10n.Reply_n_1+'</label><input id="sounding_answer" type="text" value="" name="sounding_answer" size="65%"/></div><div id="div_answeradd"><button id="sounding_answeradd_button" type="button" class="button-primary btn btn-primary" name="sounding_answeradd_button" onclick="AddAnswer()">'+objectL10n.Add_Answer+'</button></div></div></div><button id="sounding_question_button" type="button" class="button-primary btn btn-primary" name="sounding-question-button" onclick="WRDsrvSaveQuestionAjax()" >'+objectL10n.Save_Question+'</button></div></div></div>';
	
	jQuery("#form-question-create_"+questid).after(addque);
	
}


function AddAnswer(){
	
	var numero = jQuery("#form-answer-create div[id*='answer_']").length;
	var risposte = jQuery('#sounding_question_num').val();
	
	jQuery('#div_answeradd').before('<div id="answer_'+(parseInt(risposte) + 1)+'"><label>Risposta n. '+(parseInt(risposte) + 1)+'</label><a id="buttondel_'+(parseInt(risposte) + 1)+'" type="button" class="buttondel" onclick="WRDsrvDelAnswer('+(parseInt(risposte) + 1)+')" style="display:none;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a><input id="sounding_answer" type="text" value="" name="sounding_answer" size="65%"/></div>');
	
	jQuery('#sounding_question_num').val(parseInt(risposte) + 1);
}

function WRDsrvDelAnswer(cd){
	var quest = jQuery('#sounding_quest_id').val();
	var idsounding = jQuery("#sounding_id").val();
	var act = 'a';
	var tit = jQuery('#answer_'+cd+' input[name="sounding_answer"]').val();
	var risp = jQuery('#sounding_question_num').val();
	
	if(confirm("Are you sure to delete this answer?")){

		jQuery("#buttondel_"+cd).after('<img id="loadgif" src="'+objectL10n.wrdsrv_image_path+'" width="25px" height="25px">');
	
		jQuery.post(objectL10n.ajaxurl,{  
			action: 'wrdsrva_delete_answer_sounding',
			quest: quest,
			act:act,
			tit:tit},
			function(data) {  
				var res = JSON.parse(data);
				var mess;
				jQuery.each(res,function(i){
					mess = res[i].msg;
				});
				jQuery("#loadgif").remove();
				jQuery('#form-answer-create div[id="answer_'+cd+'"]').remove();
				jQuery('#sounding_question_num').val(parseInt(risp) - 1);
				jQuery("#message").append(mess);

				WRDsrvPreviewLoad(idsounding);

			});
	}
}

function AddAnswerWithCod(cod){
	
	var numero = jQuery("#form-answer-create_"+cod+" div[id*='answer_']").length;
	var risposte = jQuery('#sounding_question_num_'+cod).val();
	
	jQuery('#div_answeradd_'+cod).before('<div id="answer_'+(parseInt(risposte) + 1)+'"><label>Risposta n. '+(parseInt(risposte) + 1)+'</label><a id="buttondel_'+cod+'_'+(parseInt(risposte) + 1)+'" type="button" class="buttondel" onclick="WRDsrvDelAnswerWithCod('+(parseInt(risposte) + 1)+','+cod+')" style="display:none;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a> <input id="sounding_answer" type="text" value="" name="sounding_answer_'+cod+'" size="65%"/></div>');
	
	jQuery('#sounding_question_num_'+cod).val(parseInt(risposte) + 1);

}

function WRDsrvDelAnswerWithCod(cd,cod){
	var quest = jQuery('#sounding_quest_id_'+cod).val();
	var idsounding = jQuery("#sounding_id").val();
	var act = 'a';
	var tit = jQuery('#answer_'+cd+' input[name="sounding_answer_'+cod+'"]').val();
	var risp = jQuery('#sounding_question_num_'+cod).val();
	
	if(confirm("Are you sure to delete answer "+cd+"?")){
		jQuery("#buttondel_"+cod+"_"+cd).after('<img id="loadgif" src="'+objectL10n.wrdsrv_image_path+'" width="25px" height="25px">');
	
		jQuery.post(objectL10n.ajaxurl,{  
			action: 'wrdsrva_delete_answercod_sounding',
			quest: quest,
			act:act,
			tit:tit},
			function(data) {  
				var res = JSON.parse(data);
				var mess;
				jQuery.each(res,function(i){
					mess = res[i].msg;
				});
				jQuery("#loadgif").remove();
				jQuery('#form-answer-create_'+cod+' div[id="answer_'+cd+'"]').remove();
				jQuery('#sounding_question_num_'+cod).val(parseInt(risp) - 1);
				jQuery("#message").append(mess);

				WRDsrvPreviewLoad(idsounding);
			}
		);
	}	
}


/*Funzioni pagina di modifica sondaggi*/
function WRDsrvMasterDelete(cod,page){
	
	if(confirm("Are you sure to delete sounding? All data will be lost.")){
	
		jQuery("#sounding_del").attr("disabled","disabled");
		jQuery("#sounding_del").after('<img id="loadgif" src="'+objectL10n.wrdsrv_image_path+'" width="25px" height="25px">');
		
		var act = "d";

		jQuery.post(objectL10n.ajaxurl,{  
			action: 'wrdsrva_masterdelete_sounding',
			cod:cod,
			act:act},
			function(data) {  
				if(page == 'main'){
					var res = JSON.parse(data);
					var mess;
					jQuery.each(res,function(i){
						mess = res[i].msg;
					});
					jQuery("#message").append(mess);
					jQuery("#loadgif").remove();
					jQuery("#"+cod).fadeOut("slow"); 
				} else if(page == 'edit'){
					window.location = "/wp-admin/admin.php?page=wp-wordsurvey/wordsurvey-manager.php";
				}
			}
		);
	}	

}

function MasterAddQuestion(){
	
	//aggiungo la nuova domanda
	var addque = '<div class="panel panel-default question-create" id="form-question-create"><div class="panel-heading" id="headingOne"><h4 class="panel-title"><a id="questtitlecoll" data-toggle="collapse" data-parent="#form-question-create" href="#collapseOne" class="accordion-toggle" aria-expanded="true">'+objectL10n.Question+'</a></h4></div><div id="collapseOne" class="collapse in" aria-expanded="true"><div class="panel-body"><div class="add-quest-opt" id="div_sounding_question_title"><label>'+objectL10n.Question+'</label><input id="sounding_question_title" type="text" value="" name="sounding_question_title" size="70%"/></div><fieldset><label>'+objectL10n.Type+'</label><select id="sounding_question_type" name="sounding_question_type" aria-invalid="false" onchange="ShowQuestion()"><option value="">'+objectL10n.select+'</option><option value="1">text</option><option value="2">radio</option><option value="3">'+objectL10n.selection+'</option><option value="4">checkbox</option></select></fieldset><div class="add-quest-opt" id="div_sounding_question_obbl"><label>'+objectL10n.Obligatory_Question+'</label><input id="sounding_question_obbl" name="sounding_question_obbl" type="checkbox" value="si" /></div><div class="add-quest-opt" id="div_sounding_question_num"><label>'+objectL10n.Number_of_reply+'</label><input id="sounding_question_num" type="text" value="1" name="sounding_question_num" size="5" onchange="ShowAnswer()" onpaste="this.onchange();" /><div class="answer-create" id="form-answer-create"><input id="sounding_quest_id" type="hidden" value=""><div id="answer_1"><label>'+objectL10n.Reply_n_1+'</label><input id="sounding_answer" type="text" value="" name="sounding_answer" size="65%"/></div><div id="div_answeradd"><button id="sounding_answeradd_button" type="button" class="button-primary btn btn-primary" name="sounding_answeradd_button" onclick="AddAnswer()">'+objectL10n.Add_Answer+'</button></div></div></div><button id="masteredit_question_button" type="button" class="button-primary btn btn-primary" name="masteredit_question_button" onclick="WRDsrvMasterSaveQuestion()" >'+objectL10n.Save_Question+'</button></div></div></div>';
	
	jQuery('a[id*="questtitlecoll_"]').addClass("collapsed");
	
	jQuery("div[id*='collapseOne_']").removeClass("in");
	
	jQuery("#masteredit_questionadd_button").before(addque);
	
}

function WRDsrvMasterSaveQuestion(){
	
	var idsounding = jQuery("#sounding_id").val();
	var ty = jQuery("select#sounding_question_type").val();
	var title = jQuery("#sounding_question_title").val();
	if(jQuery("#sounding_question_obbl").is(":checked")){
		var obbl = 1;
	} else {
		var obbl = 0;		
	}
	
	var num = jQuery("#sounding_question_num").val();
	var act = 'q';
	
	var answer = [];
	jQuery("input[name='sounding_answer']").each(function(){
		answer.push(jQuery(this).val());
		
	});
	
	jQuery("#masteredit_question_button").attr("disabled","disabled");
	jQuery("#masteredit_question_button").after('<img id="loadgif" src="'+objectL10n.wrdsrv_image_path+'" width="25px" height="25px">');
	
	jQuery.post(objectL10n.ajaxurl,{  
			action: 'wrdsrva_mastersave_question_sounding',
			idsounding:idsounding,
			ty:ty,
			title:title,
			obbl:obbl,
			num:num,
			answer:answer,
			act:act},
		function(data) {
			var res = JSON.parse(data);
			var mess;
			var idquestion;
			jQuery.each(res,function(i){
				mess = res[i].msg;
				idquestion = res[i].idquest;
			});

			jQuery("#message").append(mess);
			jQuery("#sounding_quest_id").val(idquestion);
			jQuery("#questtitlecoll").text(jQuery("#sounding_question_title").val());
			jQuery("#div_sounding_question_num").after('<button id="sounding_question_edit_'+idquestion+'" type="button" class="button-warning btn btn-warning" name="sounding-edit" onclick="WRDsrvEditQuestion('+idquestion+')" >'+objectL10n.Edit_Question+'</button>');
			jQuery("#sounding_question_edit_"+idquestion).after('<button id="sounding_question_del_'+idquestion+'" type="button" class="button-danger btn btn-danger" name="sounding-del" onclick="WRDsrvDelQuestion('+idquestion+')" >'+objectL10n.Delete_Question+'</button>');

			jQuery("#sounding_question_title").attr('id', 'sounding_question_title_'+idquestion);
			jQuery("#sounding_question_obbl").attr('id', 'sounding_question_obbl_'+idquestion);
			jQuery("#sounding_question_num").attr('id', 'sounding_question_num_'+idquestion);
			jQuery("#sounding_question_type").attr('id', 'sounding_question_type_'+idquestion);
			jQuery("#div_sounding_question_num").attr('id', 'div_sounding_question_num_'+idquestion);
			jQuery("#form-answer-create").attr('id', 'form-answer-create_'+idquestion);
			jQuery("input[name='sounding_answer']").each(function(){
				jQuery("input[name='sounding_answer']").removeAttr('name').attr("name","sounding_answer_"+idquestion);
			});
			
			jQuery("#sounding_quest_id").attr('id', 'sounding_quest_id_'+idquestion);
			jQuery("#form-question-create").attr('id', 'form-question-create_'+idquestion);
			
			//cambio onchage della tipologia
			jQuery("#sounding_question_type_"+idquestion).removeAttr("onchange");
			jQuery("#sounding_question_type_"+idquestion).attr("onchange","ShowQuestionWithCod("+idquestion+")");
			
			//cambio onchange del numero di risposte
			jQuery("#sounding_question_num_"+idquestion).removeAttr("onchange");
			jQuery("#sounding_question_num_"+idquestion).attr("onchange","ShowAnswerWithCod("+idquestion+")");
			
			//cambio lo stato dell'accordion
			jQuery('#questtitlecoll').addClass("collapsed");
			jQuery('#questtitlecoll').attr("href","#collapseOne_"+idquestion);
			jQuery('#questtitlecoll').attr("data-parent","form-question-create_"+idquestion);
			jQuery('#questtitlecoll').attr("aria-expanded",false);
			jQuery("#collapseOne").removeClass("in");
			jQuery("#collapseOne").attr("aria-expanded",false);
			jQuery('#collapseOne').attr("id","collapseOne_"+idquestion);
			jQuery('#questtitlecoll').attr("id","questtitlecoll_"+idquestion);
			
			jQuery('#sounding_answeradd_button').attr('id','sounding_answeradd_button_'+idquestion);
			jQuery('#sounding_answeradd_button_'+idquestion).attr("onclick","AddAnswerWithCod("+idquestion+")");
			
			jQuery("#div_answeradd").attr('id', 'div_answeradd_'+idquestion);
			
			jQuery("a[id*='buttondel_']").each(function(){
				var idb = jQuery(this).attr('id');
				var s = idb.split('_');
				
				jQuery(this).removeAttr('onclick');
				jQuery(this).attr("onclick","WRDsrvDelAnswerWithCod("+s[1]+","+idquestion+")");
				
				jQuery(this).attr('id', 'buttondel_'+idquestion+'_'+s[1]);
			});
			
			jQuery("a[id*='buttondel_"+idquestion+"']").each(function(){
				jQuery(this).css("display","block");
			});
			
			jQuery("#loadgif").remove();
			jQuery("#masteredit_question_button").remove();
		}
	);
}

function WRDsrvSaveSoundingAjax(){
	
	var sound_tit = jQuery("#sounding_title").val();
	var expire = jQuery("#exdate").val();
	var act = 's';
	
	if(sound_tit != ''){
	
		jQuery("#sounding_button").attr("disabled","disabled");
		jQuery("#sounding_button").after('<img id="loadgif" src="'+objectL10n.wrdsrv_image_path+'" width="25px" height="25px">');
		jQuery.post(objectL10n.ajaxurl,{  
			action: 'wrdsrva_save_sounding',
			sound_tit: jQuery("#sounding_title").val(),
			expire: jQuery("#exdate").val(),
			act: 's'},
			function(data){  
				var res = JSON.parse(data);
				var idtit = '';
				var mess = '';
				
				jQuery.each(res,function(i){
					idtit = res[i].idtitolo;
					mess = res[i].msg;
				});
							
				jQuery("#message").append(mess);
				jQuery('#bodyprevsurvey').css("display","block");
				jQuery("#loadgif").remove();
				//tolgo pulsante di creazione
				jQuery("#sounding_button").remove();
				jQuery("#createtxt").remove();
				//aggiungo modifica e cancellazione
				jQuery("#expiredate").after('<button id="sounding_edit" type="button" class="button-warning btn btn-warning" name="sounding-edit" onclick="WRDsrvEditSounding('+idtit+')" >'+objectL10n.Edit_Sounding+'</button>');
				jQuery("#sounding_edit").after('<button id="sounding_del" type="button" class="button-danger btn btn-danger" name="sounding-del" onclick="WRDsrvDelSounding('+idtit+')" >'+objectL10n.Delete_Sounding+'</button>');
				
				jQuery("#form-question-create").css("display","block");
				jQuery("#sounding_id").val(idtit);
				jQuery("#sounding-questionadd-button").css("display","block");
				
				WRDsrvPreviewLoad(idtit);
				
			});
			
	} else {
		jQuery('#message').append('<p style="color: red;">You can\'t create an empty survey</p>');	
	}
}

function WRDsrvSaveQuestionAjax(){
	
	var idsounding = jQuery("#sounding_id").val();
	var ty = jQuery("select#sounding_question_type").val();
	var title = jQuery("#sounding_question_title").val();
	var obbl;
	if(jQuery("#sounding_question_obbl").is(":checked")){
		obbl = 1;
	} else {
		obbl = 0;		
	}
	
	var num = jQuery("#sounding_question_num").val();
	var act = 'q';
	
	var answer = [];
	jQuery("input[name='sounding_answer']").each(function(){
		answer.push(jQuery(this).val());
		
	});
	
	jQuery("#sounding_question_button").attr("disabled","disabled");
	jQuery("#sounding_question_button").after('<img id="loadgif" src="'+objectL10n.wrdsrv_image_path+'" width="25px" height="25px">');
	
	jQuery.post(objectL10n.ajaxurl,{  
			action: 'wrdsrva_save_question_sounding',
			idsounding: idsounding,
			ty:ty,
			title:title,
			obbl:obbl,
			num:num,
			answer:answer,
			act:act},
		function(data) { 
			var quest = JSON.parse(data);
			var idquestion;
			var mess;

			jQuery.each(quest,function(i){
				idquestion = quest[i].idquest;
				mess = quest[i].msg;
			});
			
			jQuery("#loadgif").remove();
			jQuery("#sounding_question_button").remove();
			jQuery("#infosgnquest").remove();

			jQuery("#message").append(mess);
			jQuery("#sounding_quest_id").val(idquestion);
			jQuery("#questtitlecoll").text(jQuery("#sounding_question_title").val());
			jQuery("#div_sounding_question_num").after('<button id="sounding_question_edit" type="button" class="button-warning btn btn-warning" name="sounding-edit" onclick="WRDsrvEditQuestion('+idquestion+')" >'+objectL10n.Edit_Question+'</button> ');
			jQuery("#sounding_question_edit").after('<button id="sounding_question_del" type="button" class="button-danger btn btn-danger" name="sounding-del" onclick="WRDsrvDelQuestion('+idquestion+')" >'+objectL10n.Delete_Question+'</button>');
			jQuery("a[id*='buttondel_']").each(function(){
				jQuery(this).css("display","block");
			});
			
			WRDsrvPreviewLoad(idsounding);
    });
}

function WRDsrvPreviewLoad(idsoun){
	
	jQuery.post(objectL10n.ajaxurl,{  
			action: 'wrdsrva_preview',
			idsoun: idsoun},
		function(data) { 
			var p = JSON.parse(data);
			var ht;
			jQuery.each(p,function(i){
				ht = p[i].html;
			});
		
			jQuery("#bodyprevsurvey").empty().append(ht);
    });
}
