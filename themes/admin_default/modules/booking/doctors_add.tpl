<!-- BEGIN: main -->
<div id="doctors-content">
	<!-- BEGIN: error_warning -->
	<div class="alert alert-danger">
		<i class="fa fa-exclamation-circle"></i> 
		{error_warning}
		<i class="fa fa-times"></i> 
	</div>
	<!-- END: error_warning -->
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left">
				<i class="fa fa-pencil"></i> 
				{CAPTION}
			</h3>
			<div class="pull-right">
				<button type="submit" form="form-doctors" data-toggle="tooltip" class="btn btn-primary" title="{LANG.save}">
					<i class="fa fa-save"></i>
				</button> 
				<a href="{CANCEL}" data-toggle="tooltip" class="btn btn-default" title="{LANG.cancel}">
					<i class="fa fa-reply"></i>
				</a>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="" method="post"  enctype="multipart/form-data" id="form-doctors" class="form-horizontal">

				<div class="form-group required">
					<label class="control-label col-sm-4" for="full_name">
						{LANG.doctors_full_name}
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="full_name" name="full_name" value="{DATA.full_name}" placeholder="{LANG.doctors_full_name}" type="text">
						<!-- BEGIN: error_full_name -->
						<div class="text-danger">
							{error_full_name}
						</div>
						<!-- END: error_full_name -->
					</div>
				</div>
				<div class="form-group required">
					<label class="control-label col-sm-4" for="phone">
						Tài khoản/Điện thoại
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="phone" name="phone" value="{DATA.username}" placeholder="{LANG.doctors_phone}" type="text" >
						<!-- BEGIN: error_phone -->
						<div class="text-danger">
							{error_phone}
						</div>
						<!-- END: error_phone -->
					</div>
				</div>

				<div class="form-group required">
					<label class="control-label col-sm-4" for="email">
						{LANG.doctors_email}
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="email" name="email" value="{DATA.email}" placeholder="{LANG.doctors_email}" type="text">
						<!-- BEGIN: error_email -->
						<div class="text-danger">
							{error_email}
						</div>
						<!-- END: error_email -->
					</div>
				</div>
				<div class="form-group required">
					<label class="control-label col-sm-4" for="gender">
						{LANG.patient_gender}
					</label> 
					<div class=" col-sm-20">

						<select name="gender" class="form-control btn-sm">
							<option value="" > 
								{LANG.patient_gender}
							</option>
							<!-- BEGIN: gender -->
							<option value="{GENDER.key}" {GENDER.selected} > 
								{GENDER.name}
							</option>
							<!-- END: gender -->
						</select>

						<div class="clearfix">

						</div>
						<!-- BEGIN: error_gender -->
						<div class="text-danger">
							{error_gender}
						</div>
						<!-- END: error_gender -->
					</div>
				</div>
				<div class="form-group required">
					<label class="control-label col-sm-4" for="birthday">
						{LANG.patient_birthday}
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="birthday" name="birthday" value="{DATA.birthday}" placeholder="{LANG.patient_birthday_placeholder}" type="text" autocomplete="off" autocomplete="off" style="display:inline-block;width:100px">
						<!-- BEGIN: error_birthday -->
						<div class="text-danger">
							{error_birthday}
						</div>
						<!-- END: error_birthday -->
					</div>
				</div>
				<div class="form-group required">
					<label class="control-label col-sm-4" for="address">
						{LANG.doctors_address}
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="address" name="address" value="{DATA.address}" placeholder="{LANG.doctors_address}" type="text">
						<!-- BEGIN: error_address -->
						<div class="text-danger">
							{error_address}
						</div>
						<!-- END: error_address -->
					</div>
				</div>

				<div class="form-group required">
					<label class="control-label col-sm-4" for="branch_id">
						{LANG.doctors_branch}
					</label> 
					<div class=" col-sm-20">

						<select name="branch_id" class="form-control btn-sm">
							<option value="" > 
								{LANG.doctors_branch_select}
							</option>
							<!-- BEGIN: branch -->
							<option value="{BRANCH.key}" {BRANCH.selected} >
								{BRANCH.name}
							</option>
							<!-- END: branch -->
						</select>

						<div class="clearfix"></div>
						<!-- BEGIN: error_branch -->
						<div class="text-danger">
							{error_branch}
						</div>
						<!-- END: error_branch -->
					</div>
				</div>	

				<div class="form-group required">
					<label class="control-label col-sm-4">
						{LANG.time_from}
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="time_from" name="time_from" value="{DATA.time_from}" placeholder="{LANG.time_from}" type="text" autocomplete="off" autocomplete="off" style="display:inline-block;width:100px">
						<!-- BEGIN: error_time_from -->
						<div class="text-danger">
							{error_time_from}
						</div>
						<!-- END: error_time_from -->
					</div>
				</div>
				
				<div class="form-group required">
					<label class="control-label col-sm-4">
						{LANG.time_to}
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="time_to" name="time_to" value="{DATA.time_to}" placeholder="{LANG.time_to}" type="text" autocomplete="off" autocomplete="off" style="display:inline-block;width:100px">
						<!-- BEGIN: error_time_to -->
						<div class="text-danger">
							{error_time_to}
						</div>
						<!-- END: error_time_to -->
					</div>
				</div>

				<div class="form-group required">
					<label class="control-label col-sm-4" for="active">
						{LANG.doctors_active}
					</label> 
					<div class=" col-sm-20">

						<select name="active" class="form-control btn-sm">
							<!-- BEGIN: active -->
							<option value="{ACTIVE.key}" {ACTIVE.selected} > 
								{ACTIVE.name}
							</option>
							<!-- END: active -->
						</select>

						<div class="clearfix"></div>
						<!-- BEGIN: error_active -->
						<div class="text-danger">
							{error_active}
						</div>
						<!-- END: error_active -->
					</div>
				</div>				 


				<div align="center" style="padding:10px">
					<input type="hidden" name="userid" value="{DATA.userid}"/>
					<input type="hidden" name="action" value="add"/>
					<input type="hidden" name="save" value="1"/>
					<input type="hidden" name="token" value="{DATA.token}"/>
					<button class="btn btn-primary" id="submitform" type="submit">
						<i class="fa fa-spinner fa-spin" style="display:none;font-size:14px"></i> 
						{BUTTON_SUBMIT} 
					</button>
					
				</div>
			</form>      

		</div>
	</div>
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

		$('#date_booking').datepicker({
			dateFormat: 'dd/mm/yy',
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true,
		});

		$('#time_set').popover({
			content: $('#settime').html(),
			html: true,
			trigger: 'focus',
			placement: 'top'
		})

		$('#time_set').on('click', function(){	
			if( $.trim($(this).val()) != '' )
				$("#boxsettime .timing-list>li:contains('"+$.trim($(this).val())+"')").addClass('timing-list-active');
		})
		$(document).on('click', '.timing-list>li', function(){
			$('#time_set').val( $(this).text() );
		})

	});

	$('#form-doctors').on('submit', function() {
		$('#submitform,button[type="submit"]').prop('disabled', true);
		$('#submitform .fa-spinner').show();
	});
	$('#birthday').datepicker({
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		changeYear: true,
		showOtherMonths: true,
	});
	
	$('#time_from').datepicker({
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		changeYear: true,
		showOtherMonths: true,
	});
	
	$('#time_to').datepicker({
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		changeYear: true,
		showOtherMonths: true,
	});

</script>
<!-- END: main -->