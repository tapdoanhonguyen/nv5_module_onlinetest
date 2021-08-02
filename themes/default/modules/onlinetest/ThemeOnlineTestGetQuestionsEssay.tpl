<!-- BEGIN: main -->
<form id="formQuestions" role="form" method="post" enctype="application/x-www-form-urlencoded">
	
	<!-- BEGIN: loop -->
	<div class="test" id="question{LOOP.essay_id}" data-id="{LOOP.essay_id}">
		
		<input type="hidden" value="{LOOP.essay_id}" name="answers[{LOOP.essay_id}][essay_id]">
		<input type="hidden" value="{LOOP.token}" name="answers[{LOOP.essay_id}][token]">
		<p class="question-info">
			<b>{LANG.question_stt} {LOOP.stt}</b>
			<a class="fr btn-feedback btn-onclick report" data-essay_id="{LOOP.essay_id}" data-token="{LOOP.token}" data-login>
				<img class="not-hover" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/onlinetest/icon-feedback.png">
				<img class="hover" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/onlinetest/icon-feedback_hover.png">
				<span>Báo lỗi</span>
			</a>
		</p>
		<div class="question" >{LOOP.question}</div>	
		<div class="answer" >{LOOP.answer}</div>	
		 
		<div class="clearfix"></div>
	</div>
	<!-- END: loop -->
	 
	<div align="center" style="padding:10px" id="boxsubmit">
		<input type="hidden" name="essay_exam_id" value="{DATA.essay_exam_id}"/>
		<input type="hidden" name="history_essay_id" value="{HISTORY_ID}"/>
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
	CKupdate();
 	$.ajax({
		url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=sendtest&action=essay&second=' + new Date().getTime(),
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
	CKupdate();

	$.ajax({
		url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=sendtest&action=essay&second=' + new Date().getTime(),
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
		
		CKupdate();
		
		formdata = $( this ).serializeArray();
		$.ajax({
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=sendtest&action=essay&second=' + new Date().getTime(),
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
					 
					$('#formQuestions input').prop('disabled', true);
					
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
