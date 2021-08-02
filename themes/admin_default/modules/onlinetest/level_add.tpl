<!-- BEGIN: main -->
<div id="level-content">
    <!-- BEGIN: error_warning -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {error_warning}<i class="fa fa-times"></i>
    </div>
    <!-- END: error_warning -->
    <div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-pencil"></i> {CAPTION}</h3>
			<div class="pull-right">
				<button type="submit" form="form-stock" data-toggle="tooltip" class="btn btn-primary btn-sm" title="{LANG.save}"><i class="fa fa-save"></i></button> 
				<a href="{CANCEL}" data-toggle="tooltip" class="btn btn-default btn-sm" title="{LANG.cancel}"><i class="fa fa-reply"></i></a>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="" method="post"  enctype="multipart/form-data" id="form-level" class="form-horizontal">
				<input type="hidden" name ="level_id" value="{DATA.level_id}" />
				<input name="save" type="hidden" value="1" />
 
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="inputs-title">{LANG.level_title}</label>
					<div class="col-sm-20">
						<input type="text" name="title" value="{DATA.title}" placeholder="{LANG.level_title}" id="inputs-title" class="form-control btn-sm" />
						<!-- BEGIN: error_title --><div class="text-danger">{error_title}</div><!-- END: error_title -->
					</div>
				</div> 
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-description">{LANG.level_description}</label>
					<div class="col-sm-20">
						<input type="text" name="description" value="{DATA.description}" placeholder="{LANG.level_description}" id="input-description" class="form-control btn-sm" />
						<!-- BEGIN: error_description --><div class="text-danger">{error_description}</div><!-- END: error_description -->
					</div>
				</div> 
				                  	 
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-status">{LANG.level_status}</label>
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
<!-- END: main -->