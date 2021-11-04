<!-- BEGIN: main -->
<div id="appointment-content">
	<!-- BEGIN: error_warning -->
	<div class="alert alert-danger">
		<i class="fa fa-exclamation-circle"></i> {error_warning}<i class="fa fa-times"></i> 
	</div>
	<!-- END: error_warning -->
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-pencil"></i>
				{CAPTION}
			</h3>
			<div class="pull-right">
				<button type="submit" form="form-appointment" data-toggle="tooltip" class="btn btn-primary" title="{LANG.save}"><i class="fa fa-save"></i></button> 
				<a href="{CANCEL}" data-toggle="tooltip" class="btn btn-default" title="{LANG.cancel}"><i class="fa fa-reply"></i></a>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="" method="post"  enctype="multipart/form-data" id="form-appointment" class="form-horizontal">

				<div class="form-group required">
					<label class="control-label col-sm-4" for="customer_full_name">
						{LANG.appointment_customer_full_name}
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="customer_full_name" name="customer_full_name" value="{DATA.customer_full_name}" placeholder="{LANG.appointment_customer_full_name}" type="text">
						<!-- BEGIN: error_customer_full_name -->
						<div class="text-danger">
							{error_customer_full_name}
						</div>
						<!-- END: error_customer_full_name -->
					</div>
				</div>
				<div class="form-group required">
					<label class="control-label col-sm-4" for="customer_phone">
						{LANG.appointment_customer_phone}
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="customer_phone" name="customer_phone" value="{DATA.customer_phone}" placeholder="{LANG.appointment_customer_phone}" type="text" >
						<!-- BEGIN: error_customer_phone -->
						<div class="text-danger">
							{error_customer_phone}
						</div>
						<!-- END: error_customer_phone -->
					</div>
				</div>

				<div class="form-group required">
					<label class="control-label col-sm-4" for="customer_email">
						{LANG.appointment_customer_email}
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="customer_email" name="customer_email" value="{DATA.customer_email}" placeholder="{LANG.appointment_customer_email}" type="text">
						<!-- BEGIN: error_customer_email -->
						<div class="text-danger">
							{error_customer_email}
						</div>
						<!-- END: error_customer_email -->
					</div>
				</div>

				<div class="form-group required">
					<label class="control-label col-sm-4" >
						{LANG.appointment_customer_date_booking}
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="customer_date_booking" name="customer_date_booking" value="{DATA.customer_date_booking}" placeholder="{LANG.appointment_customer_date_booking}" type="text" maxlength="10" readonly style="display:inline-block;width:150px;background:#fff">
						<!-- BEGIN: error_customer_date_booking -->
						<div class="text-danger">
							{error_customer_date_booking}
						</div>
						<!-- END: error_customer_date_booking -->
					</div>
				</div>

				<div class="form-group required">
					<label class="control-label col-sm-4" for="customer_time_set">
						{LANG.appointment_customer_time_set}
					</label> 
					<div id="boxsettime" class=" col-sm-20">
						<input class="form-control" data-toggle="popover" id="customer_time_set" name="customer_time_set" value="{DATA.customer_time_set}" placeholder="{LANG.appointment_customer_time_set_help}" type="text" readonly maxlength="5" style="display:inline-block;width:150px;background:#fff">
						<!-- BEGIN: error_customer_time_set -->
						<div class="text-danger">
							{error_customer_time_set}
						</div>
						<!-- END: error_customer_time_set -->
					</div>
				</div>

				<div class="form-group required">
					<label class="control-label col-sm-4" for="service_id">
						{LANG.appointment_service}
					</label> 
					<div class=" col-sm-20">
						<!-- BEGIN: service -->
						<label style="margin-bottom: 0; padding-top: 7px;margin-left: 10px">
							<input type="checkbox" name="service_id[]" value="{SERVICE.key}" {SERVICE.checked} > 
							{SERVICE.name}
						</label>
						<!-- END: service -->
						<div class="clearfix"></div>
						<!-- BEGIN: error_service_id -->
						<div class="text-danger">
							{error_service_id}
						</div>
						<!-- END: error_service_id -->
					</div>
				</div>				 
				<div class="form-group required">
					<label class="control-label col-sm-4" for="service_id">
						{LANG.appointment_branch}
					</label> 
					<div class=" col-sm-10">
						<select id="branch" class="form-control">
							<!-- BEGIN: branch -->
							<option value="{BRANCH.key}" {BRANCH.selected}>
								{BRANCH.name}
							</option>
							<!-- END: branch -->
						</select>
						<div class="clearfix"></div>
						<!-- BEGIN: error_branch_id -->
						<div class="text-danger">
							{error_branch_id}
						</div>
						<!-- END: error_branch_id -->
					</div>
				</div>				 
				<div class="form-group required">
					<label class="control-label col-sm-4" for="service_id">
						{LANG.appointment_doctors}
					</label> 
					<div class=" col-sm-10">
						<select name="doctors_id" id="doctors" class="form-control" >
							<!-- BEGIN: doctors -->
							<option value="{DOCTORS.userid}" selected="selected">{DOCTORS.full_name} - {DOCTORS.username}
							</option>
							<!-- END: doctors -->	
						</select>
						<div class="clearfix"></div>
						<!-- BEGIN: error_doctors_id -->
						<div class="text-danger">
							{error_doctors_id}
						</div>
						<!-- END: error_doctors_id -->
					</div>
				</div>				 
				<div class="form-group">
					<label class="control-label col-sm-4" for="customer_message">
						{LANG.appointment_customer_message}
					</label> 
					<div class=" col-sm-20">
						<textarea class="form-control" id="customer_message" name="customer_message" placeholder="{LANG.appointment_customer_message}" rows="8">{DATA.customer_message}</textarea>
						<!-- BEGIN: error_customer_message -->
						<div class="text-danger">
							{error_customer_message}
						</div>
						<!-- END: error_customer_message -->
					</div>
				</div>
				<div>
					<p>
						Gửi email cho bệnh nhân
						<input type="checkbox" id="send_mail_patient" name="send_mail_patient" value="10">
					</p>
				</div>
				<div>
					<p>
						Gửi email cho bác sĩ
						<input type="checkbox" id="send_mail_doctor" name="send_mail_doctor" value="10">
					</p>
				</div>
				<div align="center" style="padding:10px">
					<input type="hidden" name="appointment_id" value="{DATA.appointment_id}"/>
					<input type="hidden" name="branch_id" id="branch_id" value="{DATA.branch_id}" >
					<input type="hidden" name="action" value="add"/>
					<input type="hidden" name="save" value="1"/>
					<input type="hidden" name="token" value="{DATA.token}"/>
					<button class="btn btn-primary" id="submitform" type="submit"><i class="fa fa-spinner fa-spin" style="display:none;font-size:14px"></i> {BUTTON_SUBMIT} </button>
					
				</div>
			</form>      

		</div>
	</div>
</div>
<div id="settime" style="display:none">
	<ul class="timing-list">
		<!-- BEGIN: time -->
		<li>{TIME}</li>
		<!-- END: time -->
	</ul>
</div>

<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>


<script type="text/javascript">

	$(document).ready(function() {

		$('.select2').select2({language: '{NV_LANG_INTERFACE}'})


		$("#branch").select2({  
			multiple: true,        
			maximumSelectionLength: 1,
			placeholder: '{LANG.appointment_branch_select}',
			ajax: {                
				url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&action=getBranch&second=' + new Date().getTime(),
				type: "post",      
				dataType: 'json',  
				delay: 250,        
				data: function (params) {
					return {       
						q: params.term  
					};             
				},                 
				beforeSend: function ( ) {
					$("#branch_id").val();
					$("#doctors").select2("val", "");
					$(".select2-selection__choice__remove").trigger('click');
					

					
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
		}).on('select2:select', function (e) {

			$("#branch_id").val(e.params.data.id);
		});
		
		
		$("#doctors").select2({  
			multiple: true,        
			maximumSelectionLength: 1,
			placeholder: '{LANG.appointment_doctors_select}',
			ajax: {                
				url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&action=getDoctors&second=' + new Date().getTime(),
				type: "post",      
				dataType: 'json',  
				delay: 250,        
				data: function (params) {
					
					var appointment_id = $('input[name=appointment_id]').val();
					
					var query = {
						search : params.term,
						appointment_id : appointment_id,
						branch_id : $('#branch_id').val(),
						booking_date:  $('#customer_date_booking').val(),
						booking_hour:  $('#customer_time_set').val()
					}

					
					return query;
				},                 
				processResults: function (response) {
				
					console.log(response);
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


		$('#customer_date_booking').datepicker({
			dateFormat: 'dd/mm/yy',
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true,
		});

		$('#customer_time_set').popover({
			content: $('#settime').html(),
			html: true,
			trigger: 'focus',
			placement: 'top'
		})

		$('#customer_time_set').on('click', function(){	
			if( $.trim($(this).val()) != '' )
				$("#boxsettime .timing-list>li:contains('"+$.trim($(this).val())+"')").addClass('timing-list-active');
		})
		$(document).on('click', '.timing-list>li', function(){
			$('#customer_time_set').val( $(this).text() );
		})

	});

	$('#form-appointment').on('submit', function() {
		$('#submitform,button[type="submit"]').prop('disabled', true);
		$('#submitform .fa-spinner').show();
	});


</script>
<!-- END: main -->