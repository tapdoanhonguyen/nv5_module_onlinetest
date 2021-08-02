<!-- BEGIN: main -->
<div id="changetoanswer">
	<h2 class="titles">{LANG.list_answer}</h2>
	<div class="boxsgroup3 scrollbar" >	
		
		<ul class="list-group">
			<!-- BEGIN: loop -->
			<li class="list-group-item" id="item{QUESTION_ID}">
				<div class="qs"><strong>{LANG.pdf_question} {QUESTION_ID}:</strong></div>
				<div class="qs labelx">
					<!-- BEGIN: answers -->
					<label class="{ANSWERS.checked_class} {ANSWERS.trueanswer} {NOANSWER}"><span class="title">{ANSWERS.title}</span><input {ANSWERS.checked} type="checkbox" class="hide checkbox" name="answers[{QUESTION_ID}][answers][{ANSWERS.key}]" value="{ANSWERS.key}" data-id="{QUESTION_ID}"></label>
					<!-- END: answers -->
				</div>
				<!-- BEGIN: show_wrong -->
				<p>
					<i class="fa fa-times color-red" style="margin-right: 10px;margin-left: 5px;"></i> 
					<b class="color-red">{LISTTRUE.error}</b> 
					<!-- BEGIN: allow_answer -->
					<b style="color: #8ca752 !important;"> - {LANG.trueanswers} {LISTTRUE.ans}</b>
					<!-- END: allow_answer -->
				</p>
				<!-- END: show_wrong -->

			</li>
			<!-- END: loop -->
		</ul>
	</div>
</div>
<!-- END: main -->
