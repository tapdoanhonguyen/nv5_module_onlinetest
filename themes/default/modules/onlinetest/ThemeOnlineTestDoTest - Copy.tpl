<!-- BEGIN: main -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.7&appId={CONFIG.facebook_appid}";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div id="OnlineTestDoTest" class="OnlineTestDoTest">
	<div  class="OnlineTestDoTestbox">
	
		<div class="summury-info text-center">
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
		
	</div>
	

</div>


<!-- BEGIN: trigger_open -->
<script type="text/javascript" >
$(document).ready(function(){
	$('#sureOpen').trigger('click');
	
})
var popit = true;
 window.onbeforeunload = function() { 
  if(popit === true) {
	   popit = false;
	   return "Are you sure you want to leave?"; 
  }
}

window.addEventListener("beforeunload", function (e) {
  var confirmationMessage = "\o/";

  (e || window.event).returnValue = confirmationMessage; //Gecko + IE
  return confirmationMessage;                            //Webkit, Safari, Chrome
});


$(window).on('focus', function () {
	console.log('focus');
	//alert('VUA DI CHOI VE DUNG KHONG');
});
var popupCounter = 1;

$(window).on('blur', function () {

	var d = new Date();
	var x = document.getElementById("countxyx");
	x.innerHTML = 'BẠN VÙA VI PHẠM LÚC QUY ĐỊNH LẦN '+ popupCounter +' ' + d.toLocaleTimeString();
	
	popupCounter ++;
	//alert('DINH CHANGE TAB A');
	
});


var mouseX = 0;
var mouseY = 0;
var popupCounter = 0;

document.addEventListener("mousemove", function(e) {
	mouseX = e.clientX;
	mouseY = e.clientY;
	<!-- document.getElementById("countxyx").innerHTML = "<br />X: " + e.clientX + "px<br />Y: " + e.clientY + "px"; -->
});

$(document).mouseleave(function () {
	if (mouseY < 100) {
		if (popupCounter < 1) {
			//alert("BẠN CÓ CHẮC NỘP BÀI THI KHÔNG ?");
		}
		//popupCounter ++;
	}
});

window.onbeforeunload = function (e) 
{
    e = e || window.event;

        e.returnValue = 'BẠN CÓ CHẮC CHẮN DỪNG THI ? ';


};

</script>
<!-- END: trigger_open -->

<script type="text/javascript" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/jquery.timer.min.js"></script>
<!-- END: main -->
