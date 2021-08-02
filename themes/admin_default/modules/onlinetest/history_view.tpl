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
					<!-- <div class="visible-xs" style="display: inline-block; width: calc(100% - 140px); ">
						<p><b>Kết quả bài thi: {DATA.title}</b></p>
					</div> -->
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
						<p style="margin-top: 15px;margin-bottom: 0" class="sum-result">
							<a class="download" href="javascript:void(0);" onclick="download_exam('{DATA.history_id}', '{DATA.token}')"><i class="fa fa-download" aria-hidden="true"></i> <strong>{LANG.download}</strong></a>					 
						</p>
					</div>
				</div>
				<div class="body-result-2 list-question-number2 toggle_list_q">
					<ul>
						<!-- BEGIN: loop_num_question --> 
						<li class="q_{QUESTION_ID}"><a class="{CLASS}" onclick="showQuestion('question{QUESTION_ID}')" href="#question{QUESTION_ID}">{NUM}</a></li>
						<!-- END: loop_num_question -->
					</ul>
				</div>
				<div id="showQuestions" class="boxtest">
					<!-- BEGIN: loop -->
					<div class="test" id="question{LOOP.question_id}">
						<p class="question-info">
							<b>{LANG.question_stt} {LOOP.stt}</b>
							<span class="level-question level-{LOOP.level_id}">{LOOP.level}</span>
							<!--  
							<a class="fr btn-feedback btn-onclick report" data-question_id="{LOOP.question_id}" data-token="{LOOP.token}" data-login>
								<img class="not-hover" src="{NV_BASE_SITEURL}themes/{THEME}/images/onlinetest/icon-feedback.png">
								<img class="hover" src="{NV_BASE_SITEURL}themes/{THEME}/images/onlinetest/icon-feedback_hover.png">
								<span>{LANG.report}</span>
							</a>
							-->
						</p>
						<div class="question" id="question{LOOP.question_id}">{LOOP.question}</div>	
						<!-- BEGIN: answers -->
							<label class="{ANSWERS.checked_class} {ANSWERS.trueanswer}"><span class="title">{ANSWERS.title}</span><input class="hide checkbox" type="checkbox" value="{ANSWERS.key}" id="answer-{LOOP.question_id}-{ANSWERS.key}" disabled="disabled" {ANSWERS.checked}> {ANSWERS.name} </label>	            
						<!-- END: answers -->
						
						<!-- BEGIN: show_wrong -->
						<p>
							<i class="fa fa-times color-red" style="margin-right: 10px;margin-left: 5px;"></i> 
							<b class="color-red">{LISTTRUE.error} - </b> <b style="color: #8ca752 !important;">{LANG.trueanswers} {LISTTRUE.ans}</b>
						</p>
						<!-- END: show_wrong -->
						
						<div class="reporth">
							<a class="analyzes" href="javascript:void(0);" data-question_id="{LOOP.question_id}" data-token="{LOOP.token}" data-login="{LOGIN}" > {LANG.view_answers}</a> - 		
							<a class="comment" href="javascript:void(0);" data-question_id="{LOOP.question_id}" data-token="{LOOP.token}" ><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> {LANG.comment} (<span id="getcomment-{LOOP.question_id}">{LOOP.comment}</span>)</a>
						</div>
						<!-- BEGIN: view_analyzes2 -->
						<div id="analyzesList-{LOOP.question_id}" class="commentbox hide">{ANALYZES}</div>
						<!-- END: view_analyzes2 -->
						<div id="commentList-{LOOP.question_id}" class="commentbox hide"> </div>
					</div>		 
					<!-- END: loop -->
				</div>
				
			</div>
			<div class="clearfix"></div>
 
		</div>
	</div>
</div>
<div class="clearfix"></div>
<script  type="text/javascript" >
MathJax = {
  tex: {
    inlineMath: [['$', '$'], ['\\(', '\\)']]
  }
};
$(document).ready(function(){
	setTimeout(function(){$('.math-tex').show()}, 400)
})
</script>
<script type="text/javascript" async  src="{NV_BASE_SITEURL}MathJax-2.7.8/MathJax.js?config=TeX-AMS_HTML"></script>  
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_EDITORSDIR}/ckeditor/ckeditor.js"></script>

<script type="text/javascript"> 
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