<!-- BEGIN: main -->
<!-- BEGIN: display_button -->
<div id="nv-block-login" class="text-center">
	<button type="button" class="login btn btn-success btn-sm" onclick="modalShowByObj('#guestLogin_{BLOCKID}')">
		{GLANG.signin}
	</button>
	<!-- BEGIN: allowuserreg2 -->
	<button type="button" class="register btn btn-primary btn-sm" onclick="modalShowByObj('#guestReg_{BLOCKID}')">
		{GLANG.register}
	</button>
	<!-- END: allowuserreg2 -->
    <!-- BEGIN: allowuserreg_link -->
    <a href="{USER_REGISTER}" class="register btn btn-primary btn-sm">{GLANG.register}</a>
    <!-- END: allowuserreg_link -->
</div>
<!-- START FORFOOTER -->
<div id="guestLogin_{BLOCKID}" class="hidden">
	<div class="page panel panel-default bg-lavender box-shadow">
		<div class="panel-body">
			<h2 class="text-center margin-bottom-lg">
				{LANG.login}
			</h2>
			{FILE "login_form.tpl"}
		</div>
	</div>
</div>
<!-- END FORFOOTER -->
<!-- END: display_button -->

<!-- BEGIN: display_form -->
{FILE "login_form.tpl"}
<!-- END: display_form -->

<!-- BEGIN: allowuserreg -->
<div id="guestReg_{BLOCKID}" class="hidden">
	<div class="page panel panel-default bg-lavender box-shadow">
		<div class="panel-body">
			<h2 class="text-center margin-bottom-lg">
				{LANG.register}
			</h2>
			{FILE "register_form.tpl"}
		</div>
	</div>
</div>
<!-- END: allowuserreg -->

<!-- BEGIN: datepicker -->
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
<!-- END: datepicker -->

<script src="{NV_BASE_SITEURL}themes/default/js/users.js"></script>
<!-- END: main -->

<!-- BEGIN: signed -->
<style type="text/css">
.userinfo{
    display: block;
    clear: both;
}
.userinfo ul{
    padding: 0;
    margin: 0;
}
.userinfo ul li{
    border-bottom: 1px #ccc solid;
   
}
.userinfo ul li:hover{
   
}
.userinfo ul li a{
	padding: 6px 4px;
	display: block;
}
.userinfo ul li a:hover{
	background: #e9e9e9
}
</style>
<div class="content signed clearfix">
    <div class="nv-info" style="display:none"></div>
    <div class="userBlock">
        <div class="row margin-bottom-lg">
    		<div class="col-xs-8 text-center">
    			<a title="{LANG.edituser}" href="#" onclick="changeAvatar('{URL_AVATAR}')"><img src="{AVATA}" alt="{USER.full_name}" class="img-thumbnail bg-gainsboro" /></a>
    		</div>
    		<div class="col-xs-16">
    			<span class="username">{USER.full_name}</span>
    		</div>
			<div class="userinfo">
				<ul>
    				<li class="active">
    					<a href="{URL_MODULE}">{LANG.user_info}</a>
    				</li>
    				<li>
    					<a href="{URL_HREF}editinfo">{LANG.editinfo}</a>
    				</li>
    				<!-- BEGIN: allowopenid -->
    				<li>
    					<a href="{URL_HREF}editinfo/openid">{LANG.openid_administrator}</a>
    				</li>
    				<!-- END: allowopenid -->
    				
					<!-- BEGIN: group -->
    				<li>
    					<a href="{URL_ONLINETEST_GROUP}">{LANG.manager_group}</a>
    				</li>
    				<li>
    					<a href="{URL_ONLINETEST_TYPEEXAM}">{LANG.manager_typeexam}</a>
    				</li>
    				<!-- END: group -->
					<li>
    					<a href="{URL_ONLINETEST_HISTORY}">{LANG.history}</a>
    				</li>
					<li>
    					<a href="{URL_ONLINETEST_VCOIN}">{LANG.vicoin}: {POINT} Vicoin</a>
    				</li>
    				<li>
    					<a href="{URL_ONLINETEST_HISTORY}">{LANG.total_score}: {TOTAL_SCORE}</a>
    				</li>
    				<li>
    					<a href="{URL_ONLINETEST_HISTORY}">{LANG.exam_number}: {EXAM_NUMBER}</a>
    				</li>
    				<li>
    					<a href="{URL_ONLINETEST_HISTORY}">{LANG.user_level}: {USER_LEVEL}</a>
    				</li>
    				<li>
    					<a href="#" onclick="{URL_LOGOUT}(this);"><i class="fa fa-sign-out" aria-hidden="true"></i> Thoát</a>
    				</li>
    			</ul>	
			</div>
    	</div>
		
    </div>
</div>
<script src="{NV_BASE_SITEURL}themes/default/js/users.js"></script>
<!-- END: signed -->