<!-- BEGIN: main -->
<!DOCTYPE html>
<html lang="{LANG.Content_Language}" xmlns="http://www.w3.org/1999/xhtml" prefix="og: http://ogp.me/ns#">
	<head>
		<title>{THEME_PAGE_TITLE}</title>
		<!-- BEGIN: metatags -->
		<meta {THEME_META_TAGS.name}="{THEME_META_TAGS.value}" content="{THEME_META_TAGS.content}">
		<!-- END: metatags -->
		<link rel="shortcut icon" href="{SITE_FAVICON}">
 
  		<style type="text/css" media="all">
		 body {
			margin: 0;
			padding: 0;
			background-color: #FAFAFA;
			font-size:12px;
			font-family: arial
		}
		* {
			box-sizing: border-box;
			-moz-box-sizing: border-box;
		}
		h1{
			font-weight: bold;
			font-size: 14px;
			margin: 0;
			display: inline-block;
		}
		.question p{
			margin:0
		}
		.basicInfo {
			margin: 10px 0;
			font-size:12px
		}
		.basicInfo .code {
			font-weight: bold;
			color: #000;
			font-size:12px
		}
		.boxtest .test .question {
			padding: 8px 0;
			font-weight: bold;
			font-size: 12px;
		}
		.OnlineTestDoTest .boxinfo {
			border: 1px #ccc solid;
			padding: 10px;
		}
		.OnlineTestDoTest .fixed {
			padding: 4px 0;
		}
		.boxtest .test div {
			font-weight: normal;
			vertical-align: middle;
		}
		.boxtest .test div input {
			display: inline-block;
			vertical-align: middle;
		}
		.boxtest .test div.trueanswer {
 			font-weight: bold;
			color: #000;
		}
		.boxtest .test div.checked.wrong {
			text-decoration: line-through;
		}
		.col{
			width:33.3333%;
			float:left;		
		}
		.page {
			width: 21cm;
			min-height: 29.7cm;
			padding: 2cm;
			margin: 1cm auto;
			border: 1px #D3D3D3 solid;
			border-radius: 5px;
			background: white;
			box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
		}
 
		@page {
			size: A4;
			margin: 0;
		}
		@media print {
			.page {
				margin: 0;
				border: initial;
				border-radius: initial;
				width: initial;
				min-height: initial;
				box-shadow: initial;
				background: initial;
				page-break-after: always;
			}
			table.padd td{ padding:6px; border:1px #ccc solid; }
			.break {
				word-break: break-all;
				margin-top:10px;
				position: relative;
			}
			.no-pad {
				margin: 6px 0px;
			}
			
			
		}
		table.padd td{ padding:6px; border:1px #ccc solid; }
		.break {
			word-break: break-all;
			margin-top:10px;
			position: relative;
		}
		.price_string {
			position: absolute;
			left: 196px;
			top: 1px;
			font-size: 15px;
			font-weight: bold;
			word-break: break-all;
		}
		.no-pad {
			margin: 6px 0px;
		}
		.clear{
			clear:both;
		}
		.boxtest .test img{
			vertical-align: middle;
			padding: 2px;
		}
		.boxtest .test div.ques {
		   margin-bottom: 4px;
		   font-size: 14px;
		   font-weight: bold
		}
		</style>
 
		<!--[if lt IE 9]>
		<script type="text/javascript" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/html5shiv.min"></script>
		<script type="text/javascript" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/respond.min.js"></script>
		<![endif]-->
		

	</head>
	<!-- onload="window.print();" -->
	<body>

 
		<div id="history-content">
			<div class="page">
				<div class="content">			 
					<div id="OnlineTestDoTest" class="OnlineTestDoTest">
						<div class="testTitle">
							<h1>{LANG.exam}: {DATA.title}</h1>
							<div class="clearfix basicInfo">
							{LANG.code}: <span class="code">{DATA.code}</span> | {LANG.viewed}:  <span class="code">{DATA.viewed}</span> | {LANG.date_added}:  <span class="code">{DATA.date_added}</span>
							</div>
						</div>
						<div class="boxinfo">
							<div class="row">		 
								<div class="col fixed">
									{LANG.full_name}: <strong>{DATA.username}</strong>
								</div>
								<div class="col fixed">
									{LANG.time_test}: <strong>{DATA.time} ({LANG.minutes})</strong>
								</div>
								<div class="col fixed">
									{LANG.share_time_complete}:<strong> {DATA.time_do_test}</strong>
								</div>
								
								<div class="col fixed">
									{LANG.result_wrong}: <strong id="number-error">{DATA.number_error}</strong>
								</div>
								<div class="col fixed">
									{LANG.result_right}: <strong id="number-success">{DATA.number_success}</strong>
								</div>
								<div class="col fixed">
									{LANG.setpoint}: <strong id="number-total">{DATA.score}</strong>
								</div>		
								<div class="clear"></div>
							</div>
						</div>
						<div id="showQuestions" class="boxtest">
							<div class="test">
								<!-- BEGIN: loop -->
								<div class="question" id="question{LOOP.question_id}">
									<div class="ques">{LANG.question} {LOOP.stt}: {LOOP.question}  </div>
									<div class="clear"></div>
									<!-- BEGIN: answers -->
									<div class="{ANSWERS.checked_class} {ANSWERS.trueanswer}"><input class="checkbox" type="checkbox" value="{ANSWERS.key}" name="answers[{LOOP.question_id}][answers][{ANSWERS.key}]" id="answer-{LOOP.question_id}-{ANSWERS.key}" disabled="" {ANSWERS.checked} /> {ANSWERS.name} 
									<div class="clear"></div>
									</div>	            
									<!-- END: answers -->
									 
								</div>		 
	 
								<!-- END: loop -->
							</div>
						</div> 			 
					</div>
				</div>	
			</div>				  
		</div>				  
	</body>
</html>
<!-- END: main -->