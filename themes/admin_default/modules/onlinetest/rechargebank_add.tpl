<!-- BEGIN: main -->
<div id="rechargebank-content">
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
			<form action="" method="post"  enctype="multipart/form-data" id="form-rechargebank" class="form-horizontal">
				<input type="hidden" name ="rechargebank_id" value="{DATA.rechargebank_id}" />
				<input name="save" type="hidden" value="1" />
				
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-username">{LANG.rechargebank_username}</label>
					<div class="col-sm-20">
						<span class="spansearch">
							<input type="text" {DISABLED} name="username" value="{DATA.username}" placeholder="{LANG.rechargebank_username}..." class="form-control" id="input-username" autocomplete="off">
							<i class="fa fa-times"></i>
						</span>
						<!-- BEGIN: error_username --><div class="text-danger">{error_username}</div><!-- END: error_username -->
					</div>
				</div> 		                  	 
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-bank">{LANG.rechargebank_bank}</label>
					<div class="col-sm-20">
						<span class="spanbank">
							<input type="hidden" {DISABLED} name="bank_id" value="{DATA.bank_id}" />
							<input type="text" {DISABLED} name="banktitle" value="{DATA.banktitle}" placeholder="{LANG.rechargebank_bank}..." class="form-control" id="input-bank" autocomplete="off">
							<i class="fa fa-times"></i>
						</span>
						<!-- BEGIN: error_bank_id --><div class="text-danger">{error_bank_id}</div><!-- END: error_bank_id -->
					</div>
				</div> 		                  	 
  		                  	 
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-transaction">{LANG.rechargebank_transaction}</label>
					<div class="col-sm-20">
						<input type="text" {DISABLED} name="transaction" value="{DATA.transaction}" placeholder="{LANG.rechargebank_transaction}" id="input-transaction" class="form-control" />
						<!-- BEGIN: error_transaction --><div class="text-danger">{error_transaction}</div><!-- END: error_transaction -->
					</div>
				</div> 		                  	 
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-money">{LANG.rechargebank_money}</label>
					<div class="col-sm-20">
						<input type="text" {DISABLED} name="money" value="{DATA.money}" placeholder="{LANG.rechargebank_money}" id="input-money" class="form-control" />
						<!-- BEGIN: error_money --><div class="text-danger">{error_money}</div><!-- END: error_money -->
					</div>
				</div> 		                  	 
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-note">{LANG.rechargebank_note}</label>
					<div class="col-sm-20">
						<input type="text" {DISABLED} name="note" value="{DATA.note}" placeholder="{LANG.rechargebank_note}" id="input-note" class="form-control" />
					</div>
				</div> 		                  	 
    
				<div align="center">
					<input {DISABLED} class="btn btn-primary" type="submit" value="{LANG.save}">
					<a class="btn btn-primary" href="{CANCEL}" title="{LANG.back}">{LANG.back}</a> 
					<div class="clearfix">
					<p style="color:red;font-style:italic;padding-top:10px">Chú ý: Thông tin giao dịch sẽ không thể cập nhật lại</p>
					</div>
					
				</div>          
			</form>
		</div>
	</div>
</div>
<script src="{NV_BASE_SITEURL}themes/{THEME}/js/jquery.number.min.js"></script>
<script type="text/javascript">
$(function(){
	$('#input-money').number( true, 0 );
});

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
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=rechargebank&action=getUsername&username=' +  encodeURIComponent(request) + '&nocache=' + new Date().getTime(),
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



$('input[name="banktitle"]').on('keydown, keyup', function(){
	if( $(this).val() != '')
	{
		$('.spanbank i').show();
	}else{
		$('.spanbank i').hide();
	}	
});
$('.spanbank i').on('click', function(){
	$(this).hide();
	$('input[name="bank_id"]').val('');
	$('input[name="banktitle"]').val( '' );
});
$('input[name="banktitle"]').autofill({
	'source': function(request, response) {
		if( $('input[name="banktitle"').val().length > 2 )
		{	 
			$.ajax({
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=rechargebank&action=getBank&banktitle=' +  encodeURIComponent(request) + '&nocache=' + new Date().getTime(),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['title'],
							value: item['bank_id']
						}
					}));
				}
			});
		}
	},
	'select': function(item) {
		$('input[name="bank_id"]').val( item['value'] );
		$('input[name="banktitle"]').val( item['label'] );
	}
});
 
//$('.dropdown-menu.template').on('mouseenter', function(){
//    console.log('clicked inside active element');
//}).on('mouseleave', function(){ 
//    console.log('clicked outside active element');
//});
 
</script>
<!-- END: main -->