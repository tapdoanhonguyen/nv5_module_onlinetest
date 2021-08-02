<!-- BEGIN: main -->
<form id="OnlinetestSearch">
	<div class="input-group">
		<input type="text" value="{Q}" name="q" class="form-control" placeholder="Nhập tên bài thi...">
		<div class="input-group-btn">
			<button type="submit" id="SearchButton" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Tìm kiếm</button>
		</div>		
	</div>
	<div id="contenttype" style="margin-top: 10px;text-align:center">
		<!-- BEGIN: type -->
		<label><input type="radio" name="type" value="{TYPE.key}" {TYPE.checked}> {TYPE.name} </label>
		<!-- END: type -->
	</div>
</form>
<script>
$('#OnlinetestSearch').submit(function(){

	window.location.href = '{URLSEARCH}' + '?q=' + $('input[name="q"]').val().replace(' ', '+') + '&t=' + $('input[name="type"]:checked').val(); return false;
});
</script>
<!-- END: main -->