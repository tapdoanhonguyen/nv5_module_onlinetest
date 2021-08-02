<!-- BEGIN: main -->
<!-- BEGIN: type -->
<div id="scrolldiv">
	<div class="sticky">
		<div class="process-exam hidden-xs">
			<p class="mg-0">
				<b>Thời gian còn lại</b>
				<span style="width: 1px;display: inline-block;height: 16px;margin: 0 13px -1px 17px;background: #fff;"></span>
				<b style="color: #fff;font-size: 22px;"> <span class="onlinetest-clock"><span class="timer" data-seconds-left="1"></span></span></b>
			</p>
			<p>
				<b>Số câu đã làm</b>
				<span style="width: 1px;display: inline-block;height: 16px;margin: 0 15px 0 38px;background: #fff;"></span>
				<b style="color: #fff; font-size: 22px;"><span class="number_answer_pc">0</span>/<span class="num_of_question">{DATA.num_question}</span></b>
			</p>
		</div>
		<div class="list-question-number scrollbar toggle_list_q">
			<p class="mg-0 color-blue text-center title-list-q"><b>CÂU HỎI</b></p>
			<ul>
				<!-- BEGIN: loop -->
				<li class="q_{NUM}"><a class="" onclick="showQuestion('question{NUM}')" href="#question{NUM}">{NUM}</a></li>
				<!-- END: loop -->
			</ul>
			<p class="text-center">
				<a class="btn-onclick submit-exam" >
					<b>Nộp bài</b>
				</a>
			</p>
			 
		</div>
		<div id="countxyx" style="font-size: 12px;font-weight:bold"></div>

	</div>
</div>
<div class="clearfix"> </div>
 
<script type="text/javascript">

$('.submit-exam').on('click', function(){
	$('#submitform')[0].click();
})

var height =  $('.list-question-number ul').height();
$('.list-question-number ul').css({
	'height' : height
});

$(".list-question-number .title-list-q").click(function () {
	$('.list-question-number').toggleClass('toggle_list_q');
	if($('.list-question-number').hasClass('toggle_list_q')){
		$('.list-question-number ul').css({
			'height' : height
		});
	}else {
		$('.list-question-number ul').css({
			'height' : 0
		});
	}
});
</script>
<!-- END: type -->
<!-- BEGIN: type2 -->
<div id="scrolldiv">
	<div class="sticky">
		<div class="process-exam hidden-xs">
			<p class="mg-0">
				<b>Thời gian còn lại</b>
				<span style="width: 1px;display: inline-block;height: 16px;margin: 0 13px -1px 17px;background: #fff;"></span>
				<b style="color: #fff;font-size: 22px;"> <span class="onlinetest-clock"><span class="timer" data-seconds-left="1"></span></span></b>
			</p>
			<p>
				<b>Số câu đã làm</b>
				<span style="width: 1px;display: inline-block;height: 16px;margin: 0 15px 0 38px;background: #fff;"></span>
				<b style="color: #fff; font-size: 22px;"><span class="number_answer_pc">0</span>/<span class="num_of_question">{DATA.num_question}</span></b>
			</p>
		</div>
		<div id="changetoanswer">
			<div class="list-question-number scrollbar toggle_list_q">
				<p class="mg-0 color-blue text-center title-list-q"><b>CÂU HỎI</b></p>
				<ul>
					<!-- BEGIN: loop_question -->
					<li class="q_{NUM}"><a href="javascript:void(0);">{NUM}</a></li>
					<!-- END: loop_question -->
				</ul>
				 
			</div>
		</div>
		<div id="countxyx" style="font-size: 12px;font-weight:bold"></div>

	</div>
</div>
<div class="clearfix"> </div>
 
<script type="text/javascript">

$('.submit-exam').on('click', function(){
	$('#submitform')[0].click();
})

var height =  $('.list-question-number ul').height();
$('.list-question-number ul').css({
	'height' : height
});

$(".list-question-number .title-list-q").click(function () {
	$('.list-question-number').toggleClass('toggle_list_q');
	if($('.list-question-number').hasClass('toggle_list_q')){
		$('.list-question-number ul').css({
			'height' : height
		});
	}else {
		$('.list-question-number ul').css({
			'height' : 0
		});
	}
});
 
</script>
<!-- END: type2 -->
<!-- END: main -->
