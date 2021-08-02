<!-- BEGIN: main -->
<div id="ranking-content">
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
			<form action="" method="post"  enctype="multipart/form-data" id="form-ranking" class="form-horizontal">
				<input type="hidden" name ="ranking_id" value="{DATA.ranking_id}" />
				<input name="save" type="hidden" value="1" />
 
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="inputs-title">{LANG.ranking_title}</label>
					<div class="col-sm-20">
						<input type="text" name="title" value="{DATA.title}" placeholder="{LANG.ranking_title}" id="inputs-title" class="form-control" />
						<!-- BEGIN: error_title --><div class="text-danger">{error_title}</div><!-- END: error_title -->
					</div>
				</div> 
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-description">{LANG.ranking_description}</label>
					<div class="col-sm-20">
						<input type="text" name="description" value="{DATA.description}" placeholder="{LANG.ranking_description}" id="input-description" class="form-control" />
						<!-- BEGIN: error_description --><div class="text-danger">{error_description}</div><!-- END: error_description -->
					</div>
				</div> 
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-min_score">{LANG.ranking_min_score}</label>
					<div class="col-sm-20">
						<input type="text" name="min_score" value="{DATA.min_score}" placeholder="{LANG.ranking_min_score}" id="input-min_score" class="form-control numberonly" />
						<!-- BEGIN: error_min_score --><div class="text-danger">{error_min_score}</div><!-- END: error_min_score -->
					</div>
				</div> 
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-max_score">{LANG.ranking_max_score}</label>
					<div class="col-sm-20">
						<input type="text" name="max_score" value="{DATA.max_score}" placeholder="{LANG.ranking_max_score}" id="input-max_score numberonly" class="form-control" />
						<!-- BEGIN: max_score --><div class="text-danger">{error_max_score}</div><!-- END: max_score -->
					</div>
				</div> 
				                  	 
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-status">{LANG.ranking_status}</label>
					<div class="col-sm-20">
						<select name="status" id="input-status" class="form-control">
							<!-- BEGIN: status -->
							<option value="{STATUS.key}" {STATUS.selected}>{STATUS.name}</option>
							<!-- END: status -->
						</select>
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
(function(d){d.fn.price_format=function(){function l(k,b,a){var c='',g=a.replace(/[^0-9.]/g, '').split('');a=[];for(var e=0,h="",f=g.length-1;0<=f;f--)h+=g[f],e++,3==e&&(a.push(h),e=0,h="");0<e&&a.push(h);for(f=a.length-1;0<=f;f--){g=a[f].split("");for(e=g.length-1;0<=e;e--)c+=g[e];0<f&&(c+=",")}"input"==b?d(k).val(c):d(k).empty().text(c)}this.each(function(k,b){var a=null,c=null;d(b).is("input")||d(b).is("textarea")?(c=d(b).val().replace(/,/g,""),a="input"):(c=d(b).text().replace(/,/g,""),a="other");d(b).on("paste keyup",function(){if( d(b).val().length > 1 && $.isNumeric(d(b).val())){c=d(b).val().replace(/,/g,"").replace(/^0+/, '');}else{c=d(b).val().replace(/,/g,"");if( c== '' ) c = 0;}l(b,a,c)});l(b,a,c)})}})(jQuery);

$('.numberonly').price_format();
</script>
<!-- END: main -->