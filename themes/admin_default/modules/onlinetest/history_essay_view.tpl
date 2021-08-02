<!-- BEGIN: main -->
<div id="history-content">
	<!-- BEGIN: success -->
		<div class="alert alert-success">
			<i class="fa fa-check-circle"></i> {SUCCESS}<i class="fa fa-times"></i>
		</div>
	<!-- END: success -->
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-list"></i> {LANG.history_view}: {DATA.title}</h3> 
			<div class="pull-right">
				
				<button onclick="print_history('{DATA.history_essay_id}', '{DATA.token}')" type="button" data-toggle="tooltip" data-placement="top" class="btn btn-primary btn-sm" id="button-print" title="{LANG.save}">
					<i class="fa fa-floppy-o" aria-hidden="true"></i>

				</button>
				
				<button onclick="delete_history('{DATA.history_essay_id}', '{DATA.token}')" type="button" data-toggle="tooltip" data-placement="top" class="btn btn-danger btn-sm" id="button-delete" title="{LANG.delete}">
					<i class="fa fa-trash-o"></i>
				</button>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
	
			<div id="OnlineTestDoTest" class="OnlineTestDoTest">
				<div id="boxfirst" class="toggle_list_q">
					<h1>{DATA.title}  - {DATA.full_name}({DATA.username})</h1>
					<div class="body-result-1">
						<!-- BEGIN: loop -->
						<div class="boxsecond">
							<p class="question-info">
								<b>{LANG.question_stt} {LOOP.stt}</b>
								
							</p>
							
							<div class="question" id="question{LOOP.essay_id}">{LOOP.question}</div>	
							<hr class="line">
							<div class="answer" >{LOOP.answer}</div>
							<div class="clearfix"></div>
						</div>			
						<!-- END: loop -->  
					</div>
					
				</div>
				
			</div>
			<div class="clearfix"></div>

		</div>
	</div>
</div>
<div class="clearfix"></div>
<script type="text/x-mathjax-config">

MathJax.Hub.Config({
	extensions: ["tex2jax.js"],
	jax: ["input/TeX","output/HTML-CSS"],
	inlineMath: [['$','$'], ['\\\\(','\\\\)']],
	'HTML-CSS': {
		matchFontHeight: false,
		availableFonts: ["STIX"],
		webFont: 'STIX-Web',
		preferredFont: 'STIX-Web',
		styles: {
		".MathJax_Preview": {visibility: "hidden"},
	  }
	}
});
 
</script>
<script type="text/javascript">
$(document).ready(function(){
	setTimeout(function(){$('.math-tex').show()}, 400)
})
  
function print_history(history_essay_id, token) {
 
	$.ajax({
		url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=history-essay&action=print&nocache=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data: 'history_essay_id=' + history_essay_id + '&token=' + token,
		beforeSend: function() {
			$('#button-print i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
			$('#button-print').prop('disabled', true);
		},	
		complete: function() {
			$('#button-print i').replaceWith('<i class="fa fa-floppy-o"></i>');
			$('#button-print').prop('disabled', false);
		},
		success: function(json) {
			$('.alert').remove();

			if( json['link'] )
			{
				window.location.href= json['link'];
			}else if( json['error'] ) 
			{
				alert( json['error'] );
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	 
}
 
function delete_history(history_essay_id, token) {
	if(confirm('{LANG.confirm}')) {
		$.ajax({
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=history-essay&action=delete&nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'history_essay_id=' + history_essay_id + '&token=' + token + '&redirect=1',
			beforeSend: function() {
				$('#button-delete i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
				$('#button-delete').prop('disabled', true);
			},	
			complete: function() {
				$('#button-delete i').replaceWith('<i class="fa fa-trash-o"></i>');
				$('#button-delete').prop('disabled', false);
			},
			success: function(json) {
				$('.alert').remove();

				if( json['link'] )
				{
					location.href= json['link'];
				}else if( json['error'] ) 
				{
					alert( json['error'] );
				}	
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}


</script>
<script type="text/javascript" async  src="{NV_BASE_SITEURL}MathJax-2.7.8/MathJax.js?config=TeX-AMS_HTML-full"></script> 
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_EDITORSDIR}/ckeditor/ckeditor.js"></script>
<!-- END: main -->