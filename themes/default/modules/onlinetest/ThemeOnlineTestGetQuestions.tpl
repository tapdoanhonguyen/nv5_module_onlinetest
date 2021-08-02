<!-- BEGIN: main -->
<!-- BEGIN: type -->
<form id="formQuestions" role="form" method="post" enctype="application/x-www-form-urlencoded">
	
	<!-- BEGIN: loop -->
	<div class="test" id="question{LOOP.question_id}" data-id="{LOOP.question_id}">
		
		<input type="hidden" value="{LOOP.question_id}" name="answers[{LOOP.question_id}][question_id]">
		<input type="hidden" value="{LOOP.token}" name="answers[{LOOP.question_id}][token]">
		<p class="question-info">
			<b>{LANG.question_stt} {LOOP.stt}</b>
			<span class="level-question level-{LOOP.level_id}">{LOOP.level}</span>
			<a class="fr btn-feedback btn-onclick report" data-question_id="{LOOP.question_id}" data-token="{LOOP.token}" data-login>
				<img class="not-hover" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/onlinetest/icon-feedback.png">
				<img class="hover" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/onlinetest/icon-feedback_hover.png">
				<span>Báo lỗi</span>
			</a>
		</p>
		<div class="question" >{LOOP.question}</div>	
		<!-- BEGIN: answers -->
		<label class="{ANS.class_checked}"><span class="title">{ANS.title}</span><input class="hide checkbox" {ANS.checked} type="checkbox" value="{ANS.key}" name="answers[{LOOP.question_id}][answers][{ANS.key}]" id="answer-{LOOP.question_id}-{ANS.key}"> {ANS.name} </label>	            
		<!-- END: answers -->
		<!-- BEGIN: show_answer -->
		<div class="report">
			<a class="analyzes" href="javascript:void(0);" data-question_id="{LOOP.question_id}" data-token="{LOOP.token}" data-login="{LOGIN}" > {LANG.view_answers}</a> - 
			<a class="report" href="javascript:void(0);" data-question_id="{LOOP.question_id}" data-token="{LOOP.token}" data-login="{LOGIN}"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> {LANG.report}</a> - 
			<a class="comment" href="javascript:void(0);" data-question_id="{LOOP.question_id}" data-token="{LOOP.token}" ><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> {LANG.comment} (<span id="getcomment-{LOOP.question_id}">{LOOP.comment}</span>)</a>
		</div>
		<div id="analyzesList-{LOOP.question_id}" class="commentbox hide"> </div>
		<div class="clearfix"></div>
		<div id="commentList-{LOOP.question_id}" class="commentbox hide"> </div>
		<!-- END: show_answer -->
		
		<!-- <p style="text-align: right">
			<i>Câu hỏi: <b>{LOOP.question_id}</b></i>
			<span style="width: 1px;height: 12px;background: #ccc;display: inline-block;margin: 0 5px 0 10px;"></span>
			<a data-status-save="q_saved" onclick="saveQuestion({LOOP.question_id})" id="q_save_{LOOP.question_id}" class="btn-save btn-onclick">
				<i class="fa fa-bookmark" aria-hidden="true"></i>
                <span>lưu</span>
			</a>
		</p> -->
		<div class="clearfix"></div>
	</div>
	<!-- END: loop -->
	 
	<div align="center" style="padding:10px" id="boxsubmit">
		<input type="hidden" name="type_exam_id" value="{DATA.type_exam_id}"/>
		<input type="hidden" name="history_id" value="{HISTORY_ID}"/>
		<input type="hidden" id="is_sended" name="is_sended" value="0"/>
		<input type="hidden" id="timeout" name="timeout" value="0"/>
		<input type="hidden" name="token" value="{TOKEN}"/>
		<button class="btn btn-primary" id="submitform" type="submit"><i class="fa fa-spinner fa-spin" style="display:none"></i> {LANG.send_test} </button>
		
	</div>
</form>
<div class="sticky-stopper"></div> 
<script>

function countAnswer()
{

	var countAnswer = 1;
	$('#formQuestions .test').each(function(){
		id = $(this).attr('data-id');
		if( $(this).find('label.checked').length > 0 )
		{
			
			$('.q_'+id).addClass('selected');
			$('.number_answer_pc').html(countAnswer);
			++countAnswer;
		}
		else
		{
			$('.q_'+id).removeClass('selected');
		}
	})
}

$('#formQuestions input.checkbox').on('click', function(){
	
	
	if( $(this).prop('checked') )
	{
		$(this).parent().addClass('checked');
	}else{
		$(this).parent().removeClass('checked');
	}
	
	setTimeout(function(){countAnswer()}, 200)
	
})
</script>
<!-- END: type -->

<!-- BEGIN: type2 -->
<iframe id="pdfview" frameborder="0" height="1000" scrolling="yes" src="{PDFVIEW}" width="100%" style="border: 1px #ccc solid;"></iframe>					
<div class="sticky-stopper"></div> 
 
<script type="text/javascript">
$('#pdfview').css({height: $( window ).height() + 'px'});
$('html,body').animate({
	scrollTop: ($('#pdfviewoffset').offset().top - 104)
}, 'slow');

function countAnswer()
{

	var countAnswer = 0;
	$('#formQuestions .labelx').each(function(){
		if( $(this).find('label.checked').length > 0 )
		{
			
			
			++countAnswer;
		}
		
	})
	$('.number_answer_pc').html(countAnswer);
}

$(document).on('click', '.boxsgroup2 input.checkbox', function(){

	if( $(this).prop('checked') )
	{
		$(this).parent().addClass('checked');
	}else{
		$(this).parent().removeClass('checked');
	}
	
	setTimeout(function(){countAnswer()}, 200)
	
	
})

<!-- $('body').addClass('overlay'); -->
 
</script>
 
<!-- END: type2 -->
<script type="text/javascript">




(function () {
  var script = document.createElement("script");
  script.type = "text/javascript";
  script.src = "{NV_BASE_SITEURL}MathJax-2.7.8/MathJax.js?config=TeX-AMS_HTM";   // use the location of your MathJax

  var config = 'MathJax.Hub.Config({' +
                 'extensions: ["tex2jax.js"],' +
                 'jax: ["input/TeX","output/HTML-CSS"]' +
               '});' +
               'MathJax.Hub.Startup.onload();';

  if (window.opera) {script.innerHTML = config}  else {script.text = config}

  document.getElementsByTagName("head")[0].appendChild(script);
  setTimeout(function(){$('.math-tex').show()}, 400)
})();
 
var startcounting = '';
function updateTest()
{
 	$.ajax({
		url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=sendtest&second=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data: $('#formQuestions').serializeArray(),
		beforeSend: function() {

		},	
		complete: function() {
			clearInterval(window.startcounting); 
			startcounting = setInterval(function(){updateTest() }, 5000);
		},
		success: function(json) {
			clearInterval(window.myTimerPage);
		 
			<!-- console.log( json ); -->

		},
		error: function(xhr, ajaxOptions, thrownError) {
			clearInterval(window.startcounting); 
			setInterval(function(){updateTest() }, 5000);
			<!-- alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); -->
		}
	});
}

startcounting = setInterval(function(){updateTest() }, 5000);


$('#formQuestions input').on('click', function( ){
	$.ajax({
		url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=sendtest&second=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data: $('#formQuestions').serializeArray()
	});
})
 
 
$('#formQuestions').on('submit', function( e ) {
	var check = false;
	if( $('#is_sended').val() == 0 )
	{
		check = confirm( '{LANG.send_sure}' );
	}else{
		
		check = true;
	}

	if ( check )
	{
		clearTimeout(startcounting);
		$('#is_sended').val(1);
		formdata = $( this ).serializeArray();
		$.ajax({
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=sendtest&second=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: formdata,
			beforeSend: function() {
				$('#submitform i' ).show();
				$('#submitform' ).prop('disabled', true);
				
			},	
			complete: function() {
				$('#boxsubmit' ).remove();
			},
			success: function(json) {
				clearInterval(window.myTimer);
				if( json['answers'] )
				{
					$.each( json['answers'], function( i, item ) {
						$.each(item['trueanswer'], function(t, trueanswer){
							$('#answer-'+ i +'-' + trueanswer).parent().addClass('trueanswer').attr('data-true', '1');
						})
					});
					$.each( json['analyzes'], function( question_id, item ) {
						$('#analyzesList-' + question_id).html( item );
					});
					
					$.each( $('#OnlineTestDoTest').find('label.checked'), function( i, x ) {
						if( ! $(this).attr('data-true') )
						{
							$(this).addClass('wrong');		
						}
					});
 
 
					$('#formQuestions input').prop('disabled', true);
					
					$('#number-success').html( json['number_success'] );
					$('#number-error').html( json['number_error'] );
					$('#number-total').html( json['number_total'] );
 					$('div.report').show();
					$('.onlinetest-clock').html('<span class="timer"><span class="minutes">00:</span><span class="seconds">00</span><div class="clearDiv"></div></span>');
					if( json['shareLink'] )
					{
						window.location.href = json['shareLink']; 
		 
					}
			 
					
 				}
 
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
	e.preventDefault() ;
})
</script> 

<!-- END: main -->
