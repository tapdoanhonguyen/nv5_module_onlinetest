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
				<button onclick="delete_history('{DATA.history_id}', '{DATA.token}')" type="button" data-toggle="tooltip" data-placement="top" class="btn btn-danger btn-sm" id="button-delete" title="{LANG.delete}">
					<i class="fa fa-trash-o"></i>
				</button>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<div id="OnlineTestDoTest" class="OnlineTestDoTest">
				<div class="body-result-1">
					<div class="total_point">
						<span class="color-orange size-18"><b>{LANG.diemso}</b></span>
						<b class="color-orange size-20">{DATA.score}/{DATA.max_score}</b>
					</div>
					<div class="result-question">
						<h3>{LANG.ketquathi}: {DATA.title}</h3>
						<div class="box-50">
							<p>
								{LANG.socaudalam}: <b style="margin-left: 40px;">{DATA.number_work} / {DATA.num_question} {LANG.num_question_title} </b> <br>
								{LANG.share_time_do_test}: <b style="margin-left: 22px;">{DATA.time_do_test} / {DATA.time} {LANG.share_minutes}</b>
							</p>
						</div>
						<p style="margin-top: 15px;margin-bottom: 0" class="sum-result">
							<span class="aw_correct"></span> {LANG.true}: <b>{DATA.number_success}</b>
							<span class="aw_not_correct"></span> {LANG.failed}: <b>{DATA.number_error}</b>
							<span class=""></span> {LANG.number_notans}: <b>{DATA.number_notans}</b>
						</p>
						
						<!-- BEGIN: config -->
						<p style="margin-top: 15px;margin-bottom: 0">
							<a class="download" href="javascript:void(0);" onclick="download_exam('{DATA.history_id}', '{DATA.token}')"><i class="fa fa-download" aria-hidden="true"></i> <strong>{LANG.download}</strong></a>					 
 						
							<!-- BEGIN: allow_show_answer -->
							<a class="download" href="{ANALYZED}" target="_blank"> <i class="fa fa-download" aria-hidden="true"></i><strong> {LANG.download_analyzed}</strong> </a>					 
							<!-- END: allow_show_answer -->
							
							<!-- BEGIN: allow_video -->
							<a class="download" href="javascript:void(0);" onclick="video_analyzed('{VIDEO_TYPE}','{VIDEO}', '{IMAGES}', '{DATA.title}')"><i class="fa fa-video-camera" aria-hidden="true"></i> <strong>{LANG.video}</strong></a>					 
							<div class="modal fade" id="ModalAddList" role="dialog">
								<div class="modal-dialog modal-lg">
								  <div class="modal-content">
									<div class="modal-header">
									  <button type="button" class="close" data-dismiss="modal">&times;</button>
									  <h4 class="modal-title"></h4>
									</div>
									<div class="modal-body">
									 
									</div>
							 
								  </div>  
								</div>
							</div>
							<link href="{NV_BASE_SITEURL}themes/{THEME}/css/onlinetest-video.css" rel="stylesheet">
							<link href="{NV_BASE_SITEURL}themes/{THEME}/css/onlinetest-video-config.css" rel="stylesheet">
							<script src="{NV_BASE_SITEURL}themes/{THEME}/js/onlinetest-video.js"></script>
							<script src="{NV_BASE_SITEURL}themes/{THEME}/js/onlinetest-video-youtube.js"></script>
							<!-- END: allow_video --> 
						</p> 
						<!-- END: config -->
						
					</div>
				</div>
				
				<div class="row">
					<div class="col-sm-18 col-md-18">
				
						<div id="showQuestions" class="boxtest">
							 <!-- BEGIN: pdf -->
							<div id="pdfviewoffset"></div> 
							<iframe id="pdfview" frameborder="0" height="1000" scrolling="yes" src="{PDFVIEW}" width="100%" style="border: 1px #ccc solid;"></iframe>					
							<!-- END: pdf -->
						</div>
					
					</div>
					<div class="col-sm-6 col-md-6">
						<div id="changetoanswer">
							<h2 class="titles">{LANG.list_answer}</h2>
							<div class="boxsgroup3 scrollbar" >	
								<ul class="list-group">
									<!-- BEGIN: loop -->
									<li class="list-group-item" id="item{QUESTION_ID}">
										<div class="qs"><strong>{LANG.pdf_question} {QUESTION_ID}:</strong></div>
										<div class="qs labelx">
											<!-- BEGIN: answers -->
											<label class="{ANSWERS.checked_class} {ANSWERS.trueanswer} {NOANSWER}"><span class="title">{ANSWERS.title}</span><input {ANSWERS.checked} type="checkbox" class="hide checkbox" name="answers[{QUESTION_ID}][answers][{ANSWERS.key}]" value="{ANSWERS.key}" data-id="{QUESTION_ID}"></label>
											<!-- END: answers -->
										</div>
										<!-- BEGIN: show_wrong -->
										<p>
											<i class="fa fa-times color-red" style="margin-right: 10px;margin-left: 5px;"></i> 
											<b class="color-red">{LISTTRUE.error} - </b> <b style="color: #8ca752 !important;">{LANG.trueanswers} {LISTTRUE.ans}</b>
										</p>
										<!-- END: show_wrong -->

									</li>
									<!-- END: loop -->
								</ul>
							</div>
						</div>
	 
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
 
		</div>
	</div>
</div>
<div class="clearfix"></div>
 
<script type="text/javascript"> 
$('#pdfview').css({height: $( window ).height() + 'px'});
$('#changetoanswer>.boxsgroup3').css({height: $( window ).height() + 'px'});
function delete_history(history_id, token) {
	if(confirm('{LANG.confirm}')) {
		$.ajax({
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=history&action=delete&nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'history_id=' + history_id + '&token=' + token + '&redirect=1',
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
<!-- END: main -->