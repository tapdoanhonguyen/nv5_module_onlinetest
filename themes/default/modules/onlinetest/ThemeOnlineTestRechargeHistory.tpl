<!-- BEGIN: main -->

<!-- BEGIN: data -->
<table class="table table-bordered table-hover">
	<caption><i class="fa fa-arrow-right"></i> {LANG.transaction_list}</caption>
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
<div class="clearfix"></div>
<!-- BEGIN: generate_page -->
<div class="generate_page text-center">{GENERATE_PAGE}</div>
<!-- END: generate_page -->	
<!-- END: data -->

<!-- BEGIN: no_data -->
<div>{LANG.recharge_history_no_data}</div>
<!-- END: no_data -->

<!-- END: main -->
