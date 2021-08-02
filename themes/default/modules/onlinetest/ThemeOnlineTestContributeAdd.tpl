<!-- BEGIN: main -->
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
				<button type="submit" form="form-stock" data-toggle="tooltip" class="btn btn-primary" title="{LANG.save}"><i class="fa fa-save"></i></button> 
				<a href="{CANCEL}" data-toggle="tooltip" class="btn btn-default fix" title="{LANG.cancel}"><i class="fa fa-reply"></i></a>
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
								<td style="width:130px">{LANG.question_group_exam}</td>
								<td>
									<select name="group_exam_id" id="group_exam_id" class="form-control">
										<option value="0"> {LANG.question_group_exam_select} </option>
										<!-- BEGIN: group_exam -->
										<option value="{GROUPEXAM.key}" {GROUPEXAM.selected} > {GROUPEXAM.name} </option>
										<!-- END: group_exam -->
									</select>
									<!-- BEGIN: error_group_exam --><div class="text-danger">{error_group_exam}</div><!-- END: error_group_exam -->
								</td>
							</tr>
							<tr class="required">
								<td style="width:130px">{LANG.question_category}</td>
								<td>
									<select name="category_id"  id="category_id" class="form-control">
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
								<td colspan="2">
								{LANG.question_question} | <a href="javascript:void(0);" onclick="createEditor('question')"><strong>{LANG.use_ckeditor}<strong></a>
								<!-- BEGIN: error_question --><div class="text-danger">{error_question}</div><!-- END: error_question -->
								
								</td>
							</tr>
							<tr class="required" >
								<td colspan="2">
									{DATA.question}
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<a class="btn btn-primary btn-xs" onclick="addAnalyzes($(this))" href="javascript:void(0);" >{LANG.add_analyzes}</a>
									<input type="hidden" name="addAnalyzes" value="{DATA.addAnalyzes}" >
								</td>
							</tr>
							<tr class="showAnalyzes {DATA.css_hide}" >
								<td colspan="2">	
									<div id="showAnalyzes">
									<!-- BEGIN: analyzes -->
									{ANALYZESS}
									<!-- END: analyzes -->
									</div>
								</td>
							</tr>
							
							<!-- BEGIN: loopAnswers -->
							<tr class="insertAnswers" id="boxanwser_{ANSWERS.key}"> 
								<td colspan="2">
									{ANSWERS.answer}
									<div style="float:left;margin-top: 10px;">
										
										<label><input class="answers" data-key="{ANSWERS.key}" type="checkbox" name="trueanswer_{ANSWERS.key}" id="trueanswer_{ANSWERS.key}" value="1" {ANSWERS.trueanswer}> {LANG.true_answers}</label>
										 | <label><a href="javascript:void(0);" onclick="createEditor('answer_{ANSWERS.key}');">{LANG.use_ckeditor}</a></label>
										<input type="hidden" name="getcontent[{ANSWERS.key}]" id="getcontent_{ANSWERS.key}" value="1" >
									</div>
									<div style="float:right;margin-top: 10px;">
										<a class="btn btn-danger btn-xs delete" href="javascript:void(0);" ><i class="fa fa-trash-o"></i> {LANG.delete}</a>
									</div>
								</td>
							</tr>
							<!-- END: loopAnswers -->
							<tr class="insertAnswer">
								<td colspan="2">
									<a onclick="addAnswers()" id="addAnswers" href="javascript:void(0);" class="btn btn-primary btn-xs" style="margin-top:10px">{LANG.add_answers}</a>
								</td>
							</tr> 
						</tbody>
					</table>
				</div>            
				<div align="center">
					<input id="button-submit" class="btn btn-primary" type="submit" value="{LANG.save}">
					<a class="btn btn-default fix" href="{CANCEL}" title="{LANG.cancel}">{LANG.cancel}</a> 
				</div>          
			</form>
		</div>
	</div>
</div>
 
<script type="text/javascript">
$('#form-question').on('submit', function(e){
	$('#button-submit').prop('disabled', true);
	if( $('#group_exam_id').val() == 0 )
	{
		alert('{LANG.question_error_group_exam_id}');
		return false;
	}
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
		removeButtons: 'Templates,Googledocs,NewPage,Preview,Print,Save,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Blockquote,Flash,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,Maximize,About,Anchor,BidiRtl,CreateDiv,Indent,BulletedList,NumberedList,Outdent,ShowBlocks,Youtube,Video' 
			
	});
	CKEDITOR.add;
}

var answer_row = {answer_row};
function addAnswers() {
	var html='';
	html += '<tr class="insertAnswers" id="boxanwser_' + answer_row + '">';
	html += '	<td colspan="2">';
	html += '		 <textarea name="answer_' + answer_row + '" id="answer_' + answer_row + '" class="form-control" style="height:32px"></textarea>';  					
	html += '		<div style="float:left;margin-top: 10px;">';
	html += '			<label><input type="checkbox" name="trueanswer_' + answer_row + '" id="trueanswer_' + answer_row + '" value="1" > {LANG.true_answers}</label>';
	html += '			| <label><a href="javascript:void(0);" onclick="createEditor(\'answer_' + answer_row + '\');">{LANG.use_ckeditor}</a></label>';
	html += '			<input type="hidden" name="getcontent[' + answer_row + ']" id="getcontent_' + answer_row + '" value="1">';
	html += '		</div>';
	html += '		<div style="float:right;margin-top: 10px;">';
	html += '			<a class="btn btn-danger btn-xs delete" href="javascript:void(0);" ><i class="fa fa-trash-o"></i> {LANG.delete}</a>';
	html += '		</div>';
	html += '	</td>';
	html += '</tr>';

	$(html).insertAfter('.insertAnswers:last');
 
	$('#boxanwser_' + answer_row).fadeIn(500);
	 
	answer_row++;
}

function addAnalyzes(obj) {
 
	var addAnalyzes = $('input[name="addAnalyzes"]').val();
	if( addAnalyzes == 1 )
	{
		$('#showAnalyzes').empty();
		$('.showAnalyzes').addClass('hides').removeClass('shows');
		$('input[name="addAnalyzes"]').val(0);
		obj.text('{LANG.add_analyzes}');
		
	}else{
		$('input[name="addAnalyzes"]').val(1);
		
		var html = '<textarea name="analyzes" id="analyzes" class="form-control"></textarea>';  					
		
		$('#showAnalyzes').append(html);
		$('.showAnalyzes').addClass('shows').removeClass('hides');
 
		createEditor('analyzes');
		
		obj.text('{LANG.analyzes_delete}');
	}
	
 
}

$(document).on('click', '.delete', function(e){
	$(this).parent().parent().parent().remove();
	e.preventDefault();
})

$('.text-danger').each( function(key, item){
	$(this).prev().addClass('warning');
})
 
</script>
 
<!-- END: main -->