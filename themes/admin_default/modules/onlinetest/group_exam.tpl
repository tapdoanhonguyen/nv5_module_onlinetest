<!-- BEGIN: main -->
<div id="group-exam-content"> 
	<!-- BEGIN: catnav -->
	<div class="divbor1" style="margin-bottom: 10px">
		<!-- BEGIN: loop -->
		{CAT_NAV}
		<!-- END: loop -->
	</div>
	<!-- END: catnav -->
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
			<h3 class="panel-title" style="float:left"><i class="fa fa-list"></i> {LANG.group_exam_list}</h3> 
			 <div class="pull-right">
				<a href="{ADD_NEW}" data-toggle="tooltip" data-placement="top" title="{LANG.add_new}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i></a>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="#" method="post" enctype="multipart/form-data" id="form-group_exam">
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<td class="text-center" style="width:80px" ><a {WEIGHT_ORDER} href="{URL_WEIGHT}"><strong>{LANG.weight}</strong></a></td>
								<td class="text-center"><a {TITLE_ORDER} href="{URL_TITLE}"><strong>{LANG.group_exam_title}</strong></a> </td>
								<td class="text-center"><strong>{LANG.group_exam_show_inhome} </strong></td>
								<td class="text-center"><strong>{LANG.group_exam_status} </strong></td>
								<td class="text-center"><strong>{LANG.action}</strong></td>
							</tr>
						</thead>
						<tbody>
							 <!-- BEGIN: loop --> 
							<tr id="group_{LOOP.group_exam_id}">
								<td class="text-center">
									<select id="id_weight_{LOOP.group_exam_id}" class="form-control btn-sm formajax" data-action="weight" data-id="{LOOP.group_exam_id}" data-token="{LOOP.token}">
									<!-- BEGIN: weight -->
									<option value="{WEIGHT.w}"{WEIGHT.selected}>{WEIGHT.w}</option>
									<!-- END: weight -->
									</select>
								</td>
								<td class="text-left"><a href="{LOOP.link}"> <strong>{LOOP.title}</strong> </a> {LOOP.numsubcat}</td>
								<td class="text-center">
									<input name="inhome" id="id_inhome_{LOOP.group_exam_id}" value="1" type="checkbox" data-token="{LOOP.token}" data-group_exam_id="{LOOP.group_exam_id}" data-action="inhome" {LOOP.inhome_checked}>
								</td>
								<td class="text-center">
									<input name="status" id="id_status_{LOOP.group_exam_id}" value="1" type="checkbox" data-token="{LOOP.token}" data-group_exam_id="{LOOP.group_exam_id}" data-action="status" {LOOP.status_checked}>
								</td>
								<td class="text-center">
									<a href="{LOOP.edit}" data-toggle="tooltip" title="{LANG.edit}" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
									<a href="javascript:void(0);" onclick="delete_group_exam('{LOOP.group_exam_id}', '{LOOP.token}')" data-toggle="tooltip" title="{LANG.delete}" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i>
								</td>
							</tr>
							 <!-- END: loop -->
						</tbody>
					</table>
				</div>
			</form>
			<!-- BEGIN: generate_page -->
			<div class="row">
				<div class="col-sm-12 text-left">
				
				<div style="clear:both"></div>
				{GENERATE_PAGE}
				
				</div>
				 
			</div>
			<!-- END: generate_page -->
		</div>
		<div id="cat-delete-area">&nbsp;</div>
	</div>
</div>

<script type="text/javascript">
$('.formajax').on('change', function() {
 
	var action = $(this).attr('data-action');
	var token = $(this).attr('data-token');
	var group_exam_id = $(this).attr('data-id');
	var new_vid = $(this).val();
	var id = $(this).attr('id');
	$.ajax({
		url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=group_exam&nocache=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data: 'action=' + action + '&group_exam_id=' + group_exam_id + '&new_vid=' + new_vid + '&token='+token,
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
				
				$('#id_'+action+'_'+group_exam_id).val( json['new_vid'] );
				
			};	
			if ( json['link'] ) location.href = json['link'];
 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});
$('input[name="status"]').on('change', function() {
	var obj = $(this);
	var action = $(this).attr('data-action');
	var token = $(this).attr('data-token');
	var group_exam_id = $(this).attr('data-group_exam_id');
	var new_vid = ( obj.prop('checked') ) ? 1 : 0;
 
	$.ajax({
		url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=group_exam&nocache=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data: 'action=' + action + '&group_exam_id=' + group_exam_id + '&new_vid=' + new_vid + '&token='+token,
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
				
				$('#group-exam-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> '+ json['success'] +' <i class="fa fa-times"></i></div>');
				
			} 
			
			
 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			obj.prop('disabled', false);
			 
		}
	});
});
$('input[name="inhome"]').on('change', function() {
	var obj = $(this);
	var action = $(this).attr('data-action');
	var token = $(this).attr('data-token');
	var group_exam_id = $(this).attr('data-group_exam_id');
	var new_vid = ( obj.prop('checked') ) ? 1 : 0;
 
	$.ajax({
		url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=group_exam&nocache=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data: 'action=' + action + '&group_exam_id=' + group_exam_id + '&new_vid=' + new_vid + '&token='+token,
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
				
				$('#group-exam-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> '+ json['success'] +' <i class="fa fa-times"></i></div>');
				
			} 
			
			
 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			obj.prop('disabled', false);
			 
		}
	});
});

function delete_group_exam(group_exam_id, token) {
	if(confirm('{LANG.confirm}')) {
		$.ajax({
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=group_exam&action=delete&nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'group_exam_id=' + group_exam_id + '&token=' + token,
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
					$('#group-exam-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<i class="fa fa-times"></i></div>');
				}
				
				if (json['success']) {
					$('#group-exam-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<i class="fa fa-times"></i></div>');
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
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=group_exam&action=delete&nocache=' + new Date().getTime(),
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
					$('#group-exam-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<i class="fa fa-times"></i></div>');
				}
				
				if (json['success']) {
					$('#group-exam-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<i class="fa fa-times"></i></div>');
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