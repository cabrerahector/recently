(function ($) {
	"use strict";
	$(function () {
		
		// TOOLS
		// thumb source selection
		$("#thumb_source").change(function() {
			if ($(this).val() == "custom_field") {
				$("#lbl_field, #thumb_field, #row_custom_field, #row_custom_field_resize").show();
			} else {
				$("#lbl_field, #thumb_field, #row_custom_field, #row_custom_field_resize").hide();
			}
		});
		// file upload
		$('#upload_thumb_button').click(function(e) {
			tb_show('Upload a thumbnail', 'media-upload.php?referer=recently&type=image&TB_iframe=true&post_id=0', false);
			e.preventDefault();			
		});		
		window.send_to_editor = function(html) {
			var regex = /<img[^>]+src="(http:\/\/[^">]+)"/g;
			var result = regex.exec(html);			

			if ( null != result ) {
				$('#upload_thumb_src').val(result[1]);

				var img = new Image();
				img.onload = function() {
					$("#thumb-review").html( this ).parent().fadeIn();
				}
				img.src = result[1];
			}
			
			tb_remove();			
		};
		// cache interval 
		$("#cache").change(function() {
			if ($(this).val() == 1) {
				$("#cache_refresh_interval").show();
			} else {
				$("#cache_refresh_interval, #cache_too_long").hide();
			}
		});
		// interval
		$("#cache_interval_time").change(function() {			
			var value = parseInt( $("#cache_interval_value").val() );
			var time = $(this).val();
			
			if ( time == "hour" && value > 72 ) {				
				$("#cache_too_long").show();				
			} else if ( time == "day" && value > 3 ) {				
				$("#cache_too_long").show();				
			} else if ( time == "week" && value > 1 ) {				
				$("#cache_too_long").show();				
			} else if ( time == "month" && value >= 1 ) {				
				$("#cache_too_long").show();				
			} else if ( time == "year" && value >= 1 ) {				
				$("#cache_too_long").show();
			} else {
				$("#cache_too_long").hide();
			}			
		});
		
		$("#cache_interval_value").change(function() {			
			var value = parseInt( $(this).val() );
			var time = $("#cache_interval_time").val();
			
			if ( time == "hour" && value > 72 ) {				
				$("#cache_too_long").show();				
			} else if ( time == "day" && value > 3 ) {				
				$("#cache_too_long").show();				
			} else if ( time == "week" && value > 1 ) {				
				$("#cache_too_long").show();				
			} else if ( time == "month" && value >= 1 ) {				
				$("#cache_too_long").show();				
			} else if ( time == "year" && value >= 1 ) {				
				$("#cache_too_long").show();
			} else {
				$("#cache_too_long").hide();
			}			
		});
	});
}(jQuery));