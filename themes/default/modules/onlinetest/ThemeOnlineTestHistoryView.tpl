<!-- BEGIN: main -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.7&appId={CONFIG.facebook_appid}";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div id="shareLink">
	<div id="facebookshare" class="fb-like" data-href="{DATA.shareLink}" data-layout="button_count" data-action="like" data-size="small" data-show-faces="false" data-share="true"></div>
</div>
<div id="print">
<a href="#" onclick="open_popup('{PRINT}')"><i class="fa fa-print" aria-hidden="true"></i> In bài thi</a>
</div>
<div class="clearfix"></div>
 
<div id="history-content">
	<!-- BEGIN: success -->
		<div class="alert alert-success">
			<i class="fa fa-check-circle"></i> {SUCCESS}<i class="fa fa-times"></i>
		</div>
	<!-- END: success -->
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-list"></i> {LANG.history_view}</h3> 
			<div class="pull-right">
				<button type="button" data-toggle="tooltip" data-placement="top" class="btn btn-danger" id="button-delete" onclick="delete_history('{DATA.history_id}', '{DATA.token}' )" title="{LANG.delete}">
					<i class="fa fa-trash-o"></i>
				</button>
				<a href="{BACK}" data-toggle="tooltip" class="btn btn-default default" title="{LANG.back}"><i class="fa fa-reply"></i></a>
			
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<div id="OnlineTestDoTest" class="OnlineTestDoTest">
				<div class="testTitle">
					<strong>{LANG.exam}: </strong><h1>{DATA.title}</h1>
					<div class="clearfix basicInfo">
					{LANG.code}: <span class="code">{DATA.code}</span> | {LANG.viewed}:  <span class="code">{DATA.viewed}</span> | {LANG.date_added}:  <span class="code">{DATA.date_added}</span>
					</div>
 				</div>
				<div class="boxinfo">
					<div class="row">		 
						<div class="col-md-8 col-sm-12 fixed">
							{LANG.full_name}: <strong>{DATA.username}</strong>
						</div>
						<div class="col-md-8 col-sm-12 fixed">
							{LANG.time_test}: <strong>{DATA.time} ({LANG.minutes})</strong>
						</div>
						<div class="col-md-8 col-sm-12 fixed">
							{LANG.share_time_complete}:<strong> {DATA.time_do_test}</strong>
						</div>
						
						<div class="col-md-8 col-sm-12 fixed">
							{LANG.result_wrong}: <strong id="number-error">{DATA.number_error}</strong>
						</div>
						<div class="col-md-8 col-sm-12 fixed">
							{LANG.result_right}: <strong id="number-success">{DATA.number_success}</strong>
						</div>
						<div class="col-md-8 col-sm-12 fixed">
							{LANG.setpoint}: <strong id="number-total">{DATA.score}</strong>
						</div>		
					</div>
				</div>
				<div id="showQuestions" class="boxtest">
					<!-- BEGIN: loop -->
					<div class="test">
						<input type="hidden" value="2" name="answers[{LOOP.question_id}][question_id]" disabled="disabled">
						<div class="question" id="question{LOOP.question_id}">{LANG.question} {LOOP.stt}: {LOOP.question}</div>	
						<!-- BEGIN: answers -->
						<label class="{ANSWERS.checked_class} {ANSWERS.trueanswer}"><input class="checkbox" type="checkbox" value="{ANSWERS.key}" name="answers[{LOOP.question_id}][answers][{ANSWERS.key}]" id="answer-{LOOP.question_id}-{ANSWERS.key}" disabled="" {ANSWERS.checked}> {ANSWERS.name} </label>	            
						<!-- END: answers -->
						<!-- BEGIN: show_answer -->
						<div class="reporth">
							<a class="analyzes" href="javascript:void(0);" data-question_id="{LOOP.question_id}" data-token="{LOOP.token}" data-login="{LOGIN}"> {LANG.view_answers}</a> - 		
							<a class="report" href="javascript:void(0);" data-question_id="{LOOP.question_id}" data-token="{LOOP.token}" data-login="{LOGIN}"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Báo lỗi</a> - 
							<a class="comment" href="javascript:void(0);" data-question_id="{LOOP.question_id}" data-token="{LOOP.token}" ><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> {LANG.comment} (<span id="getcomment-{LOOP.question_id}">{LOOP.comment}</span>)</a>
						</div>
						<div id="analyzesList-{LOOP.question_id}" class="commentbox hide">{LOOP.analyzes}</div>
						<div id="commentList-{LOOP.question_id}" class="commentbox hide"> </div>
						<!-- END: show_answer -->
					</div>		 
					<!-- END: loop -->
				</div>
			</div> 
		</div>
	</div>
</div>
<div class="clearfix"></div>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_EDITORSDIR}/ckeditor/ckeditor.js"></script>
 
<script type="text/javascript"> 
function delete_history(history_id, token) {
	if(confirm('{LANG.confirm}')) {
		$.ajax({
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=history&action=delete&second=' + new Date().getTime(),
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
				$('#button-delete i').replaceWith('<i class="fa fa-trash-o"></i>');
				$('#button-delete').prop('disabled', false);
			}
		});
	}
}
function open_popup( url )
{
 	nv_open_browse( url, 'Print', $(document).width(), $(document).height(), "resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
	return false;

} 
</script>
<!-- END: main -->