<!-- BEGIN: main -->
<div id="recharge-content">
   <!-- BEGIN: error_warning -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {error_warning}<i class="fa fa-times"></i>           
    </div>
    <!-- END: error_warning -->
    <div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-pencil"></i> {CAPTION}</h3>
			<div class="pull-right">
				<button type="submit" form="form-stock" data-toggle="tooltip" class="btn btn-primary" title="{LANG.save}"><i class="fa fa-save"></i></button> 
				<a href="{CANCEL}" data-toggle="tooltip" class="btn btn-default" title="{LANG.cancel}"><i class="fa fa-reply"></i></a>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="" method="post"  enctype="multipart/form-data" id="form-recharge" class="form-horizontal">
				<input type="hidden" name ="recharge_id" value="{DATA.recharge_id}" />
				<input name="save" type="hidden" value="1" />
				
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-username">{LANG.recharge_username}</label>
					<div class="col-sm-20">
						<span class="spansearch">
							<input type="text" name="username" value="{DATA.username}" placeholder="{LANG.recharge_username}..." class="form-control" id="input-username" autocomplete="off">
							<i class="fa fa-times"></i>
						</span>
						<!-- BEGIN: error_username --><div class="text-danger">{error_username}</div><!-- END: error_username -->
					</div>
				</div> 		                  	 
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-bank">{LANG.recharge_bank}</label>
					<div class="col-sm-20">
						<span class="spanbank">
							<input type="hidden" name="bankcode" value="{DATA.bankcode}" />
							<input type="text" name="bank" value="{DATA.bank}" placeholder="{LANG.recharge_bank}..." class="form-control" id="input-bank" autocomplete="off">
							<i class="fa fa-times"></i>
						</span>
						<!-- BEGIN: error_bank --><div class="text-danger">{error_bank}</div><!-- END: error_bank -->
					</div>
				</div> 		                  	 
  		                  	 
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-transaction">{LANG.recharge_transaction}</label>
					<div class="col-sm-20">
						<input type="text" name="transaction" value="{DATA.transaction}" placeholder="{LANG.recharge_transaction}" id="input-transaction" class="form-control" />
						<!-- BEGIN: error_transaction --><div class="text-danger">{error_transaction}</div><!-- END: error_transaction -->
					</div>
				</div> 		                  	 
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-money">{LANG.recharge_money}</label>
					<div class="col-sm-20">
						<input type="text" name="money" value="{DATA.money}" placeholder="{LANG.recharge_money}" id="input-money" class="form-control" />
						<!-- BEGIN: error_money --><div class="text-danger">{error_money}</div><!-- END: error_money -->
					</div>
				</div> 		                  	 
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-note">{LANG.recharge_note}</label>
					<div class="col-sm-20">
						<input type="text" name="note" value="{DATA.note}" placeholder="{LANG.recharge_note}" id="input-note" class="form-control" />
					</div>
				</div> 		                  	 
    
				<div align="center">
					<input class="btn btn-primary" type="submit" value="{LANG.save}">
					<a class="btn btn-primary" href="{CANCEL}" title="{LANG.cancel}">{LANG.cancel}</a> 
				</div>          
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
$('input[name="username"]').on('keydown, keyup', function(){
	if( $(this).val() != '')
	{
		$('.spansearch i').show();
	}else{
		$('.spansearch i').hide();
	}	
});
$('.spansearch i').on('click', function(){
	$(this).hide();
	$('input[name="username"]').val('');
});
$('input[name="username"]').autofill({
	'source': function(request, response) {
		if( $('input[name="username"').val().length > 2 )
		{	 
			$.ajax({
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=recharge&action=getUsername&username=' +  encodeURIComponent(request) + '&nocache=' + new Date().getTime(),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['username'],
							value: item['userid']
						}
					}));
				}
			});
		}
	},
	'select': function(item) {
		$('input[name="username"]').val( item['label'] );
	}
});



$('input[name="bank"]').on('keydown, keyup', function(){
	if( $(this).val() != '')
	{
		$('.spanbank i').show();
	}else{
		$('.spanbank i').hide();
	}	
});
$('.spanbank i').on('click', function(){
	$(this).hide();
	$('input[name="bank"]').val('');
	$('input[name="bankcode"]').val( '' );
});
$('input[name="bank"]').autofill({
	'source': function(request, response) {
		if( $('input[name="bank"').val().length > 2 )
		{	 
			$.ajax({
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=recharge&action=getBank&bank=' +  encodeURIComponent(request) + '&nocache=' + new Date().getTime(),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['code'],
							value: item['title']
						}
					}));
				}
			});
		}
	},
	'select': function(item) {
		$('input[name="bank"]').val( item['value'] );
		$('input[name="bankcode"]').val( item['label'] );
	}
});

</script>
<!-- END: main -->