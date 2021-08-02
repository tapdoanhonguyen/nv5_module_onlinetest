<!-- BEGIN: main -->
<h2 class="titles">{LANG.list_answer}</h2>
<div class="boxsgroup2 scrollbar" >	
	<form id="formQuestions" role="form" method="post" enctype="application/x-www-form-urlencoded">
		<ul class="list-group">
			<!-- BEGIN: loop_question -->
			<li class="list-group-item" id="item{QUESTION_ID}">
				<div class="qs"><strong>{LANG.pdf_question} {QUESTION_ID}:</strong></div>
				<div class="qs labelx">
					<!-- BEGIN: answers -->
					<label class="{ANS.class_checked}"><span class="title">{ANS.title}</span><input {ANS.checked} data-ans="{ANS.title}" type="checkbox" class="hide checkbox" name="answers[{QUESTION_ID}][answers][{ANS.key}]" value="{ANS.key}" data-id="{QUESTION_ID}"></label>
					<!-- END: answers -->
				</div>
				<input type="hidden" value="{QUESTION_ID}" name="answers[{QUESTION_ID}][question_id]">
				<input type="hidden" value="{QUESTION_TOKEN}" name="answers[{QUESTION_ID}][token]">
			</li>
			<!-- END: loop_question -->
		</ul>
		<div align="center" style="padding:10px" id="boxsubmit">
			<input type="hidden" name="type_exam_id" value="{DATA.type_exam_id}"/>
			<input type="hidden" name="history_id" value="{HISTORY_ID}"/>
			<input type="hidden" id="is_sended" name="is_sended" value="0"/>
			<input type="hidden" id="timeout" name="timeout" value="0"/>
			<input type="hidden" name="token" value="{TOKEN}"/>
			<button class="btn btn-primary" id="submitform" type="submit"><i class="fa fa-spinner fa-spin" style="display:none"></i> {LANG.send_test} </button>	
		</div>
	</form>
</div>

<!-- END: main -->
