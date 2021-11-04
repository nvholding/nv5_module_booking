<!-- BEGIN: main -->
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<div id="patient-content">
	<!-- BEGIN: success -->
	<div class="alert alert-success">
		<i class="fa fa-check-circle"></i> 
		{SUCCESS}
		<i class="fa fa-times"></i>
	</div>
	<!-- END: success -->
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title fl">
				<i class="fa fa-list"></i>
				{LANG.patient_info}: {USER.full_name} - {USER.username}
			</h3> 
			<div class="fr">
				<a href="javascript:void(0);" onclick="printPatient('{USER.userid}', '{USER.token}')" data-toggle="tooltip" data-placement="top"  class="btn btn-info btn-sm" title="In hồ sơ bệnh án">
					<i class="fa fa-print" aria-hidden="true"></i>
				</a>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="panel-body">
			<div class="well">
				<div class="row">
					<form id="searchForm" action="" method="post" enctype="multipart/form-data">
						
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label" for="input-doctors">
									Thầy trị liệu
								</label>
								<select name="doctors_id" id="module_doctors" class="form-control booking-info-inp" >
									
								</select>								 
							</div>
						</div>
						
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label clear" for="input-date_added">
									Ngày trị liệu
								</label>
								<input type="text" name="df" value="{DATA.date_from}" id="date_from"  placeholder="{LANG.patient_date_from}" class="form-control" autocomplete="off" style="display:inline-block;width:100px"> 
								<strong>:</strong>
								<input type="text" name="dt" value="{DATA.date_to}" id="date_to" placeholder="{LANG.patient_date_to}" class="form-control" autocomplete="off" style="display:inline-block;width:100px">
							</div> 
						</div>

						<div class="col-sm-12">
							<div class="fixposition">
								
								<input type="hidden" name="action" value="search" />
								<input type="hidden" value="{USER.userid}" name="userid"/>
								<input type="hidden" value="{USER.token}" name="token"/>
								<button id="submitform" type="submit" class="btn btn-primary" > 
									<i class="fa fa-search"></i> 
									Tìm kiếm 
								</button>
								<!-- <button class="btn btn-info export_file" data-all="0" > <i class="fa fa-download"></i> Xuất danh sách tìm kiếm </button> -->
								<!-- <button class="btn btn-info export_file" data-all="1"> <i class="fa fa-download"></i> Xuất tất cả </button> -->
							</div> 
						</div> 
					</form>
				</div>
				<div class="clearfix"></div>
			</div>
			<div id="userinfo">
				<form id="updateForm" action="" method="post" enctype="multipart/form-data">
					<input type="hidden" value="{USER.userid}" name="userid"/>
					<input type="hidden" value="{USER.token}" name="token"/>
					<h2 style="margin:20px 0px; font-size:20px"> 
						Thông tin Khách Hàng ( Số lần khám bệnh còn lại: {LOOP.kham_conlai})
					</h2>
					<div  class="row">  
						<div class="col-sm-5">  
							<div class="form-group">
								<label class="control-label">
									Xưng hô
								</label>
								<div class="form-control">
									{USER.confess}
								</div>
							</div>
						</div>
						<div class="col-sm-5">  
							<div class="form-group">
								<label class="control-label">
									{LANG.patient_fullname}
								</label>
								<div class="form-control">
									{USER.full_name}
								</div>
							</div>
						</div>
						
						<div class="col-sm-3">
							<div class="form-group">
								<label class="control-label">
									{LANG.grouppatient}
								</label>
								<div class="form-control">
									{USER.patient_group_name}
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label class="control-label">
									Gói dịch vụ
								</label>
								<div class="form-control">
									{USER.service_package}
								</div>
							</div>
						</div>
						
						<div class="col-sm-3">  
							<div class="form-group">
								<label class="control-label">
									{LANG.patient_gender}
								</label>
								<div class="form-control">
									{USER.gender}
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label clear">
									{LANG.patient_birthday}
								</label>
								<div class="form-control">
									{USER.birthday}
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label">
									{LANG.patient_phone}
								</label>
								<div class="form-control">
									{USER.username}
								</div>
							</div>
						</div>
						<div class="col-sm-5">
							<div class="form-group">
								<label class="control-label">
									{LANG.patient_email}
								</label>
								<div class="form-control">
									{USER.email}
								</div>
							</div>
						</div>
						<div class="col-sm-5">
							<div class="form-group">
								<label class="control-label">
									Liên hệ khác
								</label>
								<div class="form-control">
									{USER.other_contact}
								</div>
							</div>
						</div>
						<div class="col-sm-10">
							<div class="form-group">
								<label class="control-label">
									Công việc
								</label>
								<div class="form-control">
									{USER.work}
								</div>
							</div>
						</div>
						<div class="col-sm-24">
							<div class="form-group">
								<label class="control-label">
									{LANG.patient_address}
								</label>
								<div class="form-control">
									{USER.address}
								</div>
							</div>
						</div>
						
						
						<div class="col-sm-24">
							<div class="form-group">
								<label class="control-label">
									{LANG.patient_medical_history}
								</label>
								<div class="form-control">
									{USER.history}
								</div>
							</div>
						</div>
						<div class="col-sm-24">
							<div class="form-group">
								<label class="control-label">
									Kết quả điều trị
								</label>
								<div class="form-control">
									{USER.patient_result}
								</div>
							</div>
						</div>
						
						<div class="col-sm-24">
							<div class="form-group">
								<label class="control-label">
									{LANG.patient_aspirations_treatment}
								</label>
								<div class="form-control">
									{USER.expect}
								</div>
							</div>
						</div>
						<div class="col-sm-24">
							<div class="form-group">
								<label class="control-label">
									Ghi chú
								</label>
								<div class="form-control">
									{USER.note}
								</div>
							</div>
						</div>
						
					</div>
					
				</form>
			</div>
			<h2 style ="margin:20px 0px; font-size:20px"> 
				Liệu trình trị liệu 
			</h2>
			<div id="showcontent" class="table-responsive">
				<!-- BEGIN: data -->
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<td class="col-md-0 text-center">
								<strong>
									Số thứ tự
								</strong>
							</td>
							<td class="col-md-1x text-center">
								<strong>
									Ngày trị liệu
								</strong>
							</td>
							<td class="col-md-1x text-center">
								<strong>
									Thầy trị liệu
								</strong>
							</td>
							<td class="col-md-1x text-center">
								<strong>
									{LANG.patient_blood_pressure}
								</strong>
							</td>
							<td class="col-md-1x text-center">
								<strong>
									Phí dịch vụ
								</strong>
							</td>
							<td class="col-md-1x text-center">
								<strong>
									{LANG.patient_patient_result}
								</strong>
							</td>
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
							<td class="text-left">
								{LOOP.patient_result}
							</td>
							
							
						</tr>
						<!-- END: loop -->
					</tbody>
				</table>
				<!-- END: data -->
				<!-- BEGIN: no_data -->
				<table class="table table-bordered table-hover">
					
					<tbody>
						<tr>
							<td class="text-center">
								{LANG.patient_no_data}
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

<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>

<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
<script type="text/javascript">
	
	function getDataPage(obj, page, id) 
	{
		
		
		param = JSON.parse( $(obj).attr('data-param') );
		param['page'] = page;

		$.ajax({
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=patient&action=search&second=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: param,
			beforeSend: function() {
				
			},	
			complete: function() {
				
			},
			success: function(json) {
				
				if (json['template']) {
					$('#' + id ).html(json['template']);
				}		
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
	function printPatient ( userid, token ){
		
		var win = window.open('{URLPATIENT}&action=print&token=' + token, '_blank');
		if (win) {
			win.focus();
		} else {
			alert('Please allow popups for this website');
		} 
		$('.tooltip').remove();
		
	}
	$(function () { 
		
		
		$("#date_from,#date_to,#birthday").datepicker({
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
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=patient&action=getDoctors2&second=' + new Date().getTime(),
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


		$("#searchForm").on('submit', function(e) {
			e.preventDefault();    
			
			$.ajax({               
				type: "POST",      
				dataType: 'json',  
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=patient&second=' + new Date().getTime(),
				data: $('#searchForm').serialize(),
				beforeSend: function() {
					$('#submitform').prop('disabled', true);
					$('#submitform i').replaceWith('<i class="fa fa-spinner fa-spin"></i>'); 
				},	               
				complete: function() {
					$('#submitform').prop('disabled', false);
					$('#submitform i').replaceWith('<i class="fa fa-search"></i>'); 
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

		$("#updateButton").on('click', function(e) {
			e.preventDefault();    
			
			$.ajax({               
				type: "POST",      
				dataType: 'json',  
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=patient&action=update&second=' + new Date().getTime(),
				data: $('#updateForm').serialize(),
				beforeSend: function() {
					$("#updateButton").addClass('disabled').prop('disabled', true);
					$("#updateForm").css('opacity', '0.5');
				},	               
				complete: function() {
					$("#updateButton").removeClass('disabled').prop('disabled', false);
					$("#updateForm").css('opacity', '1');
				},                 
				success: function(json) {
					if( json['template'] )
					{              
						<!-- $('#showcontent').html(json['template']); -->
						
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