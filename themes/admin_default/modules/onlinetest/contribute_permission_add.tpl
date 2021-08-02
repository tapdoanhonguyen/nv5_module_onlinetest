<!-- BEGIN: main -->
<div id="contributepermission-content">
    <!-- BEGIN: error_warning -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {WARNING} <class="fa fa-times"></i>
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
			<form action="" method="post" enctype="multipart/form-data" id="form-category" class="form-horizontal">
				<input type="hidden" name ="category_id" value="{DATA.category_id}" />
				<input type="hidden" name ="parentid_old" value="{DATA.parent_id}" />
				<input name="save" type="hidden" value="1" />
 
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<td style="width:180px">
									<strong>{LANG.contribute_permission_group}</strong>
								</td>
								<td>
									<strong>{LANG.contribute_permission_permission}</strong>
								</td>
								
							</tr>	
						</thead>
						<tbody>
							<!-- BEGIN: group -->
							<tr>
								<td style="vertical-align: middle;">
									<label><input type="checkbox" class="getcategory" name="permission[{GROUP.key}][group_id]" value="{GROUP.key}" {GROUP.checked}>{GROUP.name}</label>
								</td>
								<td>
									<!-- BEGIN: permission -->
									<div class="boxlevel1"><label><input class="getlevel" type="checkbox" name="permission[{GROUP.key}][{PERMISSION.key}]" value="1" {PERMISSION.checked} >{PERMISSION.name}</label></div>				
									<!-- END: permission -->
								</td>
								 
							</tr>
							<!-- END: group -->							
						</tbody>
					</table>
				</div>                   
				       
			</form>
		</div>
	</div>
</div>
 
<!-- END: main -->