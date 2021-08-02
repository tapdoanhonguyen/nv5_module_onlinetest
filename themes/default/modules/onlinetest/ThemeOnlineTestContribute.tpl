<!-- BEGIN: main -->
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">

<div id="question-content">
	<!-- BEGIN: success -->
		<div class="alert alert-success">
			<i class="fa fa-check-circle"></i> {SUCCESS}<i class="fa fa-times"></i>
		</div>
	<!-- END: success -->
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-list"></i> {LANG.question_list}</h3> 
			<div class="pull-right">
				<!-- BEGIN: create -->
				<a href="{ADD_NEW}" data-toggle="tooltip" data-placement="top" title="{LANG.add_new}" class="btn btn-success"><i class="fa fa-plus"></i></a>
				<!-- END: create -->
				<!-- BEGIN: delete -->
				<button type="button" data-toggle="tooltip" data-placement="top" class="btn btn-danger" id="button-delete" title="{LANG.delete}">
					<i class="fa fa-trash-o"></i>
				</button>
				<!-- END: delete -->
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<div class="well">
				<div class="row">
					<form  action="{NV_BASE_SITEURL}index.php" method="get" id="formsearch">
						<input type="hidden" name ="{NV_NAME_VARIABLE}"value="{MODULE_NAME}" />
						<input type="hidden" name ="{NV_OP_VARIABLE}"value="{OP}" />
						<div class="col-sm-8 col-md-8">
							<div class="form-group">
								<label class="control-label" for="input-question">{LANG.question_question}</label>
								<input type="text" name="question" value="{DATA.question}" placeholder="{LANG.question_question}" id="input-question" class="form-control" autocomplete="off">								 
							</div>
						</div>
						<div class="col-sm-8 col-md-8">
							<div class="form-group">
								<label class="control-label" for="input-category">{LANG.question_category}</label>
								<select name="category_id"  class="form-control">
									<option value="0"> {LANG.question_category_select} </option>
									<!-- BEGIN: category -->
									<option value="{CATEGORY.key}" {CATEGORY.selected} > {CATEGORY.name} </option>
									<!-- END: category -->
								</select>
							</div>
						</div>
						<div class="col-sm-8 col-md-8">
							<div class="form-group">
								<label class="control-label" for="input-level">{LANG.question_level}</label>
								<select name="level_id" class="form-control">
									<option value="0"> {LANG.question_level_select} </option>
									<!-- BEGIN: level -->
									<option value="{LEVEL.key}" {LEVEL.selected} > {LEVEL.name} </option>
									<!-- END: level -->
								</select>  
							</div>
						</div>
						<div class="col-sm-8 col-md-8">
							<div class="form-group">
								<label class="control-label" for="input-status">{LANG.question_status}</label>
								<select name="status" class="form-control">
									<option value=""> {LANG.question_status} </option>
									<!-- BEGIN: status -->
									<option value="{STATUS.key}" {STATUS.selected} > {STATUS.name} </option>
									<!-- END: status -->
								</select>  
							</div>
						</div>
						<div class="col-sm-8 col-md-8">
							<div class="form-group">
								<label class="control-label clear" for="input-date_added">{LANG.question_date_added}</label>
								<div class="clear"></div>
								<input type="text" name="date_from" value="{DATA.date_from}" id="date_from"  placeholder="{LANG.question_date_from}" class="form-control" autocomplete="off" style="display:inline-block;width:100px"> <strong>:</strong>
								<input type="text" name="date_to" value="{DATA.date_to}" id="date_to" placeholder="{LANG.question_date_to}" class="form-control" autocomplete="off" style="display:inline-block;width:100px">
							</div> 
						</div>
						<div class="col-sm-8 col-md-8">
							<span style="display: block;position: relative;padding-top: 24px;">
							<input type="hidden" value="{TOKEN}" name="token"/>
							<button type="submit" class="btn btn-primary" > <i class="fa fa-search"></i> {LANG.search} </button>
							</span>
						</div> 
					</form>
 
				</div>
			</div>
			<form action="#" method="post" enctype="multipart/form-data" id="form-question">
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<td class="col-sm-0 text-center"><input name="check_all[]" type="checkbox" value="yes" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"> </td>
								<td class="col-sm-10 col-md-10 text-center"><a {QUESTION_ORDER} href="{URL_QUESTION}"><strong>{LANG.question_question}</strong></a> </td>
								<td class="col-sm-3 col-md-3 text-center"><a {LEVEL_ORDER} href="{URL_LEVEL}"><strong>{LANG.question_level}</strong></a> </td>
								<td class="col-sm-3 col-md-3 text-center"><a {DATE_ADDED_ORDER} href="{URL_DATE_ADDED}"><strong>{LANG.question_date_added}</strong></a> </td>
								<td class="col-sm-3 col-md-3 text-center"><a {DATE_MODIFIED_ORDER} href="{URL_DATE_MODIFIED}"><strong>{LANG.question_date_modified}</strong></a> </td>
								<td class="col-sm-2 col-md-2 text-center"><a {STATUS_ORDER} href="{URL_STATUS}"><strong>{LANG.question_status}</strong></a> </td>
								<td class="col-sm-3 col-md-3 text-center"> <strong>{LANG.action} </strong></td>
							</tr>
						</thead>
						<tbody>
							 <!-- BEGIN: loop --> 
							<tr id="group_{LOOP.question_id}">
								<td class="text-center">
									<input type="checkbox" name="selected[]" value="{LOOP.question_id}">
								</td>
								<td class="text-left">
									{LOOP.question} 
								</td>
								<td class="text-center">
									{LOOP.level_name} 
								</td> 
								<td class="text-center">
									{LOOP.date_added} 
								</td>
								<td class="text-center">
									{LOOP.date_modified} 
								</td>
								<td class="text-center">
									<input name="status" value="1" type="checkbox" {LOOP.status_checked} disabled="disabled">								
								</td>
								<td class="text-center">
									<!-- BEGIN: modified -->
									<a href="{LOOP.edit}" data-toggle="tooltip" title="{LANG.edit}" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
									<!-- END: modified -->
									<!-- BEGIN: delete -->						
									<a href="javascript:void(0);" onclick="delete_question('{LOOP.question_id}', '{LOOP.token}')" data-toggle="tooltip" title="{LANG.delete}" class="btn btn-danger"><i class="fa fa-trash-o"></i>
									<!-- END: delete -->
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
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>

<script type="text/javascript">

$("#date_from,#date_to").datepicker({
	showOn : "both",
	dateFormat : "dd/mm/yy",
	changeMonth : true,
	changeYear : true,
	showOtherMonths : true,
	buttonImage : nv_base_siteurl + "assets/images/calendar.gif",
	buttonImageOnly : true
}); 

function delete_question(question_id, token) {
	if(confirm('{LANG.confirm}')) {
		$.ajax({
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=contribute&action=delete&second=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'question_id=' + question_id + '&token=' + token,
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
					$('#question-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
				}
				
				if (json['success']) {
					$('#question-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
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
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=contribute&action=delete&second=' + new Date().getTime(),
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
					$('#question-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
				}
				
				if (json['success']) {
					$('#question-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
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
</script>
<!-- END: main -->