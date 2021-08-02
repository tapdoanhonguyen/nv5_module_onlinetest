<!-- BEGIN: main -->
<form class="form-horizontal" action="{NV_BASE_ADMINURL}index.php" method="post">
	<input type="hidden" name ="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" />
	<input type="hidden" name ="{NV_OP_VARIABLE}" value="{OP}" />
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover">
			<tbody>
				<tr>
					<td style="width:280px"><strong>{LANG.config_open}</strong></td>
					<td>						<input class="form-control" type="checkbox" value="1" name="open" {DATA.open_checked}/> 					</td>
				</tr>
				<tr>
					<td style="width:280px"><strong>{LANG.config_allow_download}</strong></td>
					<td>
						<input class="form-control" type="checkbox" value="1" name="allow_download" {DATA.allow_download_checked}/>
					</td>
				</tr>
				<tr>
					<td style="width:280px"><strong>{LANG.config_allow_show_answer}</strong></td>
					<td>
						<input class="form-control" type="checkbox" value="1" name="allow_show_answer" {DATA.allow_show_answer_checked}/>
					</td>
				</tr>
				<tr>
					<td style="width:280px"><strong>{LANG.config_allow_video}</strong></td>
					<td>
						<input class="form-control" type="checkbox" value="1" name="allow_video" {DATA.allow_video_checked}/>
					</td>
				</tr>
 
				<tr>
					<td><strong>{LANG.config_perpage}</strong></td>
					<td>
						<select class="form-control" name="perpage" style="width:150px">
							<!-- BEGIN: perpage -->
							<option value="{PERPAGE.key}"{PERPAGE.selected}>{PERPAGE.name}</option>
							<!-- END: perpage -->
						</select>
					</td>
				</tr>
				
				<tr>
					<td><strong>{LANG.config_max_score}</strong></td>
					<td><input class="form-control" type="text" value="{DATA.max_score}" name="max_score" style="width:150px"/></td>
				</tr>
				<tr>
					<td><strong>{LANG.config_test_limit}</strong></td>
					<td><input class="form-control" type="text" value="{DATA.test_limit}" name="test_limit" style="width:150px"/>({LANG.config_test_limit_help})</td>
				</tr>
				<tr>
					<td><strong>{LANG.config_test_timeout}</strong></td>
					<td><input class="form-control" type="text" value="{DATA.test_timeout}" name="test_timeout" style="width:150px"/>({LANG.minutes})({LANG.config_test_timeout_help})</td>
				</tr>
				<tr>
					<td><strong>{LANG.config_format_code_id}</strong></td>
					<td><input class="form-control" type="text" value="{DATA.format_code_id}" name="format_code_id" style="width:150px;display: inline-block;"/>({LANG.config_format_code_id_help})</td>
				</tr>
				<tr>
					<td><strong>{LANG.config_bonus_score}</strong></td>
					<td><input class="form-control" type="text" value="{DATA.bonus_score}" name="bonus_score" style="width:150px;display: inline-block;"/>({LANG.config_bonus_score_help})</td>
				</tr>
				<tr>
					<td><strong>{LANG.config_convert_to_vcoin}</strong></td>
					<td><input class="form-control" type="text" value="{DATA.convert_to_vcoin}" name="convert_to_vcoin" style="width:150px;display: inline-block;"/>({LANG.config_convert_to_vcoin_help})</td>
				</tr>
				<tr>
					<td><strong>{LANG.config_default_group_teacher}</strong></td>
					<td>
						<div class="boxajax">
							<input type="hidden" class="form-control" name="default_group_teacher" value="{DATA.default_group_teacher}" >	 
							<i class="fa fa-times {SHOW1}" aria-hidden="true"></i>							
							<input type="text" class="form-control" id="default_group_teacher" value="{DATA.default_group_teacher_title}" placeholder="{LANG.group_user_select}">	
						</div>
					</td>
				</tr>
				<tr>
					<td><strong>{LANG.config_default_group_student}</strong></td>
					<td>
						<div class="boxajax">
							<input type="hidden" class="form-control" name="default_group_student" value="{DATA.default_group_student}" >	 
							<i class="fa fa-times {SHOW2}" aria-hidden="true"></i>							
							<input type="text" class="form-control" id="default_group_student" value="{DATA.default_group_student_title}" placeholder="{LANG.group_user_select}">	
						</div>
					</td>
				</tr>
				<tr>
					<td><strong>{LANG.config_default_form_import}</strong></td>
					<td>
						<input class="form-control" type="text" value="{DATA.default_form_import}" name="default_form_import" id="default_form_import" style="width:380px"/>
						<input id="select-img" type="button" value="{LANG.config_file}" name="selectimg" class="btn btn-info" >
					</td>
				</tr>
				<tr>
					<td><strong>{LANG.config_intro}</strong></td>
					<td>
						 {INTRO}
					</td>
				</tr>
				<tr>
					<td colspan="2"><strong>{LANG.config_comment}</strong></td>				 
				</tr>
				<tr>
					<td style="width:280px"><strong>{LANG.config_show_comment}</strong></td>
					<td>
						<input class="form-control" type="checkbox" value="1" name="show_comment" {DATA.show_comment_checked}/>
					</td>
				</tr>
				<tr>
					<td><strong>{LANG.config_number_comment}</strong></td>
					<td><input class="form-control" type="text" value="{DATA.number_comment}" name="number_comment" style="width:150px;display: inline-block;" /></td>
				</tr>
				<tr>
					<td><strong>{LANG.config_time_modify_comment}</strong></td>
					<td><input class="form-control" type="text" value="{DATA.time_modify_comment}" name="time_modify_comment"  data-toggle="tooltip" data-placement="top" title="{LANG.config_time_comment_help}" style="width:150px;display: inline-block;"/> {LANG.minutes}</td>
				</tr>
				<tr>
					<td><strong>{LANG.config_time_delete_comment}</strong></td>
					<td><input class="form-control" type="text" value="{DATA.time_delete_comment}" name="time_delete_comment"  data-toggle="tooltip" data-placement="top" title="{LANG.config_time_comment_help}" style="width:150px;display: inline-block;"/> {LANG.minutes}</td>
				</tr>
				<tr>
					<td colspan="2"><strong>{LANG.config_baokim}</strong></td>				 
				</tr>
				
				
				<tr>
					<td><strong>merchant_id</strong></td>
					<td>
						<input class="form-control" type="text" value="{DATA.merchant_id}" name="merchant_id" style="width: 200px;"/>					 
					</td>
				</tr>
				<tr>
					<td><strong>secure_code</strong></td>
					<td>
						<input class="form-control" type="text" value="{DATA.secure_code}" name="secure_code" style="width: 200px;"/>					 
					</td>
				</tr>
				<tr>
					<td><strong>api_username</strong></td>
					<td>
						<input class="form-control" type="text" value="{DATA.api_username}" name="api_username"  style="width: 200px;"/>					 
					</td>
				</tr>
				<tr>
					<td><strong>api_password</strong></td>
					<td>
						<input class="form-control" type="text" value="{DATA.api_password}" name="api_password"  style="width: 200px;"/>					 
					</td>
				</tr>
				<tr>
					<td><strong>CORE_API_HTTP_USR </strong></td>
					<td>
						<input class="form-control" type="text" value="{DATA.core_api_http_usr}" name="core_api_http_usr"  style="width: 200px;"/>					 
					</td>
				</tr>
				<tr>
					<td><strong>CORE_API_HTTP_PWD  </strong></td>
					<td>
						<input class="form-control" type="text" value="{DATA.core_api_http_pwd}" name="core_api_http_pwd"  style="width: 200px;"/>					 
					</td>
				</tr>				
				
				 
				 
				<tr>
					<td style="text-align: left; padding-left:290px;" colspan="2">
					<input class="btn btn-primary" type="submit" value="{LANG.save}" name="Submit1" />
					<input type="hidden" value="1" name="saveconfig" /></td>
				</tr>        
			</tbody>
		</table>
	</div>
</form>
<script type="text/javascript">
$('#default_group_teacher').autofill({
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
		$('#default_group_teacher').val( item['label'] );
		$('input[name="default_group_teacher"]').val( item['value'] );
		$('#default_group_teacher').parent().find('i').show();
	}
});  
$('#default_group_student').autofill({
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
		$('#default_group_student').val( item['label'] );
		$('input[name="default_group_student"]').val( item['value'] );
		$('#default_group_student').parent().find('i').show();
	}
});  
$(document).delegate('.boxajax i', 'click', function() {
	$(this).parent().find('input').val('');
	$(this).hide();
});	
 
$("input[name=selectimg]").click(function() {
	var area = "default_form_import";
	var path = "{UPLOAD_CURRENT}";
	var currentpath = "{UPLOAD_CURRENT}";
	var type = "files";
	nv_open_browse(script_name + "?" + nv_name_variable + "=upload&popup=1&area=" + area + "&path=" + path + "&type=" + type + "&currentpath=" + currentpath, "NVImg", 850, 420, "resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
	return false;
});
</script>
<!-- END: main -->