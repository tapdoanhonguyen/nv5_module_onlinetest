<!-- BEGIN: main -->
<div id="photo-content">
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
			<form action="" method="post"  enctype="multipart/form-data" id="form-category" class="form-horizontal">
				<input type="hidden" name ="category_id" value="{DATA.category_id}" />
				<input type="hidden" name ="parentid_old" value="{DATA.parent_id}" />
				<input name="save" type="hidden" value="1" />
 
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="inputs-title">{LANG.category_title}</label>
					<div class="col-sm-20">
						<input type="text" name="title" value="{DATA.title}" placeholder="{LANG.category_title}" id="inputs-title" class="form-control btn-sm" />
						<!-- BEGIN: error_title --><div class="text-danger">{error_title}</div><!-- END: error_title -->
					</div>
				</div>
				<div class="form-group">
                    <label class="col-sm-4 control-label" for="input-alias">{LANG.category_alias}</label>
                    <div class="col-sm-20">
						<div class="input-group">
							<input class="form-control btn-sm" name="alias" placeholder="{LANG.category_alias}"  type="text" value="{DATA.alias}" maxlength="255" id="input-alias"/>
							<div class="input-group-addon fixaddon">
								&nbsp;<em class="fa fa-refresh fa-lg fa-pointer text-middle" onclick="get_alias( );">&nbsp;</em>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-parent">{LANG.category_sub}</label>
					<div class="col-sm-20">
						<select class="form-control btn-sm" name="parent_id">
							<option value="0">{LANG.category_sub_sl}</option>
							<!-- BEGIN: category -->
							<option value="{CATEGORY.key}" {CATEGORY.selected}>{CATEGORY.name}</option>
							<!-- END: category -->
						</select>
					</div>
				</div>			
 		
                <div class="form-group">
                     <label class="col-sm-4 control-label" for="input-description">{LANG.category_description} </label>
                     <div class="col-sm-20">
                          <textarea name="description" rows="2" placeholder="{LANG.category_description}" id="input-description" class="form-control btn-sm">{DATA.description}</textarea>
						  <!-- <span class="text-middle"> {GLANG.length_characters}: <span id="descriptionlength" class="red">0</span>. {GLANG.description_suggest_max} </span> -->            
                      </div>
                 </div>
                 <div class="form-group">
					<label class="col-sm-4 control-label" for="input-meta-title">{LANG.category_meta_title}</label>
					<div class="col-sm-20">
						<input type="text" name="meta_title" value="{DATA.meta_title}" placeholder="{LANG.category_meta_title}" id="input-meta-title" class="form-control btn-sm" />
						<!-- BEGIN: error_meta_title--><div class="text-danger">{error_meta_title}</div><!-- END: error_meta_title -->
					</div>
                 </div>
				 <div class="form-group">
					<label class="col-sm-4 control-label" for="input-meta-description">{LANG.category_meta_description}</label>
					<div class="col-sm-20">
						<textarea name="meta_description" rows="2" placeholder="{LANG.category_meta_description}" id="input-meta-description" class="form-control btn-sm">{DATA.meta_description}</textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-meta-keyword">{LANG.category_meta_keyword}</label>
					<div class="col-sm-20">
						<textarea name="meta_keyword" rows="2" placeholder="{LANG.category_meta_keyword}" id="input-meta-keyword" class="form-control btn-sm">{DATA.meta_keyword}</textarea>
					</div>
				</div>
                    	 
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-status">{LANG.category_show_status}</label>
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
	get_alias('category', {DATA.category_id});
});
//]]>
</script>
<!-- END: getalias -->

<!-- END: main -->