<!-- BEGIN: main -->
<div id="report-content">
    <!-- BEGIN: error_warning -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {error_warning}<i class="fa fa-times"></i>
    </div>
    <!-- END: error_warning -->
    <!-- BEGIN: error_reply -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {error_reply}<i class="fa fa-times"></i>
    </div>
    <!-- END: error_reply -->
    <!-- BEGIN: error_sendmail -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {error_sendmail}<i class="fa fa-times"></i>
    </div>
    <!-- END: error_sendmail -->
    <div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-pencil"></i> {CAPTION}</h3>
			<div class="pull-right">
				<button type="submit" form="form-stock" data-toggle="tooltip" class="btn btn-primary" title="{LANG.save}"><i class="fa fa-save"></i></button> 
				<a href="{CANCEL}" data-toggle="tooltip" class="btn btn-default" title="{LANG.cancel}"><i class="fa fa-reply"></i></a>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="" method="post"  enctype="multipart/form-data" id="form-report" class="form-horizontal">
				<input type="hidden" name ="report_id" value="{DATA.report_id}" />
				<input type="hidden" name ="token" value="{DATA.token}" />
				<input name="save" type="hidden" value="1" />
 
				<div class="form-group reply">
					<h2>{DATA.title}</h2>
					{DATA.note}
				</div>
				<div style="font-size: 16px;"><i class="fa fa-paper-plane" aria-hidden="true"></i> <strong>Phản Hồi</strong></div>
				<div class="form-group reply">
					{DATA.reply}
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
 
<!-- END: main -->