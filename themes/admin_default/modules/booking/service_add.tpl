<!-- BEGIN: main -->

<div id="content">
	
    <!-- BEGIN: error_warning -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {error_warning}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <br>
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
			<form action="" method="post"  enctype="multipart/form-data" id="form-service" class="form-horizontal">
				<input type="hidden" name ="service_id" value="{DATA.service_id}" />
				<input name="save" type="hidden" value="1" />
 
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-service_name">{LANG.service_service_name}</label>
					<div class="col-sm-20">
						<input type="text" name="service_name" value="{DATA.service_name}" placeholder="{LANG.service_service_name}" id="input-service_name" class="form-control" />
						<!-- BEGIN: error_service_name --><div class="text-danger">{error_service_name}</div><!-- END: error_service_name -->
					</div>
				</div> 
                <div class="form-group required">
					<label class="col-sm-4 control-label" for="input-service_name">{LANG.service_image}</label>
					<div class="col-sm-20">
						<div class="input-group">
							<input type="text" name="image" id="image" value="{DATA.image}" class="form-control" placeholder="{LANG.service_select_image}"> 
							<label class="input-group-btn">
								<span class="btn btn-info">
									{LANG.service_select_image}<input id="selectimage" type="button" style="display: none;">
								</span>
							</label>
						</div>
					</div>
				</div> 
                	 
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-status">{LANG.service_status}</label>
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
$("#selectimage").click(function() {
	var area = "image";
	var path = "{NV_UPLOADS_DIR}/{MODULE_UPLOAD}";
	var currentpath = "{CURRENT}";
	var type = "image";
	nv_open_browse("{NV_BASE_ADMINURL}index.php?{NV_NAME_VARIABLE}=upload&popup=1&area=" + area + "&path=" + path + "&type=" + type + "&currentpath=" + currentpath, "NVImg", 850, 500, "resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
	return false;
});
</script>
<!-- END: main -->