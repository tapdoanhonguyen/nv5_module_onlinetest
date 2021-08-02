<!-- BEGIN: main -->
<form class="recharge" role="form" method="post" action="" id="form-recharge">
	<h2 class="form-control-heading">{LANG.card_recharge} </h2>
	<div class="form-group">
		<label for="chonmang" class="col-sm-4 control-label">{LANG.card_type}</label>
		<div class="col-sm-20">
		  <select class="form-control" name="chonmang">
			  <option value="VIETEL">Viettel</option>
			  <option value="MOBI">Mobifone</option>
			  <option value="VINA">Vinaphone</option>
			  <option value="GATE">Gate</option>
			  <option value="VTC">VTC</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="pinnumber" class="col-sm-4 control-label">{LANG.recharge_pinnumber}</label>
		<div class="col-sm-20">
		  <input type="text" class="form-control" id="pinnumber" name="pinnumber" placeholder="{LANG.recharge_pinnumber}" data-toggle="tooltip" data-title="Mã số sau lớp bạc mỏng"/>
		</div>
	</div>
	<div class="form-group">
		<label for="serinumber" class="col-sm-4 control-label">{LANG.recharge_serinumber}</label>
		<div class="col-sm-20">
			<input type="text" class="form-control" id="serinumber" name="serinumber" placeholder="{LANG.recharge_serinumber}" data-toggle="tooltip" data-title="Mã seri nằm sau thẻ">
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-40 text-center">
			<input type="hidden" name="action" value="napthe"/>
			<input type="hidden" name="token" value="{TOKEN}"/>
			<button type="submit" class="btn btn-primary" id="napthe" name="napthe"><i class="fa fa-spinner fa-spin" style="display:none"></i> {LANG.recharge_save}</button>
			
		</div>
	</div>	 
</form>
<div id="message" class="message-box">
 
</div>
<!-- BEGIN: data -->
<table class="table table-bordered table-hover">
	<caption><i class="fa fa-arrow-right"></i> {LANG.top_transaction}</caption>
	<thead>
		<tr>
			<td class="col-sm-4 text-center"><strong>STT</strong></td>
			<td class="col-sm-4 text-center"><strong>{LANG.recharge_history_mang}</strong></td>
			<td class="col-sm-4 text-center"><strong>{LANG.recharge_history_pin}</strong></td>
			<td class="col-sm-4 text-center"><strong>{LANG.recharge_history_seri}</strong></td>
			<td class="col-sm-4 text-center"><strong>{LANG.recharge_history_money}</strong></td>
			<td class="col-sm-4 text-center"><strong>{LANG.recharge_history_recharge_date}</strong></td>
		</tr>
	</thead>
	<tbody>
		<!-- BEGIN: loop -->
		<tr>
			<td class="text-center">{LOOP.stt}</td>						 
			<td class="text-center">{LOOP.supplier}</td>						 
			<td class="text-center">{LOOP.pin_number}</td>						 
			<td class="text-center">{LOOP.seri_number}</td>						 
			<td class="text-center">{LOOP.money}</td>						 
			<td class="text-center">{LOOP.date_added}</td>						 
		</tr>
		<!-- END: loop -->
	</tbody>
</table>
<!-- END: data -->

<script type="text/javascript">

$( 'body' ).on('submit', '#form-recharge', function(e) {
	$('#napthe').prop('disabled', true); 
	var dataContent = $(this).serialize();
	$.ajax({
		type: 'post',
		url: nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=recharge&second=' + new Date().getTime(),
		data: dataContent,
		dataType: 'json',	
		beforeSend: function( ) {	
			$('#form-recharge').find('input, select, button').prop('disabled', true); 
			$('#napthe i').show(); 
		},	
		complete: function() {
			$('#form-recharge').find('input, select, button').prop('disabled', false); 	 
			$('#napthe i').hide(); 
		},
		success: function(json) {		
				 

			if (json['error']) 
			{
				alert( json['error'] );
			}else if ( json['success'] ) 
			{
				var message = '<div>' + json['message'] + '</div>'
				$('#message').html( message );
			}				
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	}); 
	 
	e.preventDefault();
}); 
</script>
<!-- END: main -->
