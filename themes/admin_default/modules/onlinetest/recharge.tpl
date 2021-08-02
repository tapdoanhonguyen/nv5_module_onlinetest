<!-- BEGIN: main -->
<div id="recharge-content"> 
	<!-- BEGIN: error_warning -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {error_warning}<i class="fa fa-times"></i>        
    </div>
    <!-- END: error_warning -->
	<!-- BEGIN: success -->
		<div class="alert alert-success">
			<i class="fa fa-check-circle"></i> {SUCCESS}<i class="fa fa-times"></i>
		</div>
	<!-- END: success -->
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-list"></i> {LANG.recharge_list}</h3> 
		 
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<div class="well">
				<div class="row">
					<form  action="{NV_BASE_ADMINURL}index.php" method="get" id="formsearch">
						<input type="hidden" name ="{NV_NAME_VARIABLE}"value="{MODULE_NAME}" />
						<input type="hidden" name ="{NV_OP_VARIABLE}"value="{OP}" />
						<div class="col-sm-8">
							<div class="form-group">
								<label class="control-label" for="input-username">{LANG.recharge_username}</label>
								<input type="text" name="username" value="{DATA.username}" placeholder="{LANG.recharge_username}" id="input-hoten" class="form-control" autocomplete="off">								 
							</div>
							<div class="form-group">
								<label class="control-label" for="input-seri_number">{LANG.recharge_seri}</label>
								<input type="text" name="seri_number" value="{DATA.seri_number}" placeholder="{LANG.recharge_seri}" id="input-seri_number" class="form-control" autocomplete="off">
							</div>
						</div>
						<div class="col-sm-8">
							<div class="form-group">
								<label class="control-label" for="input-pin_number">{LANG.recharge_pin}</label>
								<input type="text" name="pin_number" value="{DATA.pin_number}" placeholder="{LANG.recharge_pin}" id="input-pin_number" class="form-control" autocomplete="off">
							</div>
						</div>
						<div class="col-sm-8">
							<div class="form-group">
								<label class="control-label clear" for="input-date_added">{LANG.recharge_date_added}</label>
								<div class="clear"></div>
								<input type="text" name="date_from" value="{DATA.date_from}" id="date_from"  placeholder="{LANG.tsqg_date_from}" class="form-control" autocomplete="off" style="display:inline-block;width:90px"> <strong>:</strong>
								<input type="text" name="date_to" value="{DATA.date_to}" id="date_to" placeholder="{LANG.tsqg_date_to}" class="form-control" autocomplete="off" style="display:inline-block;width:90px">
							</div>
							
						</div>
						<div class="col-sm-8">
							<div class="form-group">
								<label class="control-label clear" for="input-search">&nbsp;&nbsp;</label>
								<div class="clear"></div>
								<input type="hidden" value="{TOKEN}" name="token"/>
								<button type="submit" class="btn btn-primary" > <i class="fa fa-search"></i> Tìm kiếm </button>
						
							</div>
						</div>
						 
					</form>
						
				</div>
			</div>
			<form action="#" method="post" enctype="multipart/form-data" id="form-type">
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<td class="col-sm-0 text-center"><strong>STT</strong></a></td>
								<td class="col-sm-4 text-center"><strong>Tài khoản</strong></td>
								<td class="col-sm-3 text-center"><a {SUPPLIER_ORDER} href="{URL_SUPPLIER}"><strong>{LANG.recharge_supplier}</strong></a> </td>
								<td class="col-sm-3 text-center"><strong>Số Serial</strong></td>
								<td class="col-sm-3 text-center"><strong>Mã PIN</strong></td>
								<td class="col-sm-3 text-center"><strong>Mệnh Giá</strong></td>
								<td class="col-sm-4 text-center"><strong>Ngày nạp</strong></td>
								<td class="col-sm-4 text-center"> <strong>{LANG.action} </strong></td>
							</tr>
						</thead>
						<tbody>
							 <!-- BEGIN: loop --> 
							<tr id="group_{LOOP.recharge_id}">
								<td class="text-center">
									{LOOP.stt} 
								</td>
								<td class="text-left">{LOOP.username}</td>
								<td class="text-left">{LOOP.supplier}</td>
								<td class="text-right">{LOOP.seri_number}</td>
								<td class="text-right">{LOOP.pin_number}</td>
								<td class="text-center">{LOOP.money}</td>
								<td class="text-center">{LOOP.date_added}</td>
								<td class="text-center">
									<a href="{LOOP.edit}" data-toggle="tooltip" title="{LANG.edit}" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
									<a href="javascript:void(0);" onclick="delete_recharge('{LOOP.recharge_id}', '{LOOP.token}')" data-toggle="tooltip" title="{LANG.delete}" class="btn btn-danger"><i class="fa fa-trash-o"></i>
								</td>
							</tr>
							 <!-- END: loop -->
						</tbody>
					</table>
				</div>
				<div id="total" style="text-align:right; font-weight:bold;font-size:16px">Tổng Tiền: {TOTAL} VNĐ</div>
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
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
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

function delete_recharge(recharge_id, token) {
	if(confirm('{LANG.confirm}')) {
		$.ajax({
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=recharge&action=delete&nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'recharge_id=' + recharge_id + '&token=' + token,
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
					$('#recharge-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<i class="fa fa-times"></i></div></div>');
				}
				
				if (json['success']) {
					$('#recharge-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<i class="fa fa-times"></i></div></div>');
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
	var recharge_id = $(this).attr('data-id');
	var new_vid = $(this).val();
	var id = $(this).attr('id');
	$.ajax({
		url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=recharge&nocache=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data: 'action=' + action + '&recharge_id=' + recharge_id + '&new_vid=' + new_vid + '&token='+token,
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
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=recharge&action=delete&nocache=' + new Date().getTime(),
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
					$('#recharge-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<i class="fa fa-times"></i></div></div>');
				}
				
				if (json['success']) {
					$('#recharge-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<i class="fa fa-times"></i></div></div>');
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