<!-- BEGIN: main -->
<div id="group_user-content">
    <!-- BEGIN: error_warning -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {WARNING} <i class="fa fa-times"></i>
    </div>
    <!-- END: error_warning -->
    <div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-pencil"></i> {CAPTION}</h3>
			<div class="pull-right">
				<button type="submit" form="form-stock" data-toggle="tooltip" class="btn btn-primary btn-sm" title="Save"><i class="fa fa-save"></i></button> 
				<a href="{CANCEL}" data-toggle="tooltip" class="btn btn-default btn-sm" title="Cancel"><i class="fa fa-reply"></i></a>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="" method="post"  enctype="multipart/form-data" id="form-group_user" class="form-horizontal">
				<input name="save" type="hidden" value="1" />
  	
                
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-username">{LANG.group_user_manager}</label>
					<div class="col-sm-20">
						<div class="boxajax">
							<input type="hidden" class="form-control" name="group_user_id" value="{DATA.group_user_id}" >	 
							<i class="fa fa-times {SHOW}" aria-hidden="true"></i>							
							<input type="text" class="form-control" id="group_user_id" value="{DATA.title}" placeholder="{LANG.group_user_select}">	
						</div>
						<!-- BEGIN: error_group_user_id --><div class="text-danger">{error_group_user_id}</div><!-- END: error_group_user_id -->
					</div>
				</div> 
 		        <div class="form-group required">
					<label class="col-sm-4 control-label" for="input-user">{LANG.group_user_user}</label>
					<div class="col-sm-20">
						<div style="position:relative;width:100%;">
							<input type="text" name="userlists" value="" placeholder="{LANG.group_user_user}" id="userlists" class="form-control input-sm" />
							<div id="group-user" class="well well-sm">
								<!-- BEGIN:user -->
								<div id="group-user{USER.userid}"><i class="fa fa-minus-circle"></i> {USER.username}
								<input type="hidden" name="userlist[]" value="{USER.userid}"></div>
								<!-- END:user -->
							</div>
							<!-- BEGIN: error_userlist --><div class="text-danger">{error_userlist}</div><!-- END: error_userlist -->
						</div>
					</div>
				</div>         
				<div align="center">
					<input class="btn btn-primary btn-sm" type="submit" value="{LANG.save}">
					<a class="btn btn-default btn-sm" href="{CANCEL}" title="{LANG.cancel}">{LANG.cancel}</a> 
				</div>          
			</form>
		</div>
	</div>
</div>
 
<script type="text/javascript">
$('#group_user_id').autofill({
	'source': function(request, response) {	 
		$.ajax({
			url: script_name + '?' + nv_name_variable + '='+ nv_module_name  +'&' + nv_fc_variable + '=group_user&action=get_group&title='+ encodeURIComponent(request) +'&nocache=' + new Date().getTime(),		
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
				return {
					label: item['title'],
					value: item['group_user_id']
				}
			}));
			}
		});	 
	},
    'select': function(item) {
		$('#group_user_id').val( item['label'] );
		$('input[name="group_user_id"]').val( item['value'] );
		$('#group_user_id').parent().find('i').show();
	}
}); 
$('#userlists').autofill({
	'source': function(request, response) {	 
		$.ajax({
			url: script_name + '?' + nv_name_variable + '='+ nv_module_name  +'&' + nv_fc_variable + '=group_user&action=get_user&username='+ encodeURIComponent(request) +'&nocache=' + new Date().getTime(),		
			//data: $('input[name="group_user_id"],input[name="userlist[]"]'),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
				return {
					label: item['username'],
					value: item['userid']
				}
			}));
			}
		});	 
	},
    'select': function(item) {
		$('input[name=\'userlists\']').val('');
		
		$('#group-user' + item['value']).remove();
		
		$('#group-user').append('<div id="group-user' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="userlist[]" value="' + item['value'] + '" /></div>');	
	
		
	}
}); 
$(document).delegate('.boxajax i', 'click', function() {
	$(this).parent().find('input').val('');
	$(this).hide();
});	
$('#group-user').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});	

</script>
<!-- END: main -->