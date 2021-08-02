<!-- BEGIN: main -->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
<!--
 /* Font Definitions */
 @font-face
	{font-family:Wingdings;
	panose-1:5 0 0 0 0 0 0 0 0 0;}
@font-face
	{font-family:"Cambria Math";
	panose-1:2 4 5 3 5 4 6 3 2 4;}
@font-face
	{font-family:Calibri;
	panose-1:2 15 5 2 2 2 4 3 2 4;}
@font-face
	{font-family:Cambria;
	panose-1:2 4 5 3 5 4 6 3 2 4;}
@font-face
	{font-family:"Segoe UI";
	panose-1:2 11 5 2 4 2 4 2 2 3;}
 /* Style Definitions */
 body{font-size: 12px;font-family: 'Times New Roman', Times, serif;}
 p.MsoNormal, li.MsoNormal, div.MsoNormal
	{margin-top:0in;
	margin-right:0in;
	margin-bottom:8.0pt;
	margin-left:0in;
	line-height:107%;
	font-size:12.0pt;
	font-family:"Times New Roman", Times, sans-serif;
	}
p.MsoHeader, li.MsoHeader, div.MsoHeader
	{mso-style-link:"Header Char";
	margin-top:0in;
	margin-right:0in;
	margin-bottom:8.0pt;
	margin-left:0in;
	line-height:107%;
	font-size:12.0pt;
	font-family:"Times New Roman", Times, sans-serif;}
p.MsoFooter, li.MsoFooter, div.MsoFooter
	{mso-style-link:"Footer Char";
	margin-top:0in;
	margin-right:0in;
	margin-bottom:8.0pt;
	margin-left:0in;
	line-height:107%;
	font-size:12.0pt;
	font-family:"Times New Roman", Times, sans-serif;}
p.ListParagraph1, li.ListParagraph1, div.ListParagraph1
	{mso-style-name:"List Paragraph1";
	mso-style-link:"List Paragraph Char";
	margin-top:0in;
	margin-right:0in;
	margin-bottom:10.0pt;
	margin-left:.5in;
	line-height:115%;
	font-size:12.0pt;
	font-family:"Times New Roman", Times, sans-serif;}
span.ListParagraphChar
	{mso-style-name:"List Paragraph Char";
	mso-style-link:"List Paragraph1";}
span.HeaderChar
	{mso-style-name:"Header Char";
	mso-style-link:Header;
	font-family:"Times New Roman", Times, sans-serif;}
span.FooterChar
	{mso-style-name:"Footer Char";
	mso-style-link:Footer;
	font-family:"Times New Roman", Times, sans-serif;}
.MsoChpDefault
	{font-family:"Times New Roman", Times, sans-serif;}
 /* Page Definitions */
 @page WordSection1
	{size:595.3pt 841.9pt;
	margin:42.5pt 42.5pt 42.5pt 42.5pt;}
div.WordSection1
	{page:WordSection1;}
 /* List Definitions */
 ol
	{margin-bottom:0in;}
ul
	{margin-bottom:0in;}
-->
</style>

</head>
<body>
<div class="WordSection1">
<p class=MsoNormal align=center style='color:black;font-size:14pt'>
	<b><span style='color:black;font-size:14pt'>{DATA.title}</span></b>
</p>
<br />
<div style="clear:both"></div>	 							
<!-- BEGIN: question -->
<p class=MsoNormal style='font-size:12.0pt;line-height:115%;'>	
	<span style='font-size:12.0pt;line-height:107%;font-family:"Times New Roman",serif;color:black'>
		<b>{LANG.question_num} {QUESTION.number}:</b> <span style='font-size:12.0pt;line-height:107%;font-family:"Times New Roman",serif;color:black'>{QUESTION.question}</span>
	</span>
</p> 
<!-- BEGIN: answer -->
<p class=MsoNormal style='font-size:12.0pt;line-height:115%;'>	
	<b>{ANSWER.title}.</b> <span style='font-size:12.0pt;line-height:107%;font-family:"Times New Roman",serif;color:black'>{ANSWER.name}</span>
</p> 
<!-- END: answer -->
<!-- END: question --> 

<!-- BEGIN: trueanswer -->
<p class=MsoNormal align=left style='color:black;font-size:14pt;font-weight:bold'> 
<span>Đáp án: </span>
<!-- BEGIN: loop --> 
<span>{NUMBER}{ANSWER}, </span>
<!-- END: loop --> 
</p>
<!-- END: trueanswer --> 

</div>

<!-- <div style='mso-element:footer' id="f1">
	<p class=MsoFooter>
		Page <span style='mso-field-code:" PAGE "'></span>
	</p>
</div> -->
<!-- sang trang mới<br clear=all style='mso-special-character:line-break;page-break-after:always' /> -->
<!-- tab space <span style='mso-tab-count:1'>            </span> -->
</body>	
</html>
<!-- END: main -->