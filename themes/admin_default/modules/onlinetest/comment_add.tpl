<!-- BEGIN: main -->
<div id="comment-content">
    <!-- BEGIN: error_warning -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {error_warning}<i class="fa fa-times"></i>
    </div>
    <!-- END: error_warning -->
    <!-- BEGIN: error_comment -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {error_comment}<i class="fa fa-times"></i>
    </div>
    <!-- END: error_comment -->
 
    <div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-pencil"></i> {CAPTION}</h3>
			<div class="pull-right">
				<button type="submit" form="form-stock" data-toggle="tooltip" class="btn btn-primary" title="{LANG.save}"><i class="fa fa-save"></i></button> 
				<button onclick="delete_comment('{DATA.comment_id}', '{DATA.token}')" type="button" data-toggle="tooltip" data-placement="top" class="btn btn-danger" id="button-delete" title="{LANG.delete}">
					<i class="fa fa-trash-o"></i>
				</button>
				<a href="{CANCEL}" data-toggle="tooltip" class="btn btn-default" title="{LANG.cancel}"><i class="fa fa-reply"></i></a>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="" method="post"  enctype="multipart/form-data" id="form-comment" class="form-horizontal">
				<input type="hidden" name ="comment_id" value="{DATA.comment_id}" />
				<input name="save" type="hidden" value="1" />
 
				<div class="form-group reply">
			 
					{DATA.old_comment}
				</div>
				<div style="font-size: 16px;"><i class="fa fa-paper-plane" aria-hidden="true"></i> <strong>{LANG.comment_modify}</strong></div>
				<div class="form-group reply">
					{COMMENT}
					<div class="status">
						<select name="status" id="input-status" class="form-control">
							<!-- BEGIN: status -->
							<option value="{STATUS.key}" {STATUS.selected}>{STATUS.name}</option>
							<!-- END: status -->
						</select>
					</div>
				</div>
				
				<div class="form-group text-center">
					<input class="btn btn-primary" type="submit" value="{LANG.save}">
					<a class="btn btn-primary" href="{CANCEL}" title="{LANG.cancel}">{LANG.cancel}</a> 
				</div>  
			</form>
			 
		</div>
	</div>
</div>
<script type="text/javascript"> 
function delete_comment(comment_id, token) {
	if(confirm('{LANG.confirm}')) {
		$.ajax({
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=comment&action=delete&nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'comment_id=' + comment_id + '&token=' + token + '&redirect=1',
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