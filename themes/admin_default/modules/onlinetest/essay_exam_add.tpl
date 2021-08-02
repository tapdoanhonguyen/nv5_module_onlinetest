<!-- BEGIN: main -->
<div id="typeexam-content">
	<!-- BEGIN: warning -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {WARNING}<i class="fa fa-times"></i>        
    </div>
    <!-- END: warning -->
    <!-- BEGIN: error_other -->
    <div class="alert alert-danger">
        <!-- BEGIN: loop -->
		<div class="clearfix"><i class="fa fa-exclamation-circle"></i> {ERROR}<i class="fa fa-times"></i></div>
		<!-- END: loop -->		
    </div>
    <!-- END: error_other -->
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
			<form action="" method="post"  enctype="multipart/form-data" id="form-essay_exam" class="form-horizontal">
				<input type="hidden" name ="essay_exam_id" value="{DATA.essay_exam_id}" />
				<input name="save" type="hidden" value="1" />
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<tbody>
							<tr class="required">
								<td style="width:180px"><div class="title">{LANG.essay_exam_title}</div></td>
								<td>
									<input type="text" name="title" value="{DATA.title}" class="form-control"> 
									<!-- BEGIN: error_title --><div class="text-danger">{error_title}</div><!-- END: error_title -->
								</td>
							</tr>
							<tr class="required">
								<td style="width:180px"><div class="title">{LANG.essay_exam_group_exam}</div></td>
								<td>
									<div class="message_body" style="height:260px; overflow: auto; max-width:600px">
										<table class="table table-striped table-bordered table-hover">
											<tbody>
												<!-- BEGIN: group_exam -->
												<tr>
													<td><label><input style="margin-left: {GROUPEXAM.space}px;" type="checkbox" value="{GROUPEXAM.key}" name="group_exam_list[]" {GROUPEXAM.checked} class="news_checkbox"> {GROUPEXAM.name} </label></td>
													<td><input id="group_right_{GROUPEXAM.key}" style="{GROUPEXAM.display}" type="radio" name="group_exam_id"value="{GROUPEXAM.key}" {GROUPEXAM.group_checked} data-toggle="tooltip" data-placement="top" title="{LANG.essay_exam_check_group}" /></td>
										
												</tr> 
												<!-- END: group_exam -->
											</tbody>
										</table>
									</div>
									<!-- BEGIN: error_group_exam --><div class="text-danger">{error_group_exam}</div><!-- END: error_group_exam -->
								</td>
							</tr>			 
							<tr class="norequired">
								<td style="width:180px"><div class="title">{LANG.essay_exam_group_user}</div></td>
								<td>
									<div style="position:relative;width:100%;">
										<input type="text" name="grouplist" value="" placeholder="{LANG.group_user_select}" id="grouplist" class="form-control input-sm" />
										<div id="group-users" class="well well-sm">
											<!-- BEGIN:group -->
											<div id="group-user{GROUP.group_user_id}"><i class="fa fa-minus-circle"></i> {GROUP.title}
											<input type="hidden" name="group_user[]" value="{GROUP.group_user_id}"></div>
											<!-- END:group -->
										</div>
									</div>  
								</td>
							</tr>	
							
							<tr class="norequired">
								<td style="width:180px"><div class="title">{LANG.essay_exam_point}</div></td>
								<td>
									<input type="text" name="point" value="{DATA.point}" class="form-control numberonly" maxlength="8" > 
									
								</td>
							</tr>
							<tr class="norequired">
								<td style="width:180px"><div class="title">{LANG.essay_exam_images}</div></td>
								<td>
									<div class="input-group">
										<input type="text" name="images" id="images" value="{DATA.images}" class="form-control"> 
										<label class="input-group-btn">
											<span class="btn btn-info">
												{LANG.essay_exam_select_images}<input id="selectimage" type="button" style="display: none;">
											</span>
										</label>
									</div>
								</td>
							</tr>							
							<tr class="norequired">
								<td style="width:180px"><div class="title">{LANG.essay_exam_introtext}</div></td>
								<td>
									<textarea name="introtext" class="form-control">{DATA.introtext}</textarea>
								</td>
							</tr>							
							<tr class="norequired">
								<td style="width:180px"><div class="title">{LANG.essay_exam_keywords}</div></td>
								<td>
									<input type="text" name="keywords" value="{DATA.keywords}" class="form-control" data-toggle="tooltip" data-placement="top" title="{LANG.essay_exam_help_keywords}"> 
								</td>
							</tr>							
							<tr class="norequired">
								<td style="width:180px"><div class="title">{LANG.essay_exam_status}</div></td>
								<td>
									<select name="status" id="input-status" class="form-control">
										<!-- BEGIN: status -->
										<option value="{STATUS.key}" {STATUS.selected}>{STATUS.name}</option>
										<!-- END: status -->
									</select>  
								</td>
							</tr>
							<tr class="required">
								<td style="width:180px"><div class="title">{LANG.essay_exam_time}</div></td>
								<td>
									<input type="text" name="time" value="{DATA.time}" class="form-control numberonly" maxlength="3" data-toggle="tooltip" data-placement="top" title="{LANG.essay_exam_help_time}"> 
									<!-- BEGIN: error_time --><div class="text-danger">{error_time}</div><!-- END: error_time -->
								</td>
							</tr>
							<tr class="required">
								<td style="width:180px"><div class="title">{LANG.essay_exam_num_question}</div></td>
								<td>
									<input type="text" name="num_question" id="num_question" value="{DATA.num_question}" class="form-control numberonly" maxlength="3"> 
									<!-- BEGIN: error_num_question --><div class="text-danger">{error_num_question}</div><!-- END: error_num_question -->
								</td>
							</tr>
	
						</tbody>
					</table>
				</div>
				
				
	
				
				<div id="type1">
					
					
					<div class="col-sm-14">
						<div class="config">{LANG.essay_exam_listq}</div>
						<div id="question-block" class="well well-sm scrollbar question-block" style="height: 300px; overflow: auto;">
							<ol class="simple_with_animation">
								<!-- BEGIN: question -->
								<li class="question-item" id="question-block{QUESTION.essay_id}">
									<i class="fa fa-minus-circle"></i> {QUESTION.question}
									<input type="hidden" name="question_list[]" value="{QUESTION.essay_id}"> <span class="level{QUESTION.level_id}"> {QUESTION.level}</span>
								</li>
								<!-- END: question -->
							</ol>
						</div>
					</div> 
					<div class="col-sm-10">
						<div class="config">{LANG.essay_exam_select}</div>
						<div class="boxsgroup scrollbar">
							<div id="searchbox"> 
								<div class="boxselect dropdown">
									<div class="selection dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
										Chọn chủ đề câu hỏi
									</div>
									<ul class="dropdown-list fadeInDown animated faster">
										<!-- BEGIN: category_search -->
										<li>
											<label>
												<input type="checkbox" name="typelist[]" value="{CATEGORY.key}" />
												<span> {CATEGORY.name}</span>
											</label>
										</li>
										<!-- END: category_search --> 
										 
									</ul>
									<span class="select-arrow"><i class="fa fa-angle-down fadeInUp animated fast" aria-hidden="true"></i></span>
								</div>
								<div class="input-groupx">
									<input type="text" name="keyword" value="" placeholder="Chọn câu hỏi" id="keyword" class="form-control" />
									<div class="input-group-append">
										<button class="btn btn-info" type="button" id="search">
											<em class="fa fa-search fa-lg"></em>
										</button>
									</div>
								</div>
							</div>
							<div id="question-block2" class=" question-block">
								<ol id="question2" class="simple_with_animation">
								</ol>
								<div id="generate_page"></div>
							</div>
						</div>
					
					</div>
	
				</div>
				<div class="clearfix"></div>
				
				<div class="config">{LANG.essay_exam_rules}</div>
				<div class="rules">
					 {DATA.rules}
				</div>	
      
				<div align="center" style="margin-top:10px">
					<input class="btn btn-primary" id="submitform" type="submit" value="{LANG.save}">
					<a class="btn btn-default" href="{CANCEL}" title="{LANG.cancel}">{LANG.cancel}</a> 
				</div>          
			</form>
		</div>
	</div>
</div>
<script type="text/javascript" src="{NV_BASE_SITEURL}themes/admin_default/js/jquery-sortable.js"></script>
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
<script  type="text/javascript" >
MathJax = {
  tex: {
    inlineMath: [['$', '$'], ['\\(', '\\)']]
  }
};
$(document).ready(function(){
	setTimeout(function(){$('.math-tex').show()}, 400)
})
</script>
<script type="text/javascript" async  src="{NV_BASE_SITEURL}MathJax-2.7.8/MathJax.js?config=TeX-AMS_HTML"></script> 
<script type="text/javascript">

var abc = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'S', 'T', 'U', 'V', 'W', 'S', 'W', 'Z'];
	

function isNumberOnly(a, c) {
    var b = a.which ? a.which : a.keyCode;
    return 47 < b && 58 > b ? !0 : !1
};
function isNumber(a,c){var b=a.which?a.which:a.keyCode;return 47<b&&58>b||46==b&&-1==$(c).val().indexOf('.')?!0:!1};
 
$('.number').on('keypress', function(e){
	return isNumber(e, this);
});
$('.numberonly').on('keypress', function(e){
	return isNumberOnly(e, this);
});


$(document).on('click', '.boxsgroup2 input.checkbox', function(){

	if( $(this).prop('checked') )
	{
		$(this).parent().addClass('checked');
	}else{
		$(this).parent().removeClass('checked');
	}	
})
$(document).on('click', '.boxsgroup2 i.fa-minus-circle', function(){
 
	id = $(this).attr('data');
	$('#item'+id).find('label:last').remove();
 
})

	

$(document).on('click', '.boxsgroup2 i.fa-plus-circle', function(){
	var id = $(this).attr('data');
	var ans = $('#item'+id).find('label').length;
	
	tmp='<label><span class="title">'+ abc[ans] +'</span><input type="checkbox" class="hide checkbox" name="trueanswer['+ id +'][]" value="1" ><input type="hidden" value="'+ans+'" data-ans="'+ abc[ans] +'" data-id="'+ id +'" name="answers_list['+ id +'][]"></label>';		
	$('#item'+id).find('.labelx').append(tmp);
 
})

$('input[name="typelist[]"]').on('click', function(){
	
	if( $('input[name="typelist[]"]:checked').length > 0 )
	{
		$('.selection').addClass('bold');
	}
	else{
		
		$('.selection').removeClass('bold');
	}
 
})

$('.boxselect').on('click', function(){

	if(  $(this).hasClass('open') ) 
	{
		$(this).find('i').removeClass('fa-angle-up fadeInDown animated fast').addClass('fa-angle-down fadeInUp animated fast')
	}
	else  
	{
		$(this).find('i').removeClass('fa-angle-down fadeInUp animated fast').addClass('fa-angle-up fadeInDown animated fast')
	}
})
$(document).on('click', function (e) {
    if ( $(e.target).closest(".boxselect").length === 0 && $('.boxselect.open').length > 0 ) 
	{
		//$('.boxselect span').html('<i class="fa fa-angle-down fadeInUp animated fast" aria-hidden="true"></i>');
    }
}); 
 
$(function  () {
  var adjustment;

  $(".simple_with_animation").sortable({
	group: 'simple_with_animation',
	pullPlaceholder: false,
	onDrop: function  ($item, container, _super) {
	  var $clonedItem = $('<li/>').css({height: 0});
	  $item.before($clonedItem);
	  $clonedItem.animate({'height': $item.height()});
 
	  $item.animate($clonedItem.position(), function  () {
		$clonedItem.detach();
		_super($item, container);
	  });
	 
	  if( $('#question-block>ol').find('li[id="'+ $item.attr('id') +'"]' ).length > 1 )
	  {
			$item.remove();
	  }
	  
	},
	onDragStart: function ($item, container, _super) {
	  var offset = $item.offset(),
		  pointer = container.rootGroup.pointer;

	  adjustment = {
		left: pointer.left - offset.left,
		top: pointer.top - offset.top
	  };

	  _super($item, container);
	},
	onDrag: function ($item, position) {
		
	  $item.css({
		left: position.left - adjustment.left,
		top: position.top - adjustment.top
	  });
	}
  });
});
 
 
 
$('.select2').select2();

$(document).on('submit', '#form-essay_exam', function() {
	$('#question2').empty();
	 
});
 
$(document).on('dblclick', '#question2 li', function() {

	if( $('#question-block>ol').find('#' + $(this).attr('id') ).length == 0 )
	{
		$('#question-block>ol').append( $(this).eq(0).clone() );
	}
    
});
 
$('.question-block').delegate('.fa-minus-circle', 'click', function() {
    $(this).parent().remove();
});
 
$(document).delegate('#search', 'click', function(){
   $.ajax({
		url: script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=essay_exam&action=getQuestion&nocache=' + new Date().getTime(),
		data: $('#searchbox input[type=\'text\'],#searchbox input[type=\'hidden\'], #searchbox input[type=\'checkbox\']:checked'),
		dataType: 'json',
		success: function(json) {
			
			if( json['data'] )
			{
				var temp='';
				$.each( json['data'] , function(i, item){
					temp+='<li class="question-item" id="question-block' + item['essay_id'] + '"><i class="fa fa-minus-circle"></i> ' + item['question'] + '<input type="hidden" name="question_list[]" value="' + item['essay_id'] + '" /></li>';
				})
				$('#question2').html( temp );
				if(typeof MathJax !== 'undefined') {MathJax.Hub.Queue(["Typeset",MathJax.Hub]);setTimeout(function(){$('.math-tex').show()}, 400)}
 
			}
			else if( json['data'] )
			{
				$('#question2').empty();
			}
			if( json['generate_page'] )
			{
				$('#generate_page').html( json['generate_page'] );
			}
			else if( json['generate_page'] == '')
			{
				$('#generate_page').empty( );
			}
		}
	});
});
 

function showContent ( page, id )
{
	
	$.ajax({
		type: "POST",
		dataType: 'json',
		url: script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=essay_exam&action=getQuestion&page='+ page + '&nocache=' + new Date().getTime(),
		data: $('#searchbox input[type=\'text\'],#searchbox input[type=\'hidden\'], #searchbox input[type=\'checkbox\']:checked'),
		beforeSend: function() {
			 $('.boxsgroup ').css('opacity', '0.7'); 
			 $('#search').prop('disabled', true);
			
		},	
		complete: function() {
			$('.boxsgroup ').css('opacity', '1'); 
			$('#search').prop('disabled', false);
		},
		success: function(json) {
			 
			if( json['data'] )
			{
				var temp='';
				$.each( json['data'] , function(i, item){
					temp+='<li class="question-item" id="question-block' + item['essay_id'] + '"><i class="fa fa-minus-circle"></i> ' + item['question'] + '<input type="hidden" name="question_list[]" value="' + item['essay_id'] + '" /> <span class="level' + item['level_id'] + '"> ' + item['level'] + '</span></li>';
				})
				$('#question2').html( temp );

				if(typeof MathJax !== 'undefined') {MathJax.Hub.Queue(["Typeset",MathJax.Hub]);setTimeout(function(){$('.math-tex').show()}, 400)}
			}
			else if( json['data'] )
			{
				$('#question2').empty();
			}
			if( json['generate_page'] )
			{
				$('#generate_page').html( json['generate_page'] );
			}
			else if( json['generate_page'] == '')
			{
				$('#generate_page').empty( );
			}
			 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});

}

function checklevel(category_id)
{
	$('input[data-category="'+category_id+'"]').each( function(key, item){
		if( $(this).prop('checked') )
		{
			$('#level-' + category_id +'-'+$(this).val()).prop('disabled', false);
			
		}else{
			$('#level-' + category_id +'-' + $(this).val()).prop('disabled', true);
			$('#level-' + category_id +'-' + $(this).val()).val('');
		}
	})

} 
$('.getcategory').on('click', function(e){
	var category_id = $(this).val();
	if( $(this).prop('checked') )
	{
		$('input[data-category="'+category_id+'"]').prop('disabled', false).parent().parent().removeClass('disabled');
		checklevel(category_id);
	}else{
		$('input[data-category="'+category_id+'"]').prop('disabled', true).parent().parent().addClass('disabled');	
		
		$('.boxlevel2.c' + category_id +' input').prop('disabled', true);
	}
	
}) 
<!-- BEGIN: script_edit -->
$('.getcategory').each( function(e, item){
	var category_id = $(this).val();
	if( $(this).prop('checked') )
	{
		$('input[data-category="'+category_id+'"]').prop('disabled', false).parent().parent().removeClass('disabled');
		checklevel(category_id);
	}else{
		$('input[data-category="'+category_id+'"]').prop('disabled', true).parent().parent().addClass('disabled');	
		$('.boxlevel2.c' + category_id +' input').prop('disabled', true);
	}
	
}) 

<!-- END: script_edit -->
 
$('.text-danger').each( function(key, item){
	$(this).prev().addClass('warning');
})

var clear = '';

function imageRefresh( )
{
	if( $('#pdf').val() != '' && $('#pdf').val() != $('#pdf').attr('data-url') )
	{
		$('#pdfview').attr('src', script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=essay_exam&action=pdfview&url=' + $('#pdf').val() );
		$('#pdf').attr('data-url', $('#pdf').val());
		clearInterval(window.clear); 
	}	
}


 
	

$("#selectimage").click(function() {
	var area = "images";
	var path = "{NV_UPLOADS_DIR}/{MODULE_UPLOAD}";
	var currentpath = "{CURRENT}";
	var type = "image";
	nv_open_browse("{NV_BASE_ADMINURL}index.php?{NV_NAME_VARIABLE}=upload&popup=1&area=" + area + "&path=" + path + "&type=" + type + "&currentpath=" + currentpath, "NVImg", 850, 500, "resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
	return false;
});
$("input[name='group_exam_list[]']").click(function() {
	var group_exam_id = $("input:radio[name=group_exam_id]:checked").val();
	var radios_group_exam_id = $("input:radio[name=group_exam_id]");
	var exam_list = [];
	$("input[name='group_exam_list[]']").each(function() {
			if ($(this).prop('checked')) {
				$("#group_right_" + $(this).val()).show();
				exam_list.push($(this).val());
			} else {
				$("#group_right_" + $(this).val()).hide();
				if ($(this).val() == group_exam_id) {
					radios_group_exam_id.filter("[value=" + group_exam_id + "]").prop("checked", false);
				}
			}
	});

	if (exam_list.length > 1) {
		for ( i = 0; i < exam_list.length; i++) {
			$("#group_right_" + exam_list[i]).show();
		};
		group_exam_id = parseInt($("input:radio[name=group_exam_id]:checked").val() + "");
		if (!group_exam_id) {
			radios_group_exam_id.filter("[value=" + exam_list[0] + "]").prop("checked", true);
		}
	}
	if( $('input[name="group_exam_list[]"]:checked').length == 1 )
	{
		$('#group_right_' + $('input[name="group_exam_list[]"]:checked').val()).prop('checked', true);
	}	
});

$('#grouplist').autofill({
	'source': function(request, response) {	 
		$.ajax({
			url: script_name + '?' + nv_name_variable + '='+ nv_module_name  +'&' + nv_fc_variable + '=essay_exam&action=group_user&title='+ encodeURIComponent(request) +'&nocache=' + new Date().getTime(),		
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
		$('input[name=\'grouplist\']').val('');
		
		$('#group-user' + item['value']).remove();
		
		$('#group-users').append('<div id="group-user' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="group_user[]" value="' + item['value'] + '" /></div>');	
	
		
	}
}); 
$('#group-users').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});	

</script>
 
<!-- END: main -->