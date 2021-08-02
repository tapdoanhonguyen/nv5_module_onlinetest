<!-- BEGIN: main -->
<div class="BoxGroupExam">
	<!-- BEGIN: group_exam -->
	<div class="GroupExam">
		
		<ul class="list-inline">
			<li><h2>{LANG.search_result} {NUM_ITEMS} {LANG.result} '{KEY}'</h2></li>
			
		</ul>
		<div class="row">
			<!-- BEGIN: loop -->
			 
			<div class="col-md-6 col-sm-12 fixed">
				<div class="grb {CHECKOPEN}">
					<!-- BEGIN: open -->
					<a href="{LOOP.link}" class="btn button-link">{LANG.test}</a>
					<!-- END: open -->
					<!-- BEGIN: close -->
					<a href="{LOOP.link}" class="btn button-link">{LANG.no_test}</a>
					<!-- END: close -->
					<div class="gri"><a href="{LOOP.link}" title="{LOOP.title}"><img alt="{LOOP.title}" src="{LOOP.imghome}"></a></div>
					<div class="grt">
						<a href="{LOOP.link}" title="{LOOP.title}">{LOOP.title_cut}</a>
						<div class="infoex">
							<i class="fa fa-info-circle" aria-hidden="true"></i> <strong>{LOOP.num_question}</strong> {LANG.num_question_title}. {LANG.time}: <strong>{LOOP.time} {LANG.minutes}</strong> 
							<!-- <p class="price">{LANG.price}: {LOOP.point}</p> -->
						</div>
					</div>
				</div>
			</div>
			<!-- END: loop -->
		</div>
	</div>
	<!-- END: group_exam -->
	<!-- BEGIN: generatePage -->
	<div class="generatePage">{GENERATE_PAGE}</div>
	<!-- END: generatePage -->
	<div id="fb-root"></div>
	<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v6.0&appId=408248922518950&autoLogAppEvents=1"></script>
	<div style="border:1px #ccc solid;padding:10px;">
		<div class="fb-comments" data-href="{SELFURL}" data-width="" data-numposts="10"></div>
	</div>
</div>
<script type="text/javascript">
$('.button-link').centerDiv();
</script>
<!-- END: main -->
