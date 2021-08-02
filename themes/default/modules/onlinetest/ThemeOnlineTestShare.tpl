<!-- BEGIN: main -->
<div id="OnlineTestDoTest" class="OnlineTestDoTest">
	<span id="showhidebox"><i class="fa fa-chevron-down"></i></span>
	<div id="boxfirst" class="toggle_list_q">
		<h1>{LANG.test_success} - {DATA.title}</h1>
		<div class="body-result-1">
			<div class="total_point">
				<span class="color-orange size-18"><b>{LANG.diemso}</b></span>
				<b class="color-orange size-20">{DATA.score}/{DATA.max_score}</b>
			</div>
			<div class="result-question">
				<h3>{LANG.ketquathi}: {DATA.title}</h3>
				<div class="box-50">
					<p>
						{LANG.socaudalam}: <b style="margin-left: 40px;">{DATA.number_work} / {DATA.num_question} {LANG.num_question_title} </b> <br>
						{LANG.share_time_do_test}: <b style="margin-left: 22px;">{DATA.time_do_test} / {DATA.time} {LANG.share_minutes}</b>
					</p>
				</div>
				<p style="margin-top: 15px;margin-bottom: 0" class="sum-result">
					<span class="aw_correct"></span> {LANG.true}: <b>{DATA.number_success}</b>
					<span class="aw_not_correct"></span> {LANG.failed}: <b>{DATA.number_error}</b>
					<span class=""></span> {LANG.number_notans}: <b>{DATA.number_notans}</b>
				</p>
				
				<!-- BEGIN: config -->
				<p style="margin-top: 15px;margin-bottom: 0">
					<!-- BEGIN: allow_download -->
					<a class="download" href="javascript:void(0);" onclick="download_exam('{DATA.history_id}', '{DATA.token}')"><i class="fa fa-download" aria-hidden="true"></i> <strong>{LANG.download}</strong></a>					 
					<!-- END: allow_download -->
				
					<!-- BEGIN: allow_show_answer -->
					<a class="download" href="{ANALYZED}" target="_blank"> <i class="fa fa-download" aria-hidden="true"></i><strong> {LANG.download_analyzed}</strong> </a>					 
					<!-- END: allow_show_answer -->
					
					<!-- BEGIN: allow_video -->
					<a class="download" href="javascript:void(0);" onclick="video_analyzed('{VIDEO_TYPE}','{VIDEO}', '{IMAGES}', '{DATA.title}')"><i class="fa fa-video-camera" aria-hidden="true"></i> <strong>{LANG.video}</strong></a>					 
					<div class="modal fade" id="ModalAddList" role="dialog">
						<div class="modal-dialog modal-lg">
						  <div class="modal-content">
							<div class="modal-header">
							  <button type="button" class="close" data-dismiss="modal">&times;</button>
							  <h4 class="modal-title"></h4>
							</div>
							<div class="modal-body">
							 
							</div>
					 
						  </div>  
						</div>
					</div>
					<link href="{NV_BASE_SITEURL}themes/{TEMPLATE}/css/onlinetest-video.css" rel="stylesheet">
					<link href="{NV_BASE_SITEURL}themes/{TEMPLATE}/css/onlinetest-video-config.css" rel="stylesheet">
					<script src="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/onlinetest-video.js"></script>
					<script src="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/onlinetest-video-youtube.js"></script>
					<!-- END: allow_video --> 
				</p> 
				<!-- END: config -->
			</div>
		</div>
		<!-- BEGIN: type -->
		<div class="body-result-2 list-question-number2 toggle_list_q">
			<ul>
				<!-- BEGIN: loop_num_question --> 
				<li class="q_{QUESTION_ID}"><a class="{CLASS}" onclick="showQuestion('question{QUESTION_ID}')" href="#question{QUESTION_ID}">{NUM}</a></li>
				<!-- END: loop_num_question -->
			</ul>
		</div>
		<!-- END: type -->
	</div>
	<div id="showQuestions" class="boxtest">
		
		<!-- BEGIN: pdf -->
		<div id="pdfviewoffset"></div> 
		<iframe id="pdfview" frameborder="0" height="1000" scrolling="yes" src="{PDFVIEW}" width="100%" style="border: 1px #ccc solid;"></iframe>					
		
		 
		<script type="text/javascript">
		$('#pdfview').css({height: $( window ).height() + 'px'});
		$('#changetoanswer>.boxsgroup2').css({height: $( window ).height() + 'px'});
		<!-- $("#showhidebox").click(function(){ -->
		  <!-- $("#boxfirst").toggle(); -->
		<!-- }); -->
		
		var height =  $('#boxfirst').height();
		$('#height').css({
			'height' : height
		});

		$("#showhidebox").on('click', function () {
			$('#boxfirst').toggleClass('toggle_list_q');
			if($('#boxfirst').hasClass('toggle_list_q')){
				$('#boxfirst').css({
					'height' : height
				});
				$(this).find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
			}else {
				$('#boxfirst').css({
					'height' : 0
				});
				$(this).find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
			}
		});
		
		//$('html,body').animate({
		//	scrollTop: ($('#pdfviewoffset').offset().top)
		//}, 'slow');
		</script>
		<!-- END: pdf -->
		<!-- BEGIN: loop -->
		<div class="test" id="question{LOOP.question_id}">
			<p class="question-info">
				<b>{LANG.question_stt} {LOOP.stt}</b>
				<span class="level-question level-{LOOP.level_id}">{LOOP.level}</span>
				<a class="fr btn-feedback btn-onclick report" data-question_id="{LOOP.question_id}" data-token="{LOOP.token}" data-login>
					<img class="not-hover" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/onlinetest/icon-feedback.png">
					<img class="hover" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/onlinetest/icon-feedback_hover.png">
					<span>{LANG.report}</span>
				</a>
			</p>
			<div class="question" id="question{LOOP.question_id}">{LOOP.question}</div>	
			<!-- BEGIN: answers -->
				<label class="{ANSWERS.checked_class} {ANSWERS.trueanswer}"><span class="title">{ANSWERS.title}</span><input class="hide checkbox" type="checkbox" value="{ANSWERS.key}" id="answer-{LOOP.question_id}-{ANSWERS.key}" disabled="disabled" {ANSWERS.checked}> {ANSWERS.name} </label>	            
			<!-- END: answers -->
			
			<!-- BEGIN: show_wrong -->
			<p>
				<i class="fa fa-times color-red" style="margin-right: 10px;margin-left: 5px;"></i> 
				<b class="color-red">{LISTTRUE.error}</b> 
				<!-- BEGIN: allow_answer -->
				<b style="color: #8ca752 !important;"> - {LANG.trueanswers} {LISTTRUE.ans}</b>
				<!-- END: allow_answer -->
			</p>
			<!-- END: show_wrong -->
			
			<div class="reporth">
				<a class="analyzes" href="javascript:void(0);" data-question_id="{LOOP.question_id}" data-token="{LOOP.token}" data-login="{LOGIN}" > {LANG.view_answers}</a> - 		
				<a class="comment" href="javascript:void(0);" data-question_id="{LOOP.question_id}" data-token="{LOOP.token}" ><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> {LANG.comment} (<span id="getcomment-{LOOP.question_id}">{LOOP.comment}</span>)</a>
			</div>
			<!-- BEGIN: view_analyzes2 -->
			<div id="analyzesList-{LOOP.question_id}" class="commentbox hide">{ANALYZES}</div>
			<!-- END: view_analyzes2 -->
			<div id="commentList-{LOOP.question_id}" class="commentbox hide"> </div>
		</div>		 
		<!-- END: loop -->
	</div>
	
</div>
<div class="clearfix"></div>
<script type="text/x-mathjax-config">

MathJax.Hub.Config({
	extensions: ["tex2jax.js"],
	jax: ["input/TeX","output/HTML-CSS"],
	inlineMath: [['$','$'], ['\\\\(','\\\\)']],
	'HTML-CSS': {
		matchFontHeight: false,
		availableFonts: ["STIX"],
		webFont: 'STIX-Web',
		preferredFont: 'STIX-Web',
		styles: {
		".MathJax_Preview": {visibility: "hidden"},
	  }
	}
});

$(document).ready(function(){
	setTimeout(function(){$('.math-tex').show()}, 400)
})
</script>
<script type="text/javascript" async  src="{NV_BASE_SITEURL}MathJax-2.7.8/MathJax.js?config=TeX-AMS_HTML-full"></script> 
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_EDITORSDIR}/ckeditor/ckeditor.js"></script>
<!-- END: main -->