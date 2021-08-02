<!-- BEGIN: main -->
<div id="OnlineTestDoTest" class="OnlineTestDoTest">
	<div id="boxfirst" class="toggle_list_q">
		<h1>{LANG.test_success_essay} - {DATA.title}</h1>
		<div class="body-result-1">
			<!-- BEGIN: loop -->
			<div class="boxsecond">
				<p class="question-info">
					<b>{LANG.question_stt} {LOOP.stt}</b>
					<a class="fr btn-feedback btn-onclick report2" data-essay_id="{LOOP.essay_id}" data-token="{LOOP.token}" data-login>
						<img class="not-hover" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/onlinetest/icon-feedback.png">
						<img class="hover" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/onlinetest/icon-feedback_hover.png">
						<span>Báo lỗi</span>
					</a>
				</p>
				
				<div class="question" id="question{LOOP.essay_id}">{LOOP.question}</div>	
				<hr class="line">
				<div class="answer" >{LOOP.answer}</div>
				<div class="clearfix"></div>
			</div>			
			<!-- END: loop -->  
		</div>
		
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