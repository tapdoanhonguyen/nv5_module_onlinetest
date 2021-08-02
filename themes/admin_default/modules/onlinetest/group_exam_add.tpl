<!-- BEGIN: main -->
<div id="group-exam-content">
    <!-- BEGIN: error_warning -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {WARNING}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <br>
    </div>
    <!-- END: error_warning -->
    <div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-pencil"></i> {CAPTION}</h3>
			<div class="pull-right">
				<button type="submit" form="form-group-exam" data-toggle="tooltip" class="btn btn-primary" title="{LANG.save}"><i class="fa fa-save"></i></button> 
				<a href="{CANCEL}" data-toggle="tooltip" class="btn btn-default" title="{LANG.cancel}"><i class="fa fa-reply"></i></a>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="" method="post"  enctype="multipart/form-data" id="form-group_exam" class="form-horizontal">
				<input type="hidden" name ="group_exam_id" value="{DATA.group_exam_id}" />
				<input type="hidden" name ="parentid_old" value="{DATA.parent_id}" />
				<input name="save" type="hidden" value="1" />
 
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="inputs-title">{LANG.group_exam_title}</label>
					<div class="col-sm-20">
						<input type="text" name="title" value="{DATA.title}" placeholder="{LANG.group_exam_title}" id="inputs-title" class="form-control btn-sm" />
						<!-- BEGIN: error_title --><div class="text-danger">{error_title}</div><!-- END: error_title -->
					</div>
				</div>
				<div class="form-group">
                    <label class="col-sm-4 control-label" for="input-alias">{LANG.group_exam_alias}</label>
                    <div class="col-sm-20">
						<div class="input-group">
							<input class="form-control btn-sm" name="alias" placeholder="{LANG.group_exam_alias}"  type="text" value="{DATA.alias}" maxlength="255" id="input-alias"/>
							<div class="input-group-addon fixaddon">
								&nbsp;<em class="fa fa-refresh fa-lg fa-pointer text-middle" onclick="get_alias( );">&nbsp;</em>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-parent">{LANG.group_exam_sub}</label>
					<div class="col-sm-20">
						<select class="form-control btn-sm" name="parent_id">
							<option value="0">{LANG.group_exam_sub_sl}</option>
							<!-- BEGIN: group_exam -->
							<option value="{CATEGORY.key}" {CATEGORY.selected}>{CATEGORY.name}</option>
							<!-- END: group_exam -->
						</select>
					</div>
				</div>			
 		
                <div class="form-group">
                     <label class="col-sm-4 control-label" for="input-description">{LANG.group_exam_description} </label>
                     <div class="col-sm-20">
                          <textarea name="description" rows="2" placeholder="{LANG.group_exam_description}" id="input-description" class="form-control btn-sm">{DATA.description}</textarea>
						  <!-- <span class="text-middle"> {GLANG.length_characters}: <span id="descriptionlength" class="red">0</span>. {GLANG.description_suggest_max} </span> -->            
                      </div>
                 </div>
                 <div class="form-group">
					<label class="col-sm-4 control-label" for="input-meta-title">{LANG.group_exam_meta_title}</label>
					<div class="col-sm-20">
						<input type="text" name="meta_title" value="{DATA.meta_title}" placeholder="{LANG.group_exam_meta_title}" id="input-meta-title" class="form-control btn-sm" />
						<!-- BEGIN: error_meta_title--><div class="text-danger">{error_meta_title}</div><!-- END: error_meta_title -->
					</div>
                 </div>
				 <div class="form-group">
					<label class="col-sm-4 control-label" for="input-meta-description">{LANG.group_exam_meta_description}</label>
					<div class="col-sm-20">
						<textarea name="meta_description" rows="2" placeholder="{LANG.group_exam_meta_description}" id="input-meta-description" class="form-control btn-sm">{DATA.meta_description}</textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-meta-keyword">{LANG.group_exam_meta_keyword}</label>
					<div class="col-sm-20">
						<textarea name="meta_keyword" rows="2" placeholder="{LANG.group_exam_meta_keyword}" id="input-meta-keyword" class="form-control btn-sm">{DATA.meta_keyword}</textarea>
					</div>
				</div>    	 
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-groups-views">{LANG.group_exam_groups_views}</label>
					<div class="col-sm-20">
						<div class="boxcheck">
							<!-- BEGIN: groups_views -->
							<div class="clearfix">
								
								<label><input name="groups_view[]" type="checkbox" value="{GROUPS_VIEWS.key}" {GROUPS_VIEWS.checked}>{GROUPS_VIEWS.name}</label>
								
							</div>
							<!-- END: groups_views -->
						</div>
					</div>
				</div>                    
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-inhome">{LANG.group_exam_show_inhome}</label>
					<div class="col-sm-20">
						<select name="inhome" id="input-inhome" class="form-control btn-sm">
							<!-- BEGIN: inhome -->
							<option value="{INHOME.key}" {INHOME.selected}>{INHOME.name}</option>
							<!-- END: inhome -->
						</select>
					</div>
				</div>                    
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-status">{LANG.group_exam_show_status}</label>
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
					<a class="btn btn-primary btn-sm" href="{CANCEL}" title="{LANG.cancel}">{LANG.cancel}</a> 
				</div>          
			</form>
		</div>
	</div>
</div>

<!-- BEGIN: getalias -->
<script type="text/javascript">
//<![CDATA[

$("#inputs-title").change(function() {
	get_alias('group_exam', {DATA.group_exam_id});
});
//]]>
</script>
<!-- END: getalias -->

<!-- END: main -->