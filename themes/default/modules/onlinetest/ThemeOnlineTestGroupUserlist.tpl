<!-- BEGIN: main -->
<div id="group_user-content"> 
	<!-- BEGIN: error_warning -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {WARNING}<i class="fa fa-times"></i>        
    </div>
    <!-- END: error_warning -->
	<!-- BEGIN: success -->
		<div class="alert alert-success">
			<i class="fa fa-check-circle"></i> {SUCCESS}<i class="fa fa-times"></i>
		</div>
	<!-- END: success -->
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-list"></i> {LANG.group_user_list_user}: {LOOP.group_title}</h3> 
			 <div class="pull-right">
				<a href="{ADD_NEW}" data-toggle="tooltip" data-placement="top" title="{LANG.group_user_user_add}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i></a>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="#" method="post" enctype="multipart/form-data" id="form-group_user">
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<td class="text-center" style="width:80px" ><strong>Userid</strong></td>
								<td class="text-center"><a {USERNAME_ORDER} href="{URL_USERNAME}"><strong>{LANG.group_user_username}</strong></a> </td>
								<td class="text-center"><a {FULL_NAME_ORDER} href="{URL_FULL_NAME}"><strong>{LANG.group_user_full_name}</strong></a> </td>
								<td class="text-center"><a {GROUP_USER_ID_ORDER} href="{URL_GROUP_USER_ID}"><strong>{LANG.group_user_group}</strong></a> </td>
								<td class="text-center"><strong>{LANG.group_user_active} </strong></td>
								<td class="text-center"><strong>{LANG.action}</strong></td>
							</tr>
						</thead>
						<tbody>
							 <!-- BEGIN: loop --> 
							<tr id="group_{LOOP.userid}">
								<td class="text-center">
									<strong>{LOOP.userid}</strong>
								</td>
								<td class="text-left"><a href="{LOOP.user_link}" target="_blank"><strong>{LOOP.username}</strong> </a></td>
								<td class="text-left"><a href="{LOOP.user_link}" target="_blank"><strong>{LOOP.full_name}</strong></a></td>
								<td class="text-center"><strong>{LOOP.group_title}</strong></td>
								<td class="text-center">
									<input name="active" id="id_active_{LOOP.userid}" value="1" type="checkbox" data-token="{LOOP.token}" data-userid="{LOOP.userid}" data-action="active" {LOOP.active_checked}>
								</td>
								</td>
								<td class="text-center">
									<a href="javascript:void(0);" onclick="delete_group_user('{LOOP.group_user_id}','{LOOP.userid}', '{LOOP.token}')" data-toggle="tooltip" title="{LANG.group_user_delete_user}" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i>
								</td>
							</tr>
							 <!-- END: loop -->
						</tbody>
					</table>
				</div>
			</form>
			
			<div class="row">
				
				<div class="col-sm-14 text-left">
				<!-- BEGIN: generate_page -->	
				{GENERATE_PAGE}
				<!-- END: generate_page -->
				</div> 
				
				<div class="col-sm-10 text-right">
				{LANG.group_user_user_total}: {TOTAL_USER}
				</div> 
			</div>
			
		</div>
	</div>
</div>

<script type="text/javascript">
$('.formajax').on('change', function() {
 
	var action = $(this).attr('data-action');
	var token = $(this).attr('data-token');
	var userid = $(this).attr('data-id');
	var new_vid = $(this).val();
	var id = $(this).attr('id');
	$.ajax({
		url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=group-user&nocache=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data: 'action=' + action + '&userid=' + userid + '&new_vid=' + new_vid + '&token='+token,
		beforeSend: function() {
			$('#'+id ).prop('disabled', true);
			$('.alert').remove();
		},	
		complete: function() {
			$('#'+id ).prop('disabled', false);
		},
		success: function(json) {
			
			if ( json['error'] ) alert( json['error'] );	
			if ( json['new_vid'] == 0 || json['new_vid'] == 1){
				
				$('#id_'+action+'_'+userid).val( json['new_vid'] );
				
			};	
			if ( json['link'] ) location.href = json['link'];
 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('input[name="active"]').on('change', function() {
	var obj = $(this);
	var action = $(this).attr('data-action');
	var token = $(this).attr('data-token');
	var userid = $(this).attr('data-userid');
	var new_vid = ( obj.prop('checked') ) ? 1 : 0;
 
	$.ajax({
		url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=group-user&nocache=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data: 'action=' + action + '&userid=' + userid + '&new_vid=' + new_vid + '&token='+token,
		beforeSend: function() {
			obj.prop('disabled', true);
			$('.alert').remove();
		},	
		complete: function() {
			obj.prop('disabled', false);
		},
		success: function(json) {
			
			if ( json['error'] )
			{
				if ( new_vid == 1 ) obj.prop('checked', false);
				else obj.prop('checked', true);
				alert( json['error'] );	
				
				
			}else if( json['success'] ){
			
				if ( new_vid == 1 ) obj.prop('checked', true);
				else obj.prop('checked', false);
				
				$('#group_user-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> '+ json['success'] +' <i class="fa fa-times"></i></div>');
				
			} 
			
			
 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			obj.prop('disabled', false);
			 
		}
	});
});

function delete_group_user(group_user_id, userid, token) {
	if(confirm('{LANG.confirm}')) {
		$.ajax({
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=group-user&action=delete_user&nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: { group_user_id: group_user_id, userid: userid, token: token },
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
					$('#group_user-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<i class="fa fa-times"></i></div>');
				}
				
				if (json['success']) {
					$('#group_user-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<i class="fa fa-times"></i></div>');
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
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=group-user&action=delete_user&nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'group_user_id={DATA.group_user_id}&listid=' + listid + '&token={TOKEN}',
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
					$('#group_user-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<i class="fa fa-times"></i></div>');
				}
				
				if (json['success']) {
					$('#group_user-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<i class="fa fa-times"></i></div>');
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