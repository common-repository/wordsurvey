function WRDsrvVoteSurvey(){
	
	var check = [];
	var rad = [];
	var sel = [];
	var txt = {};
	
	var nchek = 0;
	
	jQuery('div[id*="questionalert_"]').each(function(){
		jQuery(this).css("display","none");
	});
	
	jQuery('#questionalert_all').css("display","none");
	
	var sid = jQuery('#surveyid').val();

		jQuery('.frontend input[type="radio"]').each(function(){
			
			var r = jQuery('input:radio[name='+jQuery(this).attr("name")+']:checked').val();
			var rn = jQuery('input:radio[name='+jQuery(this).attr("name")+']:checked').length;
			console.log(r);
			console.log(rad);

				if(jQuery(this).attr('required') == 'required'){

					if(jQuery(this).prop('checked')){
						rad.push(jQuery('input:radio[name='+jQuery(this).attr("name")+']:checked').val());
					} else {
						if(jQuery('#questionalert_'+jQuery(this).attr('display')) != 'block'){
							jQuery('#questionalert_'+jQuery(this).attr('name')).css('display','block');
						}
					}
				
				} else {
					
					if(jQuery.inArray(r,rad) == -1)
						rad.push(jQuery('input:radio[name='+jQuery(this).attr("name")+']:checked').val());
				}

			
			if(rn > 0)
					jQuery('#questionalert_'+jQuery(this).attr('name')).css('display','none');
			
		});
		
		
		jQuery('.frontend input[type="checkbox"]').each(function(){
		
			var s = jQuery('input:checkbox[name='+jQuery(this).attr("name")+']:checked').length;

				var arr1 = jQuery(this).attr('value').split('_');
				if(jQuery(this).attr('required') == 'required'){
					if(jQuery(this).prop('checked')){
							check.push(jQuery(this).val());
					} else {			
						jQuery('#questionalert_'+arr1[0]).css('display','block');
					}

				} else {
					if(jQuery(this).prop('checked')){
						check.push(jQuery(this).val());
					}
				}
				
				console.log(s);
				if(s > 0)
					jQuery('#questionalert_'+arr1[0]).css('display','none');

		});
		
		
		//select
		jQuery('.frontend select').each(function(){

			var idsel = jQuery(this).attr('id');
			var arr2 = idsel.split('_');
			
			console.log(jQuery(this).attr('required'));
			
			if(jQuery(this).attr('required') == 'required'){
				console.log(jQuery(this).val());
				if(jQuery(this).val() != ''){
						sel.push(jQuery(this).val());
				} else {
					jQuery('#questionalert_'+arr2[1]).css('display','block');

				}
				
				
			} else {
					sel.push(jQuery(this).val());
				
			}

		});
		
		//textarea
		jQuery('.frontend textarea').each(function(){
				
				var $text = jQuery(this);
				var nome = $text.attr('name');
				var testo = $text.val();
				
				if(jQuery(this).attr('required') == 'required'){
					var arr3 = jQuery(this).attr('name').split('_');
					if(testo != ''){
						txt[nome] = testo;
					} else {
						jQuery('#questionalert_'+arr3[0]).css('display','block');
							
					}
						
				} else {
					
					txt[nome] = testo;
					
				}
				
		});
		
	var d = jQuery("div[id*='questionalert_']:visible");
	console.log(d.length);
	if(d.length == 0){
	
		jQuery("#vota").remove();
		jQuery("#votadiv").append('<img id="loadgif" src="'+wrdsrvajfe.wrdsrv_image_path+'" width="25px" height="25px">');
		jQuery.post(wrdsrvajfe.ajaxurl,{  
			action: 'wrdsrva_frontend_save_vote',
			sid:sid,
			check:check,
			sel:sel,
			rad:rad,
			txt:txt},
			function(data) {  
				var res = JSON.parse(data);
				var mess;
				jQuery.each(res,function(i){
					mess = res[i].msg;
				});
				jQuery('#loadgif').remove();
				jQuery('#votadiv').append(mess);
			
				window.location.replace('http://'+document.location.hostname+'/');

			}
		);
		
	} else {
		
		jQuery("#votadiv").append('<div id="questionalert_all" class="alert alert-danger">Per poter votare devi compilare tutte le domande obbligatorie!</div>');
	}
}