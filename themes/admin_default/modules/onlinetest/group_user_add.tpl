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
				<input type="hidden" name ="group_user_id" value="{DATA.group_user_id}" />
				<input type="hidden" name ="parentid_old" value="{DATA.parent_id}" />
				<input name="save" type="hidden" value="1" />
 
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="inputs-title">{LANG.group_user_title}</label>
					<div class="col-sm-20">
						<input type="text" name="title" value="{DATA.title}" placeholder="{LANG.group_user_title}" id="inputs-title" class="form-control btn-sm" />
						<!-- BEGIN: error_title --><div class="text-danger">{error_title}</div><!-- END: error_title -->
					</div>
				</div>
				<div class="form-group">
                    <label class="col-sm-4 control-label" for="input-alias">{LANG.group_user_alias}</label>
                    <div class="col-sm-20">
						<div class="input-group">
							<input class="form-control btn-sm" name="alias" placeholder="{LANG.group_user_alias}"  type="text" value="{DATA.alias}" maxlength="255" id="input-alias"/>
							<div class="input-group-addon fixaddon">
								&nbsp;<em class="fa fa-refresh fa-lg fa-pointer text-middle" onclick="get_alias('group_user', '{DATA.group_user_id}');">&nbsp;</em>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-parent">{LANG.group_user_sub}</label>
					<div class="col-sm-20">
						<select class="form-control btn-sm select2" name="parent_id">
							<option value="0">{LANG.group_user_sub_sl}</option>
							<!-- BEGIN: group_user -->
							<option value="{GROUP_USER.key}" {GROUP_USER.selected}>{GROUP_USER.name}</option>
							<!-- END: group_user -->
						</select>
					</div>
				</div>			
                
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-username">{LANG.group_user_manager}</label>
					<div class="col-sm-20">
						<div class="boxajax">
							<input type="hidden" class="form-control" name="user_manager_id" value="{DATA.user_manager_id}" >	 
							<i class="fa fa-times {SHOW}" aria-hidden="true"></i>							
							<input type="text" class="form-control" id="user_manager_id" value="{DATA.user_manager_title}" placeholder="{LANG.group_user_manager_select}">	
						</div>
						<!-- BEGIN: error_user_manager_id --><div class="text-danger">{error_user_manager_id}</div><!-- END: error_user_manager_id -->
					</div>
				</div> 
 		
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-description">{LANG.group_user_description} </label>
					<div class="col-sm-20">
						<textarea name="description" rows="2" placeholder="{LANG.group_user_description}" id="input-description" class="form-control btn-sm">{DATA.description}</textarea>
						<!-- <span class="text-middle"> {GLANG.length_characters}: <span id="descriptionlength" class="red">0</span>. {GLANG.description_suggest_max} </span> -->            
					</div>
				</div>
                    	 
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-status">{LANG.group_user_show_status}</label>
					<div class="col-sm-20">
						<select name="status" id="input-status" class="form-control btn-sm">
							<!-- BEGIN: status -->
							<option value="{STATUS.key}" {STATUS.selected}>{STATUS.name}</option>
							<!-- END: status -->
						</select>
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
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
 
<script type="text/javascript">

<!-- BEGIN: getalias -->
$("#inputs-title").change(function() {
	get_alias('group_user', '{DATA.group_user_id}');
});
<!-- END: getalias -->

$(".select2").select2({
    language: "{NV_LANG_INTERFACE}"
}); 

$('.boxajax i').on('click', function() {
	
	$(this).hide().removeClass('showx').parent().find('input').val('');
	
});

$('#user_manager_id').autofill({
	'source': function(request, response) {	 
		$.ajax({
			url: script_name + '?' + nv_name_variable + '='+ nv_module_name  +'&' + nv_fc_variable + '=get_user&username='+ encodeURIComponent(request) +'&nocache=' + new Date().getTime(),		
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
		$('#user_manager_id').val( item['label'] );
		$('input[name="user_manager_id"]').val( item['value'] );
		$('#user_manager_id').parent().find('i').show();
	}
}); 

</script>


<!-- END: main -->