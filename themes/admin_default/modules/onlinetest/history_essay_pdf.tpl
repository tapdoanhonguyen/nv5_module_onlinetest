<!-- BEGIN: main -->
<div style="width:100%; font-family: freeserif;font-size:16px;line-height: 25px;text-align: justify; ">
	<div style="text-align: justify; margin: 10px 16mm 0 16mm;">
		<h1> {DATA.title} - {DATA.full_name}</h1>
	</div>
	<!-- BEGIN: loop -->
	<div style="text-align: justify; margin: 0 16mm 0 16mm;">
		<p>
			<b>{LANG.question_stt} {LOOP.stt}</b>		
		</p>
		<div>{LOOP.question}</div>
		<div>
			<p>
				<b>Trả lời: </b>		
			</p>
			{LOOP.answer}
		</div>
	</div>			
	<!-- END: loop -->  
</div>	
<!-- END: main -->
 