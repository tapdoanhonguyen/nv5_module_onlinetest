<!-- BEGIN: main -->
<div id="OnlineTestDoTest" class="OnlineTestDoTest">
	<div  class="OnlineTestDoTestbox">
	
		<div class="summury-info text-center" id="thoigian">
			{LANG.time_test}: <span class="color-orange">{DATA.time} {LANG.minutes}</span> - {LANG.num_question}: <span class="color-orange">{DATA.num_question} {LANG.num_question_title}</span>
		</div>
		<div class="second-info">
			<div class="inrtro-exam hidden-xs">
				{CONFIG.intro}
			</div>
			<div class="box0">
				{DATA.rules}
				
				
			</div>
		</div>
		
		<div id="openTest">
			
			<!-- BEGIN: open -->
			<div class="sureOpen">
				<div class="box1">{LANG.sureopen}</div>
				<div class="box2">
					<button id="sureOpen" type="button" class="btn btn-primary"><i class="fa fa-spinner fa-lg fa-spin" style="display:none"></i> MỞ BÀI THI</button>
					<input type="hidden" id="sureOpenTypeExamId" name="type_exam_id" value="{DATA.type_exam_id}"/>
					<input type="hidden" id="sureOpenToken" name="token" value="{DATA.token}"/>
				</div>		
			</div>
			<!-- END: open -->
			<!-- BEGIN: close -->
			<div class="sureOpen">
				<div class="box1">{LANG.not_enough_vicoin}</div>
				<div class="box2">
					<a href="{RECHARGE}"  class="btn btn-primary">{LANG.recharge_vicoin}</a>
				</div>		
			</div>
			<!-- END: close -->
		</div>
		
		<div id="showQuestion" class="boxtest">
			
		</div>
		<div id="pdfviewoffset"></div>

	</div>
	

</div>


<!-- BEGIN: trigger_open -->
<script type="text/javascript" >
$(document).ready(function(){
	$('#sureOpen').trigger('click');
	
})

<!-- window.addEventListener("beforeunload", function (e) { -->
  <!-- var confirmationMessage = "\o/"; -->

  <!-- (e || window.event).returnValue = confirmationMessage;   -->
  <!-- return confirmationMessage;                             -->
<!-- }); -->


$(window).on('focus', function () {
	console.log('focus');
	//alert('VUA DI CHOI VE DUNG KHONG');
});
var popupCounter = 1;
$(window).on('blur', function () {

	var d = new Date();
	console.log( 'Bạn vừa vi phạm quy chế lần: '+ popupCounter +' lúc ' + d.toLocaleTimeString() );
	popupCounter ++;
	
});
 
<!-- window.onbeforeunload = function (e)  -->
<!-- { -->
    <!-- e = e || window.event; -->
	<!-- e.returnValue = 'BẠN CÓ CHẮC CHẮN DỪNG THI ? '; -->
<!-- }; -->

</script>
<!-- END: trigger_open -->

<!-- END: main -->
