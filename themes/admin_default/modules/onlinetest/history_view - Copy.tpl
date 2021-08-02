<!-- BEGIN: main -->
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
				<a href="{ADD_NEW}" data-toggle="tooltip" data-placement="top" title="{LANG.add_new}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i></a>
				<button onclick="delete_history('{DATA.history_id}', '{DATA.token}')" type="button" data-toggle="tooltip" data-placement="top" class="btn btn-danger btn-sm" id="button-delete" title="{LANG.delete}">
					<i class="fa fa-trash-o"></i>
				</button>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<div id="OnlineTestDoTest" class="OnlineTestDoTest">
				<div class="testTitle">
					<strong>Đề Thi: </strong><h1>{DATA.title}</h1>
					<div class="clearfix basicInfo">
					Mã: <span class="code">{DATA.code}</span> | Lượt xem:  <span class="code">{DATA.viewed}</span> | Ngày đăng:  <span class="code">{DATA.date_added}</span>
					</div>
 				</div>
				<div class="boxinfo">
					<div class="row">
						<div class="col-md-8 col-sm-12 fixed">
							Họ tên: <strong>{DATA.username}</strong>
						</div>
						<div class="col-md-8 col-sm-12 fixed">
							Thời gian làm bài: <strong>{DATA.time} (phút)</strong>
						</div>
						<div class="col-md-8 col-sm-12 fixed">
							Thời gian làm bài:<strong> {DATA.time_do_test}</strong>
						</div>
						
						<div class="col-md-8 col-sm-12 fixed">
							Số câu sai: <strong id="number-error">{DATA.number_error}</strong>
						</div>
						<div class="col-md-8 col-sm-12 fixed">
							Số câu đúng: <strong id="number-success">{DATA.number_success}</strong>
						</div>
						<div class="col-md-8 col-sm-12 fixed">
							Điểm đạt được: <strong id="number-total">{DATA.score}</strong>
						</div>		
					</div>
				</div>
				<div id="showQuestion" class="boxtest">
					<!-- BEGIN: loop -->
					<div class="test">
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
							<a class="analyzes" href="javascript:void(0);" data-question_id="{LOOP.question_id}" data-token="{LOOP.token}" > Xem lời giải</a> - 		
							<a class="comment" href="javascript:void(0);" data-question_id="{LOOP.question_id}" data-token="{LOOP.token}" ><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Bình luận (<span id="getcomment-{LOOP.question_id}">{LOOP.comment}</span>)</a>
						</div>
						<div id="analyzesList-{LOOP.question_id}" class="commentbox hide">{LOOP.analyzes}</div>
						<div id="commentList-{LOOP.question_id}" class="commentbox hide"> </div>
					</div>		 
					<!-- END: loop -->
				</div>
			</div> 
		</div>
	</div>
</div>
<div class="clearfix"></div>
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
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