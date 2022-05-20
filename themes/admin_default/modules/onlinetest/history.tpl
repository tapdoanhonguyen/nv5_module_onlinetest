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
				<button type="button" data-toggle="tooltip" data-placement="top" class="btn btn-danger btn-sm" id="button-delete" title="{LANG.delete}">
					<i class="fa fa-trash-o"></i>
				</button>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<div class="well">
				<div class="row">
					<form  action="{NV_BASE_ADMINURL}index.php" method="get" id="formsearch">
						<input type="hidden" name ="{NV_NAME_VARIABLE}"value="{MODULE_NAME}" />
						<input type="hidden" name ="{NV_OP_VARIABLE}"value="{OP}" />
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label" for="input-code"><strong>{LANG.history_code}</strong></label>
								<input type="text" name="code" value="{DATA.code}" placeholder="{LANG.history_code}" id="input-code" class="form-control btn-sm" autocomplete="off">								 
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label" for="inputs-title"><strong>{LANG.history_title}</strong></label>
								<input type="text" name="title" value="{DATA.title}" placeholder="{LANG.history_title}" id="inputs-title" class="form-control btn-sm" autocomplete="off">								 
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label">{LANG.history_username}
									<label style="margin: 0">
										<input type="radio" name="type" value="1" {TYPE_CHECKED_1} class="form-control" style="margin: 0" data-toggle="tooltip" title="{LANG.history_username_note}">
										<i class="fa fa-user" aria-hidden="true" style="cursor:pointer"></i>
									</label>
									<label style="margin: 0">
										<input type="radio" name="type" value="2" {TYPE_CHECKED_2} class="form-control"  style="margin: 0" data-toggle="tooltip" title="{LANG.history_full_name_note}">
										<i class="fa fa-user-circle-o" aria-hidden="true" style="cursor:pointer"></i>
									</label>
								</label>
								<input type="text" name="username" value="{DATA.username}" placeholder="{LANG.history_username}" id="input-username" class="form-control btn-sm" autocomplete="off">								 
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label" for="inputs-group"><strong>{LANG.history_group}</strong></label>
								
								<select name="group" id="input-group" class="form-control select2">	
									<option value="0" >{LANG.history_group_select}</option>
									<!-- BEGIN: group -->
									<option value="{GROUP.key}" {GROUP.selected}>{GROUP.name}</option>
									<!-- END: group -->
								</select>
							</div>
						</div>
				
						<div class="col-sm-6">
							<span style="display: block;position: relative;padding-top: 24px;">
								<input type="hidden" value="{TOKEN}" name="token"/>
								<button type="submit" class="btn btn-primary btn-sm" > <i class="fa fa-search"></i> Tìm kiếm </button>
								<button class="btn btn-info export_file"> <i class="fa fa-download"></i> Xuất tìm kiếm  </button>
							</span>
						</div> 
					</form>
 
				</div>
			</div>
			<form action="#" method="post" enctype="multipart/form-data" id="form-history">
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<td class="col-sm-0 text-center"><input name="check_all[]" type="checkbox" value="yes" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"> </td>
								<td class="col-sm-2 text-center"><a {CODE_ORDER} href="{URL_CODE}"><strong>{LANG.history_code}</strong></a> </td>
								<td class="col-sm-4 text-left"><a {TITLE_ORDER} href="{URL_TITLE}"><strong>{LANG.history_title}</strong></a> </td>
								<td class="col-sm-3 text-center"><a {USERNAME_ORDER} href="{URL_USERNAME}"><strong>{LANG.history_username}</strong></a> </td>
								<td class="col-sm-3 text-center"><a {FULL_NAME_ORDER} href="{URL_FULL_NAME}"><strong>{LANG.history_full_name}</strong></a> </td>
								<td class="col-sm-3 text-center"><a {GROUP_ORDER} href="{URL_GROUP}"><strong>{LANG.history_group}</strong></a> </td>
								<td class="col-sm-2 text-center"><a {SCORE_ORDER} href="{URL_SCORE}"><strong>{LANG.history_score}</strong></a> </td>
								<td class="col-sm-3 text-center"><a {TEST_TIME_ORDER} href="{URL_TEST_TIME}"><strong>{LANG.history_test_time}</strong></a> </td>
								<td class="col-sm-2 text-center"><a {IS_DELETED_ORDER} href="{URL_IS_DELETED}"><strong>{LANG.history_status}</strong></a> </td>
								<td class="col-sm-2 text-center"> <strong>{LANG.action} </strong></td>
							</tr>
						</thead>
						<tbody>
							 <!-- BEGIN: loop --> 
							<tr id="group_{LOOP.history_id}">
								<td class="text-center">
									<input type="checkbox" name="selected[]" value="{LOOP.history_id}">
								</td>
								<td class="text-center">
									<!-- BEGIN: code -->
									<a href="{LOOP.view}">{LOOP.code}</a>
									<!-- END: code -->
									<!-- BEGIN: nocode -->
									<span class="notexist">{LANG.history_not_exist_code}</span>
									<!-- END: nocode -->
								</td>
								<td class="text-left">
									<!-- BEGIN: title -->
									<a href="{LOOP.view}">{LOOP.title}</a>
									<!-- END: title -->
									<!-- BEGIN: notitle -->
									<span class="notexist">{LANG.history_not_exist_title} </span>
									<!-- END: notitle -->
								</td>
								<!-- BEGIN: username -->
								<td class="text-center">								
									<a href="{LOOP.userlink}" target="_blank">{LOOP.username}</a>	 
								</td>	
								<td class="text-center">
									<a href="{LOOP.userlink}" target="_blank">{LOOP.full_name}</a>		 
								</td>												
								<!-- END: username -->								
								<!-- BEGIN: nousername -->
								<td class="text-center">
									<span class="notexist">{LANG.history_not_exist_username} </span> 
								</td>	
								<td class="text-center"></td>												
								<!-- END: nousername -->								
										
								<td class="text-center">
									<a href="{LOOP.grouplink}" target="_blank">{LOOP.group}</a>
								</td> 
								<td class="text-center">
									{LOOP.score} 
								</td> 
								<td class="text-center">
									{LOOP.test_time} 
								</td> 
								<td class="text-center">
									<!-- BEGIN: is_sended0 -->
									<span class="btn btn-info btn-xs" style="width:70px">{LANG.history_is_sended0}</span>
									<!-- END: is_sended0 -->
									<!-- BEGIN: is_deleted1 -->
									<span class="btn btn-danger btn-xs" style="width:70px">{LANG.history_is_deleted1}</span>
									<!-- END: is_deleted1 -->
									<!-- BEGIN: is_deleted0 -->
									<span class="btn btn-success btn-xs" style="width:70px">{LANG.history_is_deleted0}</span>
									<!-- END: is_deleted0 -->
								</td> 
								<td class="text-center">
									<!-- BEGIN: view -->
									<a href="{LOOP.view}" data-toggle="tooltip" class="btn btn-primary btn-xs" title="{LANG.view}"><i class="fa fa-eye"></i></a>
									<!-- END: view -->
									<!-- BEGIN: noview -->
									<a href="javascript:void(0);" data-toggle="tooltip" class="btn btn-primary btn-xs disabled" title="{LANG.view}"><i class="fa fa-eye"></i></a>
									<!-- END: noview -->
									<a href="javascript:void(0);" onclick="delete_history('{LOOP.history_id}', '{LOOP.token}')" data-toggle="tooltip" title="{LANG.delete}" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i>
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
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
 
<script type="text/javascript"> 

function delete_history(history_id, token) {
	if(confirm('{LANG.confirm}')) {
		$.ajax({
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=history&action=delete&nocache=' + new Date().getTime(),
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
				
				if (json['success']) {
					$('#history-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
					 $.each(json['id'], function(i, id) {
						$('#group_' + id ).remove();
					});
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
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=history&action=delete&nocache=' + new Date().getTime(),
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
					$('#history-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
					 $.each(json['id'], function(i, id) {
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
 
$('.export_file').on('click', function(e) {
 
	$.ajax({
		url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=history&action=is_download&nocache=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data: {token: '{TOKEN}'},
		beforeSend: function() {
			$('.export_file i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
			$('.export_file').prop('disabled', true);
		},	
		complete: function() {
			$('.export_file i').replaceWith('<i class="fa fa-download"></i>');
			$('.export_file').prop('disabled', false);
		},
		success: function(json) {
			if( json['error'] ) alert( json['error'] );  		
			if( json['link'] ) window.location.href= json['link'];  		
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	e.preventDefault(); 	
}); 
 
</script>
<!-- END: main -->