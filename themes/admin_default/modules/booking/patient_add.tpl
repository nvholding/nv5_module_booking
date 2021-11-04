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
					<label class="control-label col-sm-4">
						Xưng hô
					</label> 
					<div class=" col-sm-20">
						<select class="confess form-control" name="confess">
							<option value="Quý khách" {SELECTED1}>
								Quý khách
							</option>
							<option value="Cô" {SELECTED2}>
								Cô
							</option>
							<option value="Chú" {SELECTED3}>
								Chú
							</option>
							<option value="Anh" {SELECTED4}>
								Anh
							</option>
							<option value="Chị" {SELECTED5}>
								Chị
							</option>
						</select>
					</div>
				</div>

				<div class="form-group required">
					<label class="control-label col-sm-4" for="full_name">
						{LANG.patient_full_name}
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="full_name" name="full_name" value="{DATA.full_name}" placeholder="{LANG.patient_full_name}" type="text" autocomplete="off" required="required">
						<!-- BEGIN: error_full_name -->
						<div class="text-danger">
							{error_full_name}
						</div>
						<!-- END: error_full_name -->
					</div>
				</div>
				
				<div class="form-group required">
					<label class="control-label col-sm-4" for="patient_code">
						{LANG.patient_code}
					</label> 
					<div class=" col-sm-20">
						<input readonly="true" class="form-control" id="patient_code" name="patient_code" value="{DATA.patient_code}" placeholder="{LANG.patient_code}" type="text" autocomplete="off" required="required">
						<!-- BEGIN: error_patient_code -->
						<div class="text-danger">
							{error_patient_code}
						</div>
						<!-- END: error_patient_code -->
					</div>
				</div>

				<div class="form-group required">
					<label class="control-label col-sm-4" for="phone">
						{LANG.patient_phone}
					</label> 
					<div class=" col-sm-20">
						<input maxlength="10" class="form-control" id="phone" name="phone" value="{DATA.username}" placeholder="{LANG.patient_phone}" type="text" autocomplete="off" required="required">
						<!-- BEGIN: error_phone -->
						<div class="text-danger">
							{error_phone}
						</div>
						<!-- END: error_phone -->
					</div>
				</div>

				<div class="form-group required">
					<label class="control-label col-sm-4" for="email">
						{LANG.patient_email}
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="email" name="email" value="{DATA.email}" placeholder="{LANG.patient_email}" type="text" autocomplete="off" required="required">
						<!-- BEGIN: error_email -->
						<div class="text-danger">
							{error_email}
						</div>
						<!-- END: error_email -->
					</div>
				</div>


				<div class="form-group">
					<label class="control-label col-sm-4" for="address">
						{LANG.patient_address}
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="address" name="address" value="{DATA.address}" placeholder="{LANG.patient_address}" type="text" autocomplete="off" required="required">
						<!-- BEGIN: error_address -->
						<div class="text-danger">
							{error_address}
						</div>
						<!-- END: error_address -->
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4" for="address">
						Liên hệ khác
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="other_contact" name="other_contact" value="{DATA.other_contact}" placeholder="Liên hệ khác" type="text" autocomplete="off">
						
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4">
						Công việc
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="work" name="work" value="{DATA.work}" placeholder="Công việc" type="text" autocomplete="off">
						
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4">
						Bệnh sử
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="history" name="history" value="{DATA.history}" placeholder="Bệnh sử" type="text" autocomplete="off">
						
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-4">
						Kết quả điều trị
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="patient_result" name="patient_result" value="{DATA.patient_result}" placeholder="Kết quả điều trị" type="text" autocomplete="off">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4">
						Nguyện vọng điều trị
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="expect" name="expect" value="{DATA.expect}" placeholder="Nguyện vọng điều trị" type="text" autocomplete="off">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4">
						Ghi chú
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="note" name="note" value="{DATA.note}" placeholder="Ghi chú" type="text" autocomplete="off">
					</div>
				</div>

				<div class="form-group required">
					<label class="control-label col-sm-4" for="gender">
						{LANG.patient_gender}
					</label> 
					<div class=" col-sm-20">

						<select name="gender" class="form-control btn-sm" required="required">
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
					<label class="control-label col-sm-4" for="service_package_id">
						{LANG.select_group_patient}
					</label> 
					<div class=" col-sm-20">

						<select name="patient_group" class="form-control btn-sm">
							<option value="" >
								{LANG.select_group_patient}
							</option>
							<!-- BEGIN: patient_group -->
							<option value="{PATIENT_GROUP.key}" {PATIENT_GROUP.selected} > 
								{PATIENT_GROUP.title}
							</option>
							<!-- END: patient_group -->
						</select>

						<div class="clearfix"></div>
					</div>
				</div>
				
				<div class="form-group required">
					<label class="control-label col-sm-4" for="service_package_id">
						{LANG.appointment_branch_select}
					</label> 
					<div class=" col-sm-20">

						<select name="branch" class="form-control btn-sm">
							<option value="0" >
								{LANG.appointment_branch_select}
							</option>
							<!-- BEGIN: branch -->
							<option value="{branch.key}" {branch.selected} > 
								{branch.title}
							</option>
							<!-- END: branch -->
						</select>

						<div class="clearfix"></div>
					</div>
				</div>

				<!-- BEGIN: userlog -->
				<!-- <div class="form-group" style="margin-bottom:0">
					<div class="col-sm-4">&nbsp;</div>
					<div class="col-sm-20">	
						<h3>
							<strong>
								Để trống nếu không muốn cập nhật lại mật khẩu
							</strong>
						</h3>
					</div>
				</div> -->
				<!-- END: userlog -->
				<div class="form-group required hidden">
					<label class="control-label col-sm-4" for="address">
						{LANG.patient_password}
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="password1" name="password1" value="{DATA.password1}" placeholder="{LANG.patient_password}" type="password" autocomplete="off">
						<!-- BEGIN: error_password -->
						<div class="text-danger">
							{error_password}
						</div>
						<!-- END: error_password -->
					</div>
				</div>
				<div class="form-group required hidden">
					<label class="control-label col-sm-4" for="address">
						{LANG.patient_repassword}
					</label> 
					<div class=" col-sm-20">
						<input class="form-control" id="password2" name="password2" value="{DATA.password2}" placeholder="{LANG.patient_repassword}" autocomplete="off" type="password">
						<!-- BEGIN: error_password2 -->
						<div class="text-danger">
							{error_password2}
						</div>
						<!-- END: error_password2 -->
					</div>
				</div>


				<div align="center" style="padding:10px">
					<input type="hidden" name="userid" value="{DATA.userid}"/>
					<input type="hidden" name="action" value="add"/>
					<input type="hidden" name="save" value="1"/>
					<input type="hidden" name="token" value="{DATA.token}"/>
					<!-- BEGIN: admin -->
					<input type="hidden" name="check_admin" value="1"/>
					<button class="btn btn-primary" id="submitform" type="submit">
						<i class="fa fa-spinner fa-spin" style="display:none;font-size:14px"></i> 
						{BUTTON_SUBMIT}
					</button>
					<!-- END: admin -->
					<!-- BEGIN: no_admin -->
					<input type="hidden" name="check_admin" value="0"/>
					<button class="btn btn-primary" id="submitform" type="submit">
						<i class="fa fa-spinner fa-spin" style="display:none;font-size:14px"></i> 
						Thêm Mới Khách Hàng
					</button>
					<!-- END: no_admin -->
					
				</div>
			</form>      
			<!-- BEGIN: list_require -->
			<div class="container-fluid">
				<h2>
					Danh sách yêu cầu thay đổi
				</h2>
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th class="text-center">
									<strong>
										Thời gian yêu cầu
									</strong>
								</th>
								
								<th class="text-center">
									<strong>
										{LANG.patient_full_name}
									</strong> 
								</th>
								<th class="text-center">
									<strong>
										{LANG.patient_phone}
									</strong>
								</th>
								<th class="text-center">
									<strong>
										{LANG.patient_email}
									</strong>
								</th>
								<th class="text-center">
									<strong>
										{LANG.patient_address}
									</strong>
								</th>
								<th class="text-center">
									<strong>
										Liên hệ khác
									</strong>
								</th>
								<th class="text-center">
									<strong>
										Công việc
									</strong>
								</th>
								
								
								
								<th class="text-center">
									<strong>
										Giới tính
									</strong>
								</th>
								<th class="text-center">
									<strong>
										Ngày sinh
									</strong>
								</th>
								
							
								<th class="text-center">
									<strong>
										Thao tác
									</strong>
								</th>
							</tr>
						</thead>
						<tbody>
							<!-- BEGIN: loop --> 
							<tr id="group_{LOOP.userid}">
								<td class="text-center">
									{LOOP.time_require}
								</td>
								
								
								<td class="text-center">
									{LOOP.full_name}
								</td>
								<td class="text-center">
									{LOOP.phone}
								</td>
								<td class="text-center">
									{LOOP.email}
								</td>
								<td class="text-center">
									{LOOP.address}
								</td>
								<td class="text-center">
									{LOOP.other_contact}
								</td>
								<td class="text-center">
									{LOOP.work}
								</td>
								
								
								<td class="text-center">
									{LOOP.gender}
								</td>
								<td class="text-center">
									{LOOP.birthday}
								</td>
								
								<td class="text-center">
									<!-- BEGIN: using -->
									<div>
										Đang sử dụng
									</div>
									<!-- END: using -->
									<!-- BEGIN: no_using -->
									<button type="button" class="btn btn-success" onclick="accept({LOOP.id})">
										Duyệt
									</button>
									<button type="button" class="btn btn-danger" onclick="no_accept({LOOP.id})">
										Từ chối
									</button>
									<!-- END: no_using -->
								</td>
							</tr>
							<!-- END: loop -->
						</tbody>
					</table>
				</div>
			</div>
			<!-- END: list_require -->
		</div>
	</div>
</div>


<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>


<script type="text/javascript">

	function accept(id){
		$.ajax({
			type : 'POST',
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '={OP}&mod=accept&id_edit=' + id,
			success : function(res){
				res2=JSON.parse(res);
				if(res2.status=="OK"){
					alert("Thay đổi thông tin thành công!")
					location.reload();
				}else{
					
					
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
	
	function no_accept(id){
		$.ajax({
			type : 'POST',
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '={OP}&mod=no_accept&id_edit=' + id,
			success : function(res){
				res2=JSON.parse(res);
				if(res2.status=="OK"){
					alert("Thay đổi thông tin thành công!")
					location.reload();
				}else{
					
					
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
	
	
	$(document).ready(function() {

		$('#birthday').datepicker({
			showOn : "both",
			dateFormat: 'dd/mm/yy',
			changeMonth: true,
			yearRange: "-90:+0",
			changeYear: true,
			showOtherMonths: true,
			buttonImage : nv_base_siteurl + "assets/images/calendar.gif",
			buttonImageOnly : true
		});


	});

	$('#form-doctors').on('submit', function() {
		$('#submitform,button[type="submit"]').prop('disabled', true);
		$('#submitform .fa-spinner').show();
	});


</script>
<!-- END: main -->