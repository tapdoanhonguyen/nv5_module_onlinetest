<!-- BEGIN: main -->
<style type="text/css">
ul.maxscoreuser{
	padding:0;
	margin:0
}
ul.maxscoreuser li{
	border-bottom: 1px solid #EEE;
    margin-bottom: 4px;
	padding: 2px;
}
ul.maxscoreuser li span {
	display: inline-block;
    width: 80px;
    text-align: center;
    margin-right: 6px;
}
ul.maxscoreuser li span img {
	width:100%;
	border:1px #ccc solid;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
}
ul.maxscoreuser .col-left{
	float:left;
}
ul.maxscoreuser p.col-right{
	margin:0
}
ul.maxscoreuser .username{
	font-weight:bold;
	font-size:14px;
}
</style>
<div class="clearfix">
	<ul class="maxscoreuser"> 
		<!-- BEGIN: loop -->
		<li class="clearfix">
			<span class="col-left"><img src="{LOOP.photo}" alt="{LOOP.username}" /></span>
			<p class="col-right">
				<div class="username"><a href="{URL_HREF}/">{LOOP.full_name}</a></div>
                <div>Tổng Điểm: <strong>{LOOP.total_score}</strong></div>
                <div>Level: <strong>{LOOP.level}</strong></div>
                <div>Lượt thi: <strong>{LOOP.exam_number}</strong></div>
			</p>
		</li>
		<!-- END: loop -->
	</ul>
	<div class="clearfix"></div>
</div>

<div class="clearfix"></div>
<!-- END: main -->