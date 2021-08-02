<!-- BEGIN: main -->
<div id="type_exam-content">
	<!-- BEGIN: success -->
		<div class="alert alert-success">
			<i class="fa fa-check-circle"></i> {SUCCESS}<i class="fa fa-times"></i>
		</div>
	<!-- END: success -->
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-list"></i> {LANG.type_exam_list}</h3> 
			<div class="pull-right">
				<a href="{ADD_NEW}" data-toggle="tooltip" data-placement="top" title="{LANG.add_new}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i></a>
				<button type="button" data-toggle="tooltip" data-placement="top" class="btn btn-danger btn-sm" id="button-delete" title="{LANG.delete}">
					<i class="fa fa-trash-o"></i>
				</button>
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
								<label class="control-label" for="inputs-title">{LANG.type_exam_title}</label>
								<input type="text" name="title" value="{DATA.title}" placeholder="{LANG.type_exam_title}" id="inputs-title" class="form-control btn-sm" autocomplete="off">								 
							</div>
						</div>
						<div class="col-sm-8">
							<div class="form-group">
								<label class="control-label" for="input-category">{LANG.type_exam_category}</label>
								<select name="category_id"  class="form-control select2 btn-sm">
									<option value="0"> {LANG.type_exam_category_select} </option>
									<!-- BEGIN: category -->
									<option value="{CATEGORY.key}" {CATEGORY.selected} > {CATEGORY.name} </option>
									<!-- END: category -->
								</select>
							</div>
						</div>
						 
						<div class="col-sm-8">
							<div class="form-group">
								<label class="control-label" for="input-status">{LANG.type_exam_status}</label>
								<select name="status" class="form-control  btn-sm">
									<option value=""> {LANG.type_exam_status} </option>
									<!-- BEGIN: status -->
									<option value="{STATUS.key}" {STATUS.selected} > {STATUS.name} </option>
									<!-- END: status -->
								</select>  
							</div>
						</div>
 
						<div class="col-sm-8">
							<span>
								<input type="hidden" value="{TOKEN}" name="token"/>
								<button type="submit" class="btn btn-primary" > <i class="fa fa-search"></i> Tìm kiếm </button>
								<button class="btn btn-info export_file"> <i class="fa fa-download"></i> Xuất toàn bộ dữ liệu thi </button>
							</span>
						</div>
						
						
					</form>
 
				</div>
			</div>
			<form action="#" method="post" enctype="multipart/form-data" id="form-type_exam">
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<td class="col-sm-0 text-center"><input name="check_all[]" type="checkbox" value="yes" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"> </td>
								<td class="col-sm-10 text-center"><a {TITLE_ORDER} href="{URL_TITLE}"><strong>{LANG.type_exam_title}</strong></a> </td>
								<td class="col-sm-5 text-center"><a {DESCRIPTION_ORDER} href="{URL_DESCRIPTION}"><strong>{LANG.type_exam_description}</strong></a> </td>
								<td class="col-sm-2 text-center"><a {TIME_ORDER} href="{URL_TIME}"><strong>{LANG.type_exam_time}</strong></a> </td>
								<td class="col-sm-2 text-center"><a {POINT_ORDER} href="{URL_POINT}"><strong>{LANG.type_exam_point}</strong></a> </td>
								<td class="col-sm-2 text-center"><a {STATUS_ORDER} href="{URL_STATUS}"><strong>{LANG.type_exam_status}</strong></a> </td>
								<td class="col-sm-3 text-center"> <strong>{LANG.action} </strong></td>
							</tr>
						</thead>
						<tbody>
							 <!-- BEGIN: loop --> 
							<tr id="group_{LOOP.type_exam_id}">
								<td class="text-center">
									<input type="checkbox" name="selected[]" value="{LOOP.type_exam_id}">
								</td>
								<td class="text-left">
									{LOOP.title} 
								</td>
								<td class="text-left">
									{LOOP.description} 
								</td> 
								<td class="text-center">
									{LOOP.time} 
								</td> 
								<td class="text-center">
									{LOOP.point} 
								</td> 
								 
								<td class="text-center">
									<input name="status" value="1" type="checkbox" data-token="{LOOP.token}" data-type_exam_id="{LOOP.token}" {LOOP.status_checked}>
								</td>
								<td class="text-center">
									<a href="javascript:void(0);" onclick="download_type_exam(this, '{LOOP.type_exam_id}', '{LOOP.token}')"  data-toggle="tooltip" title="{LANG.ranking_download}" class="btn btn-info btn-sm"><i class="fa fa-download"></i></a>
									<a href="{LOOP.edit}" data-toggle="tooltip" title="{LANG.edit}" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
									<a href="javascript:void(0);" onclick="delete_type_exam('{LOOP.type_exam_id}', '{LOOP.token}')" data-toggle="tooltip" title="{LANG.delete}" class="btn btn-danger  btn-sm"><i class="fa fa-trash-o"></i>
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

$('.select2').select2({language: '{NV_LANG_INTERFACE}'}); 
 
function delete_type_exam(type_exam_id, token) {
	if(confirm('{LANG.confirm}')) {
		$.ajax({
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=typeexam&action=delete&nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'type_exam_id=' + type_exam_id + '&token=' + token,
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
					$('#type_exam-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
				}
				
				if (json['success']) {
					$('#type_exam-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
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

$('.formajax').on('change', function() {
	var action = $(this).attr('data-action');
	var token = $(this).attr('data-token');
	var type_exam_id = $(this).attr('data-id');
	var new_vid = $(this).val();
	var id = $(this).attr('id');
	$.ajax({
		url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=typeexam&nocache=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data: 'action=' + action + '&type_exam_id=' + type_exam_id + '&new_vid=' + new_vid + '&token='+token,
		beforeSend: function() {
			$('#'+id ).prop('disabled', true);
			$('.alert').remove();
		},	
		complete: function() {
			$('#'+id ).prop('disabled', false);
		},
		success: function(json) {
			
			if ( json['error'] ) alert( json['error'] );	
			if ( json['link'] ) location.href = json['link'];
 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});
 
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
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=typeexam&action=delete&nocache=' + new Date().getTime(),
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
					$('#type_exam-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
				}
				
				if (json['success']) {
					$('#type_exam-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
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


function download_type_exam(obj, type_exam_id, token) {
 
 
	$.ajax({
		url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=typeexam&action=is_download&nocache=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data: 'type_exam_id=' + type_exam_id + '&token=' + token,
		beforeSend: function() {
			$(obj).find('i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
			$(obj).prop('disabled', true);
		},	
		complete: function() {
			$(obj).find('i').replaceWith('<i class="fa fa-download"></i>');
			$(obj).prop('disabled', false);
		},
		success: function(json) {
			if( json['error'] ) alert( json['error'] );  		
			if( json['link'] ) window.location.href= json['link'];  			
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
 
}

$('.export_file').on('click', function(e) {
 
	$.ajax({
		url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=typeexam&action=is_download2&nocache=' + new Date().getTime(),
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