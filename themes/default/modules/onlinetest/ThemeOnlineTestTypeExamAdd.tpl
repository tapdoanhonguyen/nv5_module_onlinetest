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
			<form action="" method="post"  enctype="multipart/form-data" id="form-type_exam" class="form-horizontal">
				<input type="hidden" name ="type_exam_id" value="{DATA.type_exam_id}" />
				<input name="save" type="hidden" value="1" />
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<tbody>
							<tr class="required">
								<td style="width:180px"><div class="test-title">{LANG.type_exam_title}</div></td>
								<td>
									<input type="text" name="title" value="{DATA.title}" class="form-control"> 
									<!-- BEGIN: error_title --><div class="text-danger">{error_title}</div><!-- END: error_title -->
								</td>
							</tr>
							<tr class="required">
								<td style="width:180px"><div class="test-title">{LANG.type_exam_group_exam}</div></td>
								<td>
									<div class="message_body" style="height:260px; overflow: auto; max-width:600px">
										<table class="table table-striped table-bordered table-hover">
											<tbody>
												<!-- BEGIN: group_exam -->
												<tr>
													<td><label><input style="margin-left: {GROUPEXAM.space}px;" type="checkbox" value="{GROUPEXAM.key}" name="group_exam_list[]" {GROUPEXAM.checked} class="news_checkbox"> {GROUPEXAM.name} </label></td>
													<td><input id="group_right_{GROUPEXAM.key}" style="{GROUPEXAM.display}" type="radio" name="group_exam_id"value="{GROUPEXAM.key}" {GROUPEXAM.group_checked} data-toggle="tooltip" data-placement="top" title="{LANG.type_exam_check_group}" /></td>
										
												</tr> 
												<!-- END: group_exam -->
											</tbody>
										</table>
									</div>
									<!-- BEGIN: error_group_exam --><div class="text-danger">{error_group_exam}</div><!-- END: error_group_exam -->
								</td>
							</tr>			 
							<tr class="norequired">
								<td style="width:180px"><div class="test-title">{LANG.type_exam_group_user}</div></td>
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
								<td style="width:180px"><div class="test-title">{LANG.type_exam_point}</div></td>
								<td>
									<input type="text" name="point" value="{DATA.point}" class="form-control numberonly" maxlength="8" > 
									
								</td>
							</tr>
							<tr class="norequired">
								<td style="width:180px"><div class="test-title">{LANG.type_exam_images}</div></td>
								<td>
									<div class="input-group">
										<input type="text" name="images" id="images" value="{DATA.images}" class="form-control" ondblclick="setFileInput ( 'images' )"> 
										<label class="input-group-btn">
											<span class="btn btn-info">
												{LANG.type_exam_select_images}<input id="selectimage" type="button" onclick="setFileInput ( 'images' )" style="display: none;">
											</span>
										</label>
									</div>
								</td>
							</tr>							
							<tr class="norequired">
								<td style="width:180px"><div class="test-title">{LANG.type_exam_introtext}</div></td>
								<td>
									<textarea name="introtext" class="form-control">{DATA.introtext}</textarea>
								</td>
							</tr>							
							<tr class="norequired">
								<td style="width:180px"><div class="test-title">{LANG.type_exam_keywords}</div></td>
								<td>
									<input type="text" name="keywords" value="{DATA.keywords}" class="form-control" data-toggle="tooltip" data-placement="top" title="{LANG.type_exam_help_keywords}"> 
								</td>
							</tr>							
							<tr class="norequired">
								<td style="width:180px"><div class="test-title">{LANG.type_exam_status}</div></td>
								<td>
									<select name="status" id="input-status" class="form-control">
										<!-- BEGIN: status -->
										<option value="{STATUS.key}" {STATUS.selected}>{STATUS.name}</option>
										<!-- END: status -->
									</select>  
								</td>
							</tr>
							<tr class="required">
								<td style="width:180px"><div class="test-title">{LANG.type_exam_time}</div></td>
								<td>
									<input type="text" name="time" value="{DATA.time}" class="form-control numberonly" maxlength="3" data-toggle="tooltip" data-placement="top" title="{LANG.type_exam_help_time}"> 
									<!-- BEGIN: error_time --><div class="text-danger">{error_time}</div><!-- END: error_time -->
								</td>
							</tr>
							<tr class="required">
								<td style="width:180px"><div class="test-title">{LANG.type_exam_num_question}</div></td>
								<td>
									<input type="text" name="num_question" id="num_question" value="{DATA.num_question}" class="form-control numberonly" maxlength="3"> 
									<!-- BEGIN: error_num_question --><div class="text-danger">{error_num_question}</div><!-- END: error_num_question -->
								</td>
							</tr>
								
							<tr class="norequired">
								<td style="width:180px"><div class="test-title">{LANG.type_exam_type_id}</div></td>
								<td>
									<select name="type_id" id="input-type" class="form-control">
										<!-- BEGIN: type -->
										<option value="{TYPE.key}" {TYPE.selected}>{TYPE.name}</option>
										<!-- END: type -->
									</select>  
								</td>
							</tr>  
							<tr id="showpdf2" class="norequired hidetype">
								<td style="width:180px"><div class="test-title">{LANG.type_exam_pdf}</div></td>
								<td>
									<div class="input-group">
										<input type="text" name="pdf" id="pdf" value="{DATA.pdf}" data-url="{DATA.pdf}" class="form-control" ondblclick="setFileInput ( 'pdf' )"> 
										<label class="input-group-btn">
											<span class="btn btn-info">
												{LANG.type_exam_pdf_select}<input id="selectpdf" type="button" style="display: none;" onclick="setFileInput ( 'pdf' )">
											</span>
										</label>
									</div>
								</td>
							</tr>
							<tr id="showanalyzed2" class="norequired hidetype">
								<td style="width:180px"><div class="test-title">{LANG.type_exam_analyzed}</div></td>
								<td>
									<div class="input-group">
										<input type="text" name="analyzed" id="analyzed" value="{DATA.analyzed}" data-url="{DATA.analyzed}" class="form-control" ondblclick="setFileInput ( 'analyzed' )"> 
										<label class="input-group-btn">
											<span class="btn btn-info">
												{LANG.type_exam_analyzed_select}<input id="selectanalyzed" type="button" style="display: none;" onclick="setFileInput ( 'analyzed' )">
											</span>
										</label>
									</div>
								</td>
							</tr>
							<tr id="showvideo2" class="norequired hidetype">
								<td style="width:180px"><div class="test-title">{LANG.type_exam_video}</div></td>
								<td>
									<div class="input-group">
										<input type="text" name="video" id="video" value="{DATA.video}" data-url="{DATA.video}" class="form-control"  ondblclick="setFileInput ( 'video' )"> 
										<label class="input-group-btn">
											<span class="btn btn-info">
												{LANG.type_exam_video_select}<input id="selectvideo" type="button" style="display: none;"  onclick="setFileInput ( 'video' )">
											</span>
										</label>
									</div>
								</td>
							</tr>
							<tr class="norequired">
								<td style="width:180px"><div class="test-title">{LANG.type_exam_allow_download}</div></td>
								<td>
									<input type="checkbox" name="allow_download" value="1" {ALLOW_DOWNLOAD_CHECKED} class="form-control"> 
									
									<span style="display:inline-block;padding-left:10px">
										{LANG.type_exam_allow_show_answer}
										<input type="checkbox" name="allow_show_answer" value="1" {ALLOW_SHOW_ANSWER_CHECKED} class="form-control"> 
									</span>
									<span id="showoption2" class="hidetype">
										
										<span style="display:inline-block;padding-left:10px">
										{LANG.type_exam_allow_video}
										<input type="checkbox" name="allow_video" value="1" {ALLOW_VIDEO_CHECKED} class="form-control"> 
										</span>
									</span>
								</td>
							</tr>
							
						</tbody>
					</table>
				</div>
				<div id="type0" class="hidetype"  style="display:none">
					<div class="config">{LANG.type_exam_config}</div>
					<div class="table-responsive" style="max-height:400px;overflow-y:scroll">
						<table class="table table-bordered">
							<thead>
								<tr>
									<td style="width:180px">
										<strong>{LANG.type_exam_config_category}</strong>
									</td>
									<td style="width:230px">
										<strong>{LANG.type_exam_config_level}</strong>
									</td>
									<td>
										<strong>{LANG.type_exam_config_catetory_percent}</strong>
										(<span id="percent">0</span>)
									</td>
								</tr>	
							</thead>
							<tbody>
								<!-- BEGIN: category -->
								<tr>
									<td style="vertical-align: middle;">
										<label>{CATEGORY.space}<input type="checkbox" class="getcategory" name="getConfig[{CATEGORY.key}][category_id]" value="{CATEGORY.key}" {CATEGORY.checked}>{CATEGORY.name}</label>
									</td>
									<td>
										<!-- BEGIN: level -->
										<div class="boxlevel1 disabled"><label><input class="getlevel" data-category="{CATEGORY.key}" type="checkbox" name="getConfig[{CATEGORY.key}][level_id][{LEVEL.key}]" value="{LEVEL.key}" {LEVEL.checked} disabled="disabled" >{LEVEL.name}({COUNT_QUESTION}) </label></div>				
										<!-- END: level -->
									</td>
									<td>
										<!-- BEGIN: percent -->
										<div class="boxlevel2 c{CATEGORY.key}"><input type="text" class="form-control number percent" name="getConfig[{CATEGORY.key}][percent][{LEVEL.key}]" value="{PERCENT}" disabled="disabled" id="level-{CATEGORY.key}-{LEVEL.key}"></div>			
										<!-- END: percent -->
									</td>
								</tr>
								<!-- END: category -->							
							</tbody>
						</table>
					</div>
				</div>
				
				<div id="type1" class="hidetype" style="display:none">
					
					
					<div class="col-sm-14 col-md-14 ">
						<div class="config">{LANG.type_exam_listq}</div>
						<div id="question-block" class="well well-sm scrollbar question-block" style="height: 300px; overflow: auto;">
							<ol class="simple_with_animation">
								<!-- BEGIN: question -->
								<li class="question-item" id="question-block{QUESTION.question_id}">
									<i class="fa fa-minus-circle"></i> {QUESTION.question}
									<input type="hidden" name="question_list[]" value="{QUESTION.question_id}"> <span class="level{QUESTION.level_id}"> {QUESTION.level}</span>
								</li>
								<!-- END: question -->
							</ol>
						</div>
					</div> 
					<div class="col-sm-10 col-md-10">
						<div class="config">{LANG.type_exam_select}</div>
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
									<div class="input-group-prepend">
										<button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tất cả cấp độ</button>
										<div id="changelevel" class="dropdown-menu">
											<a class="dropdown-item" data-id="0" href="javascript:void(0);">Tất cả cấp độ</a>
											<!-- BEGIN: level -->
											<a class="dropdown-item" data-id="{LEVEL.key}" href="javascript:void(0);">{LEVEL.name}</a>
											<!-- END: level -->
											<input type="hidden" name="level_id" value="0" id="level">
										</div>
										
									</div>
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
					<div class="clearfix"></div>
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover">
							<tbody>
								<tr class="norequired">
									<td style="width:180px"><div class="test-title">{LANG.type_exam_random}</div></td>
									<td>
										<input type="checkbox" name="random" value="1" id="random" {RANDOM_CHECKED} >
									</td>
								</tr>
								  
							</tbody>
						</table>
					</div>	
				</div>
				<div id="type2" class="hidetype" style="display:none">
					<div class="col-sm-16 col-md-16">
						 <iframe id="pdfview" frameborder="0" height="600" scrolling="yes" src="" width="100%" style="border: 1px #ccc solid;"></iframe>
					</div> 
					<div class="col-sm-8 col-md-8">
						<div class="boxsgroup2 scrollbar" >
							<h2 class="titles">{LANG.pdf_answer_list}</h2>
							<div style="margin-bottom: 4px"><input class="form-control" id="autoanswer" placeholder="{LANG.pdf_answer_select}"></div>
							<ul class="list-group">
								<!-- BEGIN: pdf -->
								<li class="list-group-item" id="item{QUESTION_ID}">
									<div class="qs"><strong>{LANG.pdf_question} {QUESTION_ID}: <i class="fa fa-minus-circle" data="{QUESTION_ID}" aria-hidden="true"></i> <i class="fa fa-plus-circle" data="{QUESTION_ID}" aria-hidden="true"></i> </strong></div>
									<div class="qs labelx">
										<!-- BEGIN: answers -->
										<label class="{ANS.class_checked}"><span class="test-title">{ANS.title}</span><input {ANS.checked} data-ans="{ANS.title}" type="checkbox" class="hide checkbox" name="trueanswer[{QUESTION_ID}][]" value="{ANS.key}" data-id="{QUESTION_ID}"><input type="hidden" value="{ANS.key}" name="answers_list[{QUESTION_ID}][]"></label>
										<!-- END: answers -->
									</div>
					 
								</li>
								<!-- END: pdf -->
							</ul>
				
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="config">{LANG.type_exam_rules}</div>
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
<form id="uploadImage" action="{ACTION_UPLOAD}" method="post" enctype="multipart/form-data">
	<input type="hidden" name="token" value="{TOKEN}">
	<input type="file" id="fileupload" name="fileupload">
</form>
<script type="text/javascript" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/jquery-sortable.js"></script>
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

function setFileInput ( area ) 
{	
	$('#fileupload')[0].click();
	$("#fileupload").on("change", function() {
		var a = $("#uploadImage");
		var b = new FormData(a[0]);
		$.ajax({
			type: "POST",
			url: $(a).prop("action"),
			data: b,
			contentType: false,
			processData: false,
			beforeSend: function() {
				 
			},
			complete: function() {
				$("#fileupload").unbind( "change" ); 
			},
			success: function(json) {
				
				if( json['result_file'] )
				{
					document.getElementById(area).value = json['result_file'];
					document.getElementById('fileupload').value = '';
					
					if( area == 'pdf' )
					{
						$('#pdfview').attr('src', nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=typeexam&action=pdfview&url=' + $('#pdf').val() );
						$('#pdf').attr('data-url', $('#pdf').val());
					}
					 
				}
				console.log( json );
			}
			,
			error: function(xhr, ajaxOptions, thrownError) {
				$("#fileupload").unbind( "change" ); 
			}
		})
	})
}

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
$('.percent').bind('input propertychange', function(){
	var percent = 0;
	$('.percent').each( function(key, item){

		if (! isNaN(parseFloat($(this).val()))) {
		
			var abc = parseFloat($(this).val());
		 
		}else{
			var abc = 0;
		}
		
		percent = percent + parseFloat( abc ); 
	})
	percent = percent.toFixed(2);
	$('#percent').text(percent);
});
 

$('#autoanswer').on('input propertychange', function(){
	
	var obj = $(this).val().split('');
	console.log(obj);
	//ABCDCBADBADBDACDABEABDBDCADDBA
	$('.boxsgroup2 input.checkbox').prop('checked', false).removeAttr('checked');
	$('.boxsgroup2 input.checkbox').parent().removeClass('checked');
	$.each(obj, function(question, item){
		++question;
		$('#item'+ question +' input[data-ans="'+ item +'"]').prop('checked', true);
		$('#item'+ question +' input[data-ans="'+ item +'"]').parent().addClass('checked');	
	})
	return false;
})
 

$('#num_question').on('input propertychange', function(){
 
	var num_question = parseInt($(this).val());
	$('.boxsgroup2>ul>li').empty();
	
	var tmp = '';
	for( i=1; i <= num_question; ++i )
	{
		tmp+='<li class="list-group-item" id="item'+ i +'">';
		tmp+='	<div class="qs"><strong>{LANG.pdf_question} '+ i +': <i class="fa fa-minus-circle" data="'+ i +'" aria-hidden="true"></i> <i class="fa fa-plus-circle" data="'+ i +'" aria-hidden="true"></i> </strong></div>';
		tmp+='	<div class="qs labelx">';
		tmp+='		<label><span class="test-title">A</span><input data-ans="A" type="checkbox" class="hide checkbox" name="trueanswer['+ i +'][]" value="1" data-id="'+ i +'" ><input type="hidden" value="1" name="answers_list['+ i +'][]"></label>';
		tmp+='		<label><span class="test-title">B</span><input data-ans="B" type="checkbox" class="hide checkbox" name="trueanswer['+ i +'][]" value="2" data-id="'+ i +'" ><input type="hidden" value="2" name="answers_list['+ i +'][]"></label>';
		tmp+='		<label><span class="test-title">C</span><input data-ans="C" type="checkbox" class="hide checkbox" name="trueanswer['+ i +'][]" value="3" data-id="'+ i +'" ><input type="hidden" value="3" name="answers_list['+ i +'][]"></label>';
		tmp+='		<label><span class="test-title">D</span><input data-ans="D" type="checkbox" class="hide checkbox" name="trueanswer['+ i +'][]" value="4" data-id="'+ i +'" ><input type="hidden" value="4" name="answers_list['+ i +'][]"></label>';		
		tmp+='	</div>';
		tmp+='	<div id="trueanswer'+ i +'" class="hide"></div>';
		tmp+='</li>';		
	}
	$('.boxsgroup2>ul').html( tmp );
	
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
	
	tmp='<label><span class="test-title">'+ abc[ans] +'</span><input type="checkbox" class="hide checkbox" name="trueanswer['+ id +'][]" value="1" ><input type="hidden" value="'+ans+'" data-ans="'+ abc[ans] +'" data-id="'+ id +'" name="answers_list['+ id +'][]"></label>';		
	$('#item'+id).find('.labelx').append(tmp);
 
})

$('#changelevel .dropdown-item').on('click', function(){

	$('#level').val( $(this).attr('data-id') );
	$('#changelevel .dropdown-item').removeClass('active');
	$(this).addClass('active');
	$(this).parent().prev().html($(this).text());
 
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

var type = $('#input-type').val();
$('.hidetype').hide();
$('#type' + type).show();
$('#showpdf' + type).show();
$('#showanalyzed' + type).show();
$('#showvideo' + type).show();
$('#showoption' + type).show();

if( type == 2 ) $('#pdfview').attr('src', $('#pdf').val());



$('#input-type').on('change', function(e){
	var type = $('#input-type').val();
	$('.hidetype').hide();
	$('#type' + type).show();
	$('#showpdf' + type).show();
	$('#showanalyzed' + type).show();
	$('#showvideo' + type).show();
	$('#showoption' + type).show();

	
	if( type == 2 )
	{
		$('.boxsgroup2>ul').empty();
		var num_question = parseInt($('#num_question').val());
		var tmp = '';
		for( i=1; i <= num_question; i++ )
		{
			tmp+='<li class="list-group-item" id="item'+ i +'">';
			tmp+='	<div class="qs"><strong>{LANG.pdf_question} '+ i +':<i class="fa fa-minus-circle" data="'+ i +'" aria-hidden="true"></i>  <i class="fa fa-plus-circle" data="'+ i +'" aria-hidden="true"></i> </strong></div>';
			tmp+='	<div class="qs labelx">';
			tmp+='		<label><span class="test-title">A</span><input data-ans="A" type="checkbox" class="hide checkbox" name="trueanswer['+ i +'][]" value="1" data-id="'+ i +'" ><input type="hidden" value="1" name="answers_list['+ i +'][]"></label>';
			tmp+='		<label><span class="test-title">B</span><input data-ans="B" type="checkbox" class="hide checkbox" name="trueanswer['+ i +'][]" value="1" data-id="'+ i +'" ><input type="hidden" value="2" name="answers_list['+ i +'][]"></label>';
			tmp+='		<label><span class="test-title">C</span><input data-ans="C" type="checkbox" class="hide checkbox" name="trueanswer['+ i +'][]" value="1" data-id="'+ i +'" ><input type="hidden" value="3" name="answers_list['+ i +'][]"></label>';
			tmp+='		<label><span class="test-title">D</span><input data-ans="D" type="checkbox" class="hide checkbox" name="trueanswer['+ i +'][]" value="1" data-id="'+ i +'" ><input type="hidden" value="4" name="answers_list['+ i +'][]"></label>';		
			tmp+='	</div>';
			tmp+='	<div id="trueanswer'+ i +'" class="hide"></div>';
			tmp+='</li>';	
		}
		$('.boxsgroup2>ul').html( tmp );
	}
	
});



 
$(document).on('submit', '#form-type_exam', function() {
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
		url: nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=typeexam&action=getQuestion&nocache=' + new Date().getTime(),
		data: $('#searchbox input[type=\'text\'],#searchbox input[type=\'hidden\'], #searchbox input[type=\'checkbox\']:checked'),
		dataType: 'json',
		success: function(json) {
			
			if( json['data'] )
			{
				var temp='';
				$.each( json['data'] , function(i, item){
					temp+='<li class="question-item" id="question-block' + item['question_id'] + '"><i class="fa fa-minus-circle"></i> ' + item['question'] + '<input type="hidden" name="question_list[]" value="' + item['question_id'] + '" /> <span class="level' + item['level_id'] + '"> ' + item['level'] + '</span></li>';
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
		url: nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=typeexam&action=getQuestion&page='+ page + '&nocache=' + new Date().getTime(),
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
					temp+='<li class="question-item" id="question-block' + item['question_id'] + '"><i class="fa fa-minus-circle"></i> ' + item['question'] + '<input type="hidden" name="question_list[]" value="' + item['question_id'] + '" /> <span class="level' + item['level_id'] + '"> ' + item['level'] + '</span></li>';
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
$('.getlevel').on('click', function(e){
	var category_id = $(this).attr('data-category');
	if( $(this).prop('checked') )
	{
		$('#level-' + category_id +'-'+$(this).val()).prop('disabled', false);
	}else{
		$('#level-' + category_id +'-' + $(this).val()).prop('disabled', true);
		$('#level-' + category_id +'-' + $(this).val()).val('');
	}
}) 
$('.text-danger').each( function(key, item){
	$(this).prev().addClass('warning');
})



$("#pdf").on('input propertychange', function() {
	if( $('#pdf').val() != '' )
	{
		var urlpdf = $('#pdf').val();
		if(urlpdf.indexOf("drive.google.com") > -1 && urlpdf.substring(urlpdf.lastIndexOf('/') + 1) != 'preview')
		{
			urlpdf = urlpdf.replace('/'+urlpdf.substring(urlpdf.lastIndexOf('/') + 1), '/preview');
			$('#pdf').val(urlpdf);
			$('#pdfview').attr('src', urlpdf );
			
		}
		else
		{
			$('#pdfview').attr('src', nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=typeexam&action=pdfview&url=' + urlpdf );
	
		}
		$('#pdf').attr('data-url', urlpdf);
	}
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
});

$('#grouplist').autofill({
	'source': function(request, response) {	 
		$.ajax({
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '='+ nv_module_name  +'&' + nv_fc_variable + '=typeexam&action=group_user&title='+ encodeURIComponent(request) +'&nocache=' + new Date().getTime(),		
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