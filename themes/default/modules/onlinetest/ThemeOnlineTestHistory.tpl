<!-- BEGIN: main -->
<div id="history-content">
	<!-- BEGIN: success -->
		<div class="alert alert-success">
			<i class="fa fa-check-circle"></i> {SUCCESS}<i class="fa fa-times"></i>
		</div>
	<!-- END: success -->
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-list"></i> {LANG.history_list}</h3> 
			<div class="pull-right">
			
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<div class="well">
				<div class="row">
					<form  action="{NV_BASE_SITEURL}index.php" method="get" id="formsearch">
						<input type="hidden" name ="{NV_NAME_VARIABLE}"value="{MODULE_NAME}" />
						<input type="hidden" name ="{NV_OP_VARIABLE}"value="{OP}" />
						<div class="col-sm-8">
							<div class="form-group">
								<label class="control-label" for="input-code"><strong>{LANG.history_code}</strong></label>
								<input type="text" name="code" value="{DATA.code}" placeholder="{LANG.history_code}" id="input-code" class="form-control" autocomplete="off">								 
							</div>
						</div>
						<div class="col-sm-8">
							<div class="form-group">
								<label class="control-label" for="input-title"><strong>{LANG.history_title}</strong></label>
								<input type="text" name="title" value="{DATA.title}" placeholder="{LANG.history_title}" id="input-title" class="form-control" autocomplete="off">								 
							</div>
						</div>
		 
 
						<div class="col-sm-8">
							<span style="display: block;position: relative;">
							<input type="hidden" value="{TOKEN}" name="token"/>
							<button type="submit" class="btn btn-primary" > <i class="fa fa-search"></i> {LANG.search} </button>
							</span>
						</div> 
					</form>
 
				</div>
			</div>
			<div class="pull-right" style="margin-bottom:10px">
				<button type="button" data-toggle="tooltip" data-placement="top" class="btn btn-danger" id="button-delete" title="{LANG.delete}">
					<i class="fa fa-trash-o"></i>
				</button>
			</div>
			<div style="clear:both"></div>
			<form action="#" method="post" enctype="multipart/form-data" id="form-history">
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<td class="col-sm-0 text-center"><input name="check_all[]" type="checkbox" value="yes" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"> </td>
								<td class="col-sm-0 text-center"> <strong>{LANG.history_stt}</strong></td>
								<td class="col-sm-4 text-center"><a {CODE_ORDER} href="{URL_CODE}"><strong>{LANG.history_code}</strong></a> </td>
								<td class="col-sm-8 text-center"><a {TITLE_ORDER} href="{URL_TITLE}"><strong>{LANG.history_title}</strong></a> </td>
								<td class="col-sm-3 text-center"><a {SCORE_ORDER} href="{URL_SCORE}"><strong>{LANG.history_score}</strong></a> </td>
								<td class="col-sm-3 text-center"><a {TEST_TIME_ORDER} href="{URL_TEST_TIME}"><strong>{LANG.history_test_time}</strong></a> </td>
								<td class="col-sm-3 text-center"><a {POINT_ORDER} href="{URL_POINT}"><strong>{LANG.history_point}</strong></a> </td>
								<td class="col-sm-3 text-center"> <strong>{LANG.action} </strong></td>
							</tr>
						</thead>
						<tbody>
							 <!-- BEGIN: loop --> 
							<tr id="group_{LOOP.history_id}">
								<td class="text-center">
									<input type="checkbox" name="selected[]" value="{LOOP.history_id}">
								</td>
								<td class="text-center">
									 {LOOP.stt}
								</td>
								<td class="text-left">
									{LOOP.code}
								</td>
								<td class="text-left">
									{LOOP.title}
								</td>
								 					
								<td class="text-center">
									<!-- BEGIN: score -->
									{LOOP.score} 
									<!-- END: score -->
									<!-- BEGIN: testing1 -->
									{LANG.testing} 
									<!-- END: testing1 -->
								
								</td> 
								<td class="text-center">
									{LOOP.testtime} 
								</td> 
								<td class="text-center">
									{LOOP.point} 
								</td> 
								<td class="text-center">
									<!-- BEGIN: view -->
									<a href="{LOOP.view}" data-toggle="tooltip" class="btn btn-primary btn-xs" title="{LANG.view}"><i class="fa fa-eye"></i></a>
									<!-- END: view -->
									<!-- BEGIN: testing -->
									<a href="javascript:void(0);" id="continue{LOOP.history_id}" onclick="continue_exam('{LOOP.history_id}', '{LOOP.token}')" data-toggle="tooltip" class="btn btn-primary btn-xs" title="{LANG.continue_the_exam}"><i class="fa fa-arrow-right"></i></a>
									<!-- END: testing -->
									<a href="javascript:void(0);" onclick="delete_history('{LOOP.history_id}', '{LOOP.token}')" id="delete-{LOOP.history_id}" data-toggle="tooltip" title="{LANG.delete}" class="btn btn-danger  btn-xs"><i class="fa fa-trash-o"></i>

								</td>
							</tr>
							 <!-- END: loop -->
						</tbody>
					</table>
				</div>
			</form>
			<!-- BEGIN: generate_page -->
			<div class="row">
				<div class="col-sm-24 text-center">
				{GENERATE_PAGE}			
				</div>
			</div>
			<!-- END: generate_page -->
		</div>
	</div>
</div>
<script type="text/javascript"> 
function continue_exam(history_id, token) {
 
	$.ajax({
		url:  nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=history&action=continue&second=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data: 'history_id=' + history_id + '&token=' + token,
		beforeSend: function() {
			$('#continue'+history_id+' i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>'); 
			$('#continue'+history_id+'').prop('disabled', true);  
		},	
		complete: function() {
			$('#continue'+history_id+' i').replaceWith('<i class="fa fa-arrow-right"></i>');
			$('#continue'+history_id+'').prop('disabled', false);
		},
		success: function(json) {
			$('.alert').remove();

			if (json['error']) {
				$('#history-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
			}
			else if (json['link']) 
			{
				location.href= json['link'];
			}		
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	 
}
function delete_history(history_id, token) {
	if(confirm('{LANG.confirm}')) {
		$.ajax({
			url:  nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=history&action=delete&second=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'history_id=' + history_id + '&token=' + token,
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

				if (json['error']) {
					$('#history-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
				}
				else if (json['success']) 
				{
					
					$('#history-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<i class="fa fa-times"></i></div>');
					 $.each(json['id'], function(i, id) {					
						var describedby = $('#delete-'+ id).attr('aria-describedby');
						console.log(describedby);
						$('#' + describedby).remove();
						$('#group_' + id ).remove();
					});
					
					
					
				}	
				else if (json['link']) {
					location.href= json['link'];
				}		
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}
 
 
$('#button-delete').on('click', function() {
	if(confirm('{LANG.confirm}')) 
	{
		var listid = [];
		$("input[name=\"selected[]\"]:checked").each(function() {
			listid.push($(this).val());
		});
		if (listid.length < 1) {
			alert("{LANG.please_select_one}");
			return false;
		}
	 
		$.ajax({
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=history&action=delete&second=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'listid=' + listid + '&token={TOKEN}',
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
 
				if (json['error']) {
					$('#history-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
				}
				
				if (json['success']) {
					$('#history-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<i class="fa fa-times"></i></div>');
					 $.each(json['id'], function(i, id) {
						var describedby = $('#delete-'+ id).attr('aria-describedby');
						console.log(describedby);
						$('#' + describedby).remove();
						$('#group_' + id ).remove();
					});
				}		
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}	
});
 
</script> 
<!-- END: main -->