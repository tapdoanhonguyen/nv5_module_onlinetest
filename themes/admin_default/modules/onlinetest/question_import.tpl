<!-- BEGIN: main -->
<script type="text/javascript" src="http://cdn.mathjax.org/mathjax/2.6-latest/MathJax.js?config=TeX-AMS_HTML"></script>

<div id="question-content">
     <!-- BEGIN: error_warning -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {WARNING}<i class="fa fa-times"></i>        
    </div>
    <!-- END: error_warning -->
    <div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left;text-transform: uppercase;"><i class="fa fa-pencil"></i> {LANG.question_import}</h3>
			<div class="pull-right">
				<button type="submit" form="form-stock" data-toggle="tooltip" class="btn btn-primary" title="Save"><i class="fa fa-save"></i></button> 
				<a href="{CANCEL}" data-toggle="tooltip" class="btn btn-default" title="{LANG.cancel}"><i class="fa fa-reply"></i></a>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="" method="post"  enctype="multipart/form-data" id="form-question" class="form-horizontal">
				<input type="hidden" name ="question_id" value="{DATA.question_id}" />
				<input name="save" type="hidden" value="1" />
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<tbody>
 
							<tr class="required">
								<td style="width:130px">{LANG.question_category}</td>
								<td>
									<select name="category_id" id="category_id" class="form-control">
										<option value="0"> {LANG.question_category_select} </option>
										<!-- BEGIN: category -->
										<option value="{CATEGORY.key}" {CATEGORY.selected} > {CATEGORY.name} </option>
										<!-- END: category -->
									</select>
									<!-- BEGIN: error_category --><div class="text-danger">{error_category}</div><!-- END: error_category -->
								</td>
							</tr>
							<tr class="required" >
								<td style="width:130px">{LANG.question_level}</td>
								<td>
									<select name="level_id" id="level_id" class="form-control">
										<option value="0"> {LANG.question_level_select} </option>
										<!-- BEGIN: level -->
										<option value="{LEVEL.key}" {LEVEL.selected} > {LEVEL.name} </option>
										<!-- END: level -->
									</select>  
									<!-- BEGIN: error_level --><div class="text-danger">{error_level}</div><!-- END: error_level -->	
								</td>
							</tr>
 
							<tr>
								<td style="width:130px">{LANG.question_files}</td>
								<td>
									<input type="file" name="files"  class="form-control" >
									<!-- BEGIN: error_files --><div class="text-danger">{error_files}</div><!-- END: error_files -->
								
									<div><a href="{DEFAULT_FORM_IMPORT}" style="color:red;font-weight:bold;padding: 4px 0;display:block">Tải file mẫu tại đây</a></div>
								</td>
							</tr>
							<tr>
								<td style="width:130px">{LANG.question_duplicate}</td>
								<td>
								
									<input type="checkbox" name="duplicate" value="1" {DUPLICATE_CHECKED} class="form-control" >
									 
								</td>
							</tr>
							
							 
						</tbody>
					</table>
				</div>
                     
				<div align="center">
					<input type="hidden" name="save" value="1">
					<input type="hidden" name="action" value="import">
					<input id="button-submit" class="btn btn-primary" type="submit" value="{LANG.save}">
					<a class="btn btn-default" href="{CANCEL}" title="{LANG.cancel}">{LANG.cancel}</a> 
				</div>          
			</form>
		</div>
	</div>
</div>
 
<script type="text/javascript">
$('#form-question').on('submit', function(e){
	$('#button-submit').prop('disabled', true);
 
	if( $('#category_id').val() == 0 )
	{
		alert('{LANG.question_error_category_id}');
		return false;
	}
	if( $('#level_id').val() == 0 )
	{
		alert('{LANG.question_error_level_id}');
		return false;
	}
	
	var qeditor = CKEDITOR.instances['question'];  
	if( typeof(qeditor) === 'undefined' )
	{
		var question = $('#question').val();
	}else 
	{
		var question = CKEDITOR.instances['question'].getData();
	}
 
	if( strip_tags( question, '<img>') == '' )
	{
		alert('{LANG.question_error_question}');
		return false;
	}
 
	var is_checked = 0;
	var row = 1;
	var checkcontent = 1;
	$('input.answers').each( function( i, item )
	{
		
		if( $( this ).prop('checked') )
		{
			++is_checked;
		}
		var editor = CKEDITOR.instances['answer_' + row];  
		if( typeof(editor) === 'undefined' )
		{
			var answer = $('#answer_' + row).val();
		}else 
		{
			var answer = CKEDITOR.instances['answer_' + row].getData();
		}
		if( strip_tags( answer, '<img>').length != '' )
		{
			++checkcontent;
		}
		++row;
		
	}); 
	if( is_checked == 0 )
	{
		alert('{LANG.not_checked_answers}');
		$('#button-submit').prop('disabled', false);
		e.preventDefault();
		return ;
	}else if( checkcontent != row )
	{
		alert('{LANG.not_content_answers}');
		$('#button-submit').prop('disabled', false);
		e.preventDefault();
		return ;
	}	 
	
})
 
 
</script>
 
<!-- END: main -->