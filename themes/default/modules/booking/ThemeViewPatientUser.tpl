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
			<h3 class="panel-title fl"><i class="fa fa-list"></i> {LANG.patient_info}: {USER.full_name} - {USER.username}</h3> 
			<div class="fr">
				<a href="javascript:void(0);" onclick="addPatient('{USER.userid}', '{USER.token}')" data-toggle="tooltip" data-placement="top"  class="btn btn-success btn-sm" title="Thêm mới"><i class="fa fa-plus"></i></a>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="panel-body">
			<div class="well">
				<div class="row">
					<form id="searchForm" action="" method="post" enctype="multipart/form-data">
						
						<div class="col-sm-14 col-md-10">
							<div class="form-group">
								<label class="control-label clear" for="input-doctors">{LANG.patient_doctors}</label>
								<select name="doctors" id="module_doctors" class="form-control booking-info-inp" >
					 
								</select>								 
							</div>
						</div>
 
 						<div class="col-sm-10 col-md-10">
							<div class="form-group">
								<label class="control-label clear" for="input-date_added">{LANG.patient_date_added}</label>
								<input type="text" name="df" value="{DATA.date_from}" id="date_from"  placeholder="{LANG.patient_date_from}" class="form-control" autocomplete="off" style="display:inline-block;width:100px"> <strong>:</strong>
								<input type="text" name="dt" value="{DATA.date_to}" id="date_to" placeholder="{LANG.patient_date_to}" class="form-control" autocomplete="off" style="display:inline-block;width:100px">
							</div> 
						</div>

						<div class="col-sm-24 col-md-24">
							 
								<input type="hidden" value="{USER.userid}" name="userid"/>
								<input type="hidden" value="{USER.token}" name="token"/>
								<button type="submit" class="btn btn-primary" > <i class="fa fa-search"></i> Tìm kiếm </button>
								<!-- <button class="btn btn-info export_file" data-all="0" > <i class="fa fa-download"></i> Xuất danh sách tìm kiếm </button> -->
								<!-- <button class="btn btn-info export_file" data-all="1"> <i class="fa fa-download"></i> Xuất tất cả </button> -->
						  
						</div> 
						
						
					</form>
 
				</div>
				<div class="clearfix"></div>
			</div>
			<div id="userinfo" class="table-responsive">  
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<td class="col-md-1x text-center">{LANG.patient_fullname}</td>
							<td class="col-md-1x text-center">{LANG.patient_gender}</td>
							<td class="col-md-1x text-center">{LANG.patient_birthday}</td>
							<td class="col-md-1x text-center">{LANG.patient_phone}</td>
							<td class="col-md-1x text-center">{LANG.patient_address}</td>								
						</tr>
					</thead>
					<tbody>
						<tr>

							<td class="text-left">
								{USER.full_name}
							</td>
							<td class="text-center">
								{USER.gender}
							</td>
							<td class="text-center">
								{USER.age}
							</td>
							<td class="text-center">
								{USER.username}
							</td>
							<td class="text-center">
								{USER.address}
							</td>
							 
						</tr>
					</tbody>
				</table>
			</div>
			<div id="showcontent" class="table-responsive">
				<!-- BEGIN: data -->
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<td class="col-md-0 text-center"><strong>{LANG.patient_stt}</strong></td>
							<td class="col-md-1x text-center"><strong>{LANG.patient_date_added}</strong></td>
							<td class="col-md-1x text-center"><strong>{LANG.patient_doctors}</strong></td>
							<td class="col-md-1x text-center"><strong>{LANG.patient_blood_pressure}</strong></td>
							<td class="col-md-1x text-center"><strong>{LANG.patient_price}</strong></td>
							<td class="col-md-1x text-center"><strong>{LANG.patient_patient_result}</strong></td>
							<td class="col-md-1x text-center"><strong>{LANG.patient_typemedicine}</strong></td>
						</tr>					
					</thead>
					<tbody>
						<!-- BEGIN: loop --> 
						<tr id="group_{LOOP.patient_id}">
							<td class="text-center">
								{LOOP.stt}
							</td>
							<td class="text-left">
								{LOOP.date_added}
							</td>
							<td class="text-center">
								{LOOP.doctors}
							</td>
							<td class="text-center">
								{LOOP.blood_pressure}
							</td>
							<td class="text-center">
								{LOOP.price}
							</td>
							<td class="text-center">
								{LOOP.patient_result}
							</td>
							 <td class="text-center">
								{LOOP.typemedicine}
							</td>
							 
						</tr>
						<!-- END: loop -->
					</tbody>
				</table>
				<!-- END: data -->
				<!-- BEGIN: no_data -->
				<table class="table table-bordered table-hover">
 
					<tbody>
						<tr >
							<td class="text-center">
								{LANG.no_data}
							</td>
							
						</tr>
					</tbody>
				</table>
				<!-- END: no_data -->
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
</div> 
<div class="modal fade" id="ModalAddList" role="dialog">
	<div class="modal-dialog modal-md">
	  <div class="modal-content">
		<div class="modal-header">
		  <button type="button" class="close" data-dismiss="modal">&times;</button>
		  <h4 class="modal-title"></h4>
		</div>
		<div class="modal-body">
			<form id="insertForm" action="" method="post" enctype="multipart/form-data">	
				<div class="form-detail">
					<div class="form-group">
						<div>
							<input type="text" class="form-control required input" placeholder="Ngày nhập" value="Ngày nhập: {DATE_ADDED}" disabled  >
						</div>
					</div>
					<div class="form-group">
						<div>
							<input type="text" class="form-control required input" placeholder="Bác sỹ khám" value="Bác sỹ khám: {DOCTORS.full_name}" disabled  >
						</div>
					</div>
					<div class="form-group">
						<div>
							<input type="text" class="form-control required input" placeholder="Chi phí" value="{USER.price}" name="price" maxlength="250" >
						</div>
					</div>
					<div class="form-group">
						<div>
							<input type="text" class="form-control required input" placeholder="Huyết áp" value="{USER.blood_pressure}" name="blood_pressure" maxlength="250" >
						</div>
					</div>
					<div class="form-group">
						<div>
							<textarea type="text" class="form-control required input" placeholder="Diễn biến điều trị" name="patient_result">{USER.patient_result}</textarea>
						</div>
					</div>
					<div class="form-group">
						<div>
							<textarea type="text" class="form-control required input" placeholder="Chỉ định thuốc"  name="typemedicine">{USER.typemedicine}</textarea>
						</div>
					</div>
					<div class="text-center">
						<input type="hidden" name="patient_id" value="{DATA.patient_id}">
						<input type="hidden" name="userid" value="{USER.userid}">
						<input type="hidden" name="token" value="{USER.token}">
						<button id="insertbutton" type="submit" class="login btn btn-success btn-sm">
							Thêm bệnh án
						</button>
					</div>
				
				</div>
 
			</form>
		</div> 
	  </div>  
	</div>
</div>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
 
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
<script type="text/javascript">

function addPatient ( userid, token ){
	
	$('#ModalAddList .modal-title').html('{LANG.addpatient}');
	$('#ModalAddList').modal();
	$('.tooltip').remove();
	
}


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

	$("#module_doctors").select2({  
		multiple: true,        
		maximumSelectionLength: 1,
		placeholder: 'Chọn bác sỹ *',
		ajax: {                
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&action=getDoctors2&second=' + new Date().getTime(),
			type: "post",      
			dataType: 'json',  
			delay: 250,        
			data: function (params) {

				var query = {
					search : params.term
				}
			
				
				return query;
			},                 
			processResults: function (response) {
				if(  response['data'] )
				{              
					return {   
						results: response['data']
					};         
				}              
				else           
				{              
							   
					return {   
						results: {}
					};         
				}              
			},                 
			cache: true        
		},                     
		language : '{NV_LANG_DATA}'
	}); 

	
	$("#insertForm").on('submit', function(e) {
		e.preventDefault();    
	
		$.ajax({               
			type: "POST",      
			dataType: 'json',  
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '='+ nv_func_name +'&action=insertpatient&second=' + new Date().getTime(),
			data: $('#insertForm').serialize(),
			beforeSend: function() {
				 
			},	               
			complete: function() {
	 
			},                 
			success: function(json) {
				if( json['success'] )
				{              
					$('#showcontent').html(json['template']);
				 
				}              
			 
			},                 
			error: function(xhr, ajaxOptions, thrownError) {
				 
				console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}                  
		});                    
	}); 
	$("#searchForm").on('submit', function(e) {
		e.preventDefault();    
	
		$.ajax({               
			type: "POST",      
			dataType: 'json',  
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '='+ nv_func_name +'&second=' + new Date().getTime(),
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