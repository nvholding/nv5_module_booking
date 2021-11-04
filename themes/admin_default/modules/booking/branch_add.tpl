<!-- BEGIN: main -->
<div id="content">
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
			<form action="" method="post"  enctype="multipart/form-data" id="form-branch" class="form-horizontal">
				<input type="hidden" name ="branch_id" value="{DATA.branch_id}" />
				<input name="save" type="hidden" value="1" />
 
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-title">{LANG.branch_title}</label>
					<div class="col-sm-20">
						<input type="text" name="title" value="{DATA.title}" placeholder="{LANG.branch_title}"  class="form-control" />
						<!-- BEGIN: error_title --><div class="text-danger">{error_title}</div><!-- END: error_title -->
					</div>
				</div> 
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-phone">{LANG.branch_phone}</label>
					<div class="col-sm-20">
						<input type="text" name="phone" value="{DATA.phone}" placeholder="{LANG.branch_phone}" id="input-phone" class="form-control" />
						<!-- BEGIN: error_phone --><div class="text-danger">{error_phone}</div><!-- END: error_phone -->
					</div>
				</div> 
                <div class="form-group required">
					<label class="col-sm-4 control-label" for="input-email">{LANG.branch_email}</label>
					<div class="col-sm-20">
						<input type="text" name="email" value="{DATA.email}" placeholder="{LANG.branch_email}" id="input-email" class="form-control" />
						<!-- BEGIN: error_email --><div class="text-danger">{error_email}</div><!-- END: error_email -->
					</div>
				</div> 
                 <div class="form-group required">
					<label class="col-sm-4 control-label" for="input-address">{LANG.branch_address}</label>
					<div class="col-sm-20">
						<input type="text" name="address" value="{DATA.address}" placeholder="{LANG.branch_address}" id="input-address" class="form-control" />
						<!-- BEGIN: error_address --><div class="text-danger">{error_address}</div><!-- END: error_address -->
					</div>
				</div> 
                 
                	 
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-status">{LANG.branch_status}</label>
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
					<a class="btn btn-default" href="{CANCEL}" title="{LANG.cancel}">{LANG.cancel}</a> 
				</div>          
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
 	$('#form-branch').on('submit', function() {
		$('#submitform,button[type="submit"]').prop('disabled', true);
		$('#submitform .fa-spinner').show();
	});	 
});
</script>
<!-- END: main -->