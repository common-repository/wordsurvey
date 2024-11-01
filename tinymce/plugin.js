(function() {
	tinymce.PluginManager.add('wordsurvey', function(editor) {
		editor.addCommand('WP-WordSurvey-Insert', function() {
			var sounding_id = jQuery.trim(prompt(tinymce.translate('Enter Survey ID')));
			while(isNaN(sounding_id)) {
				sounding_id = jQuery.trim(prompt(tinymce.translate('Error: ID must be numeric') + "\n\n" + tinymce.translate('Please enter ID again')));
			}
			if (sounding_id >= -1 && sounding_id != null && sounding_id != "") {
				editor.insertContent('[wordsurvey id="' + sounding_id + '"]');
			}
		});
		editor.addButton('wordsurvey', {
			text: false,
			tooltip: tinymce.translate('Insert Survey'),
			icon: 'wordsurvey dashicons-before dashicons-chart-pie',
			onclick: function() {
				tinyMCE.activeEditor.execCommand('WP-WordSurvey-Insert')
			}
		});
	});
})();