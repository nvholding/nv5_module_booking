<!-- BEGIN: main -->
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<div id="patient-content">
	<!-- BEGIN: success -->
		<div class="alert alert-success">
			<i class="fa fa-check-circle"></i> {SUCCESS}<i class="fa fa-times"></i>
		</div>
	<!-- END: success -->
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-list"></i> {LANG.patient_search}</h3> 
		</div>
		<div class="panel-body">
			<div class="well">
				<div class="row">
					<form id="searchForm" action="" method="post" enctype="multipart/form-data">
						
						<div class="col-sm-14 col-md-12">
							<div class="form-group">
								<label class="control-label clear" for="input-keyword">{LANG.patient_keyword}</label>
								<input type="text" name="keyword" value="{DATA.keyword}" placeholder="{LANG.patient_keyword}" id="input-keyword" class="form-control" autocomplete="off">								 
							</div>
						</div>
 
						 
 						<div class="col-sm-10 col-md-12">
							<div class="form-group">
								<label class="control-label clear" for="input-date_added">{LANG.patient_date_added}</label>
								<input type="text" name="date_from" value="{DATA.date_from}" id="date_from"  placeholder="{LANG.patient_date_from}" class="form-control" autocomplete="off" style="display:inline-block;width:100px"> <strong>:</strong>
								<input type="text" name="date_to" value="{DATA.date_to}" id="date_to" placeholder="{LANG.patient_date_to}" class="form-control" autocomplete="off" style="display:inline-block;width:100px">
							</div> 
						</div>

						<div class="col-sm-24 col-md-24">
							<input type="hidden" value="{TOKEN}" name="token"/>
							<button type="submit" class="btn btn-primary" > <i class="fa fa-search"></i> Tìm kiếm </button>
							<!-- <button class="btn btn-info export_file" data-all="0" > <i class="fa fa-download"></i> Xuất danh sách tìm kiếm </button> -->
							<!-- <button class="btn btn-info export_file" data-all="1"> <i class="fa fa-download"></i> Xuất tất cả </button> -->
						</div> 
					</form>
 
				</div>
			</div>
			 
			<div id="showcontent" class="table-responsive">
				 
			</div>
			 
			<!-- BEGIN: generate_page -->
			<div class="row">
				<div class="col-sm-24 text-center">
				{GENERATE_PAGE}			
				</div>
			</div>
			<!-- END: generate_page -->
		</div>
	</div>
</div> 

<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
 
 
<script type="text/javascript">
$(function () { 

	$("#date_from,#date_to").datepicker({
		showOn : "both",
		dateFormat : "dd/mm/yy",
		changeMonth : true,
		changeYear : true,
		showOtherMonths : true,
		buttonImage : nv_base_siteurl + "assets/images/calendar.gif",
		buttonImageOnly : true
	}); 

	$("#searchForm").on('submit', function(e) {
		e.preventDefault();    
	
		$.ajax({               
			type: "POST",      
			dataType: 'json',  
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '='+ nv_func_name +'&action=search&second=' + new Date().getTime(),
			data: $('#searchForm').serialize(),
			beforeSend: function() {
				 
			},	               
			complete: function() {
	 
			},                 
			success: function(json) {
				if( json['template'] )
				{              
					$('#showcontent').html(json['template']);
				 
				}              
			 
			},                 
			error: function(xhr, ajaxOptions, thrownError) {
				 
				console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}                  
		});                    
	}); 
}); 
 
 
</script>
<!-- END: main -->