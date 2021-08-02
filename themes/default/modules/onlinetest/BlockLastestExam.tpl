<!-- BEGIN: main -->
<style type="text/css">
ul.grouplastest{
	padding: 0;
    margin: 0;
    font-size: 0;
}
ul.grouplastest li {
    border-bottom: 1px #ccc solid;
    padding: 8px 0px;
    display: block;
}
ul.grouplastest li:first-child {
   padding-top:0
}
ul.grouplastest li:last-child {
   padding-bottom:0;
   border:none
}

ul.grouplastest li .nvtitle {
	 
}
ul.grouplastest li .nvtitle>a{
	font-size: 14px;
	font-weight:bold;
}
ul.grouplastest li .nvtitle>a:hover{
	color:#FF6317;
}
ul.grouplastest li .infoex {
	font-size:12px;
}
ul.grouplastest li .infoex>.v2{
    text-align: center;
    margin-top: 2px
}
ul.grouplastest li .infoex>.v2>a{
	width: 100%;
	
}
ul.grouplastest li .infoex>.v2>a:hover{
	background:#FF6317;
	border: 1px #FF6317 solid;
}
</style>
<div class="clearfix">
	<ul class="grouplastest"> 
		<!-- BEGIN: loop -->
		<li class="clearfix">
			<div class="nvtitle">
				<a href="{LOOP.link}" title="{LOOP.title}">{LOOP.title_cut}</a>
				<div class="infoex">
					<div class="v1">Thuộc: <strong><a href="{LOOP.groupexam_url}" title="{LOOP.groupexam}">{LOOP.groupexam}</a></strong></div>
					<div class="v1">Số câu hỏi: <strong>{LOOP.num_question} câu</strong></div>
					<div class="v1">Thời gian làm bài: <strong>{LOOP.time} phút</strong></div>
					<div class="v2"><a class="btn btn-primary btn-xs" href="{LOOP.link}" title="{LOOP.title}">Làm bài ngay</a></div>
				</div>
			</div>
		</li>
		<!-- END: loop -->
	</ul>
</div>
<!-- END: main -->