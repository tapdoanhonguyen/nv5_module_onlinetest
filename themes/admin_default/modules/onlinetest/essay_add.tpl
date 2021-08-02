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
			<h3 class="panel-title" style="float:left"><i class="fa fa-pencil"></i> {CAPTION}</h3>
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
								<td style="width:130px">{LANG.essay_category}</td>
								<td>
									<select name="category_id" id="category_id" class="form-control select2">
										<option value="0"> {LANG.essay_category_select} </option>
										<!-- BEGIN: category -->
										<option value="{CATEGORY.key}" {CATEGORY.selected} > {CATEGORY.name} </option>
										<!-- END: category -->
									</select>
									<!-- BEGIN: error_category --><div class="text-danger">{error_category}</div><!-- END: error_category -->
								</td>
							</tr>
							 
 
							<tr>
								<td style="width:130px">{LANG.essay_status}</td>
								<td>
									<select name="status" id="input-status" class="form-control">
										<!-- BEGIN: status -->
										<option value="{STATUS.key}" {STATUS.selected}>{STATUS.name}</option>
										<!-- END: status -->
									</select>  
								</td>
							</tr>
							<tr>
								<td colspan="2">
								{LANG.essay_question} | <a href="javascript:void(0);" onclick="createEditor('question')"><strong>{LANG.use_ckeditor}<strong></a>
								<!-- BEGIN: error_question --><div class="text-danger">{error_question}</div><!-- END: error_question -->
								
								</td>
							</tr>
							<tr class="required" >
								<td colspan="2">
									{DATA.question}
								</td>
							</tr>
							 
						</tbody>
					</table>
				</div>
                     
				<div align="center">
					<input id="button-submit" class="btn btn-primary" type="submit" value="{LANG.save}">
					<a class="btn btn-default" href="{CANCEL}" title="{LANG.cancel}">{LANG.cancel}</a> 
				</div>          
			</form>
		</div>
	</div>
</div>

<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
 
<script type="text/javascript">

$(document).ready(function() {
	$('.select2').select2({
		language: '{NV_LANG_INTERFACE}'
	});
});

$('#form-question').on('submit', function(e){
	
	$('#button-submit').prop('disabled', true);
	setTimeout(function(){
		$('#button-submit').prop('disabled', false);
	}, 3000)
	
	if( $('#category_id').val() == 0 )
	{
		alert('{LANG.essay_error_category_id}');
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
		alert('{LANG.essay_error_question}');
		return false;
	}
	
	
	
});

function createEditor(element) {
	CKEDITOR.replace( element, {
		width: '100%',
		height: '100px',
		toolbarGroups:[
			{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
			{ name: 'forms', groups: [ 'forms' ] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
			{ name: 'links', groups: [ 'links' ] },
			{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'paragraph' ] },
			{ name: 'insert', groups: [ 'insert' ] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
			{ name: 'styles', groups: [ 'styles' ] },
			{ name: 'colors', groups: [ 'colors' ] },
			{ name: 'tools', groups: [ 'tools' ] },
			{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
			{ name: 'others', groups: [ 'others' ] },
			{ name: 'about', groups: [ 'about' ] }
		],
		removePlugins: 'autosave,gg,switchbar',
		removeButtons: 'Templates,Googledocs,NewPage,Preview,Print,Save,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Blockquote,Flash,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,Maximize,About,Anchor,BidiRtl,CreateDiv,Indent,BulletedList,NumberedList,Outdent,ShowBlocks,Youtube,Video', 
		<!-- BEGIN: filebrowserUploadUrl -->
		{filebrowserUploadUrl}
		<!-- END: filebrowserUploadUrl -->
		<!-- BEGIN: filebrowserImageUploadUrl -->
		{filebrowserImageUploadUrl}
		<!-- END: filebrowserImageUploadUrl -->
	 	<!-- BEGIN: filebrowserFlashUploadUrl -->
		{filebrowserFlashUploadUrl}
		<!-- END: filebrowserFlashUploadUrl -->
		{filebrowserBrowseUrl}
		{filebrowserImageBrowseUrl}
		{filebrowserFlashBrowseUrl}
		
	});
	CKEDITOR.add;
	
	
}


$('.text-danger').each( function(key, item){
	$(this).prev().addClass('warning');
})
 

</script>
 
<!-- END: main -->