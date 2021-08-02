<!-- BEGIN: main -->
<style type="text/css">
ul.maxscore{
	padding:0;
	margin:0
}
ul.maxscore li{
	border-bottom: 1px solid #EEE;
    margin-bottom: 4px;
	padding: 2px;
}
ul.maxscore li span {
	display: inline-block;
    width: 60px;
    background: #FF6317;
    text-align: center;
    padding: 10px 0;
    font-size: 20px;
    font-weight: bold;
    margin-right: 6px;
    color: #FFF;
}
.col-left{
	float:left;
}
p.col-right{
	margin:0
}
ul.maxscore li div{
	line-height: 22px
}
</style>
 	
 
<div class="clearfix">
	<ul class="maxscore"> 
		<!-- BEGIN: loop -->
		<li class="clearfix">
			<span class="col-left">{LOOP.score}</span>
			<div class="col-right">
				<!-- BEGIN: show_url -->
				<div>{LOOP.full_name}</div>
				<div><a href="{LOOP.link}">{LOOP.title}</a></div>
				<!-- END: show_url -->				
				<!-- BEGIN: hide_url -->
				<div>{LOOP.full_name}</div>
				<div>{LOOP.title}</div>
				<!-- END: hide_url -->				
			</div>
		</li>
		<!-- END: loop -->
	</ul>
	<div class="clearfix"></div>
</div>
<!-- END: main -->