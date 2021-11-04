<!-- BEGIN: main -->
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />

<div id="patient-content">
	<!-- BEGIN: error -->
	<div class="alert alert-warning">{ERROR}</div>
	<!-- END: error -->
	<!-- BEGIN: success -->
	<div class="alert alert-success">
		<i class="fa fa-check-circle"></i> 
		{SUCCESS}
		<i class="fa fa-times"></i>
	</div>
	<!-- END: success -->
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				<i class="fa fa-list"></i>
				{LANG.patient_list}
			</h3> 
			<div class="pull-right">
				<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" enctype="multipart/form-data" name="readexcel" id="readexcel" method="post" style="display: inline-block;">
					<input type="hidden" name="{NV_LANG_VARIABLE}"  value="{NV_LANG_DATA}" />
					<input type="hidden" name="{NV_NAME_VARIABLE}"  value="{MODULE_NAME}" />
					<input type="hidden" name="{NV_OP_VARIABLE}"  value="{OP}" />
					<label for="fname">
						File Excel
					</label>
					<p>
						<input type="file" name="excel" id="excel" required />
					</p>
					<input type="submit" value="THÊM NHIỀU" id="btsend" name="import" class="btn btn-primary" />

				</form>
				<a href="{ADD_NEW}" data-toggle="tooltip" data-placement="top" title="{LANG.add_new}" class="btn btn-success btn-sm">
					<i class="fa fa-plus"></i>
				</a>
				<button type="button" data-toggle="tooltip" data-placement="top" title="{LANG.delete}" class="btn btn-danger btn-sm" id="button-delete">
					<i class="fa fa-trash-o"></i>
				</button>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<div class="well">
				<div class="row">
					<form  action="{NV_BASE_ADMINURL}index.php" method="get" id="formsearch">
						<input type="hidden" name ="{NV_NAME_VARIABLE}"value="{MODULE_NAME}" />
						<input type="hidden" name ="{NV_OP_VARIABLE}"value="{OP}" />
						
						
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label" for="input-full_name">
									{LANG.grouppatient} 
								</label>
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
							</div>
						</div>
						
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label" for="input-patient_code">
									{LANG.patient_code}
								</label>
								<input type="text" name="patient_code" value="{DATA.patient_code}" placeholder="{LANG.patient_patient_code}" id="input-patient_code" class="form-control" autocomplete="off">								 
							</div>
						</div>
						
						
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label" for="input-full_name">
									{LANG.patient_full_name}
								</label>
								<input type="text" name="full_name" value="{DATA.full_name}" placeholder="{LANG.patient_full_name}" id="input-full_name" class="form-control" autocomplete="off">								 
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label" for="input-phone">
									{LANG.patient_phone}
								</label>
								<input type="text" name="phone" value="{DATA.phone}" placeholder="{LANG.patient_phone}" id="input-phone" class="form-control" autocomplete="off">
							</div>
						</div>

						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label clear" for="input-date_added">
									{LANG.patient_date_added}
								</label>
								<div class="clear"></div>
								<input type="text" name="date_from" value="{DATA.date_from}" id="date_from"  placeholder="{LANG.patient_date_from}" class="form-control" autocomplete="off" style="display:inline-block;width:100px"> 
								<strong>:</strong>
								<input type="text" name="date_to" value="{DATA.date_to}" id="date_to" placeholder="{LANG.patient_date_to}" class="form-control" autocomplete="off" style="display:inline-block;width:100px">
							</div> 
						</div> 

						<div class="col-sm-24">
							<div class="">
								<input type="hidden" value="{TOKEN}" name="token"/>
								<button id="submitform" type="submit" class="btn btn-primary" > 
									<i class="fa fa-search"></i> 
									Tìm kiếm 
								</button>
								<button class="btn btn-info export_file" data-all="0" >
									<i class="fa fa-download"></i> 
									Xuất danh sách tìm kiếm
								</button> 
								<button class="btn btn-info export_file" data-all="1"> 
									<i class="fa fa-download"></i>
									Xuất tất cả 
								</button>
							</div> 
						</div> 
					</form>

				</div>
			</div>
			<form action="#" method="post" enctype="multipart/form-data" id="form-patient">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th class="text-center">
									<input name="check_all[]" type="checkbox" value="yes" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" >
								</th>
								<th class="text-center">
									<strong>
										{LANG.patient_code}
									</strong>
								</th>
								
								<th class="text-center">
									<strong>
										Xưng hô
									</strong>
								</th>
								<th class="text-center">
									<a {FULL_NAME_ORDER} href="{URL_FULL_NAME}">
										<strong>
											{LANG.patient_full_name}
										</strong>
									</a> 
								</th>
								<th class="text-center">
									<a href="#">
										<strong>
											{LANG.grouppatient}
										</strong>
									</a> 
								</th>
								<th class="text-center">
									<a {USERNAME_ORDER} href="{URL_USERNAME}">
										<strong>
											{LANG.patient_phone}
										</strong>
									</a> 
								</th>
						
								
								<th class="text-center">
									<strong>
										Khám còn lại
									</strong>
								</th>

								<th class="text-center">
									<strong>
										{LANG.action} 
									</strong>
								</th>
							</tr>
						</thead>
						<tbody>
							<!-- BEGIN: loop --> 
							<tr id="group_{LOOP.userid}">
								<td class="text-center">
									<input type="checkbox" name="selected[]" value="{LOOP.userid}" >
								</td>
								<td class="text-center">
									{LOOP.patient_code} 
								</td>
								<td class="text-left">
									{LOOP.confess}
								</td>

								<td class="text-left">
									<a href="{LOOP.url_appointment}"  title="Đặt lịch hẹn thay khách">
										{LOOP.full_name}
									</a>
								</td>

								<td class="text-center">
									{LOOP.patient_group_name} 
								</td>
								<td class="text-center">
									{LOOP.username} 
								</td>

								
								<td class="text-center">
									{LOOP.kham_conlai}
								</td>

								
								<td class="text-center">
									<a href="{LOOP.url_appointment}" data-toggle="tooltip" title="Đặt lịch hẹn thay khách" class="btn btn-info  btn-sm">
										ĐẶT LỊCH HẸN
									</a>
									
									<a href="{LOOP.url_by_service}" data-toggle="tooltip" title="Gói dịch vụ" class="btn btn-info  btn-sm">
										Mua gói dịch vụ
									</a>
									
									<a href="{LOOP.view}" data-toggle="tooltip" title="Xem chi tiết khách hàng" class="btn btn-info  btn-sm">
										<i class="fa fa-eye" aria-hidden="true"></i>
									</a>
									<a href="{LOOP.edit}" data-toggle="tooltip" title="Cập nhật thông tin khách hàng" class="btn btn-primary  btn-sm">
										<i class="fa fa-pencil"></i>
									</a>
									<a href="javascript:void(0);" onclick="delete_patient('{LOOP.userid}', '{LOOP.token}')" data-toggle="tooltip" title="{LANG.delete}" class="btn btn-danger  btn-sm">
										<i class="fa fa-trash-o"></i>
									</td>
								</tr>
								<!-- END: loop -->
							</tbody>
						</table>
					</div>
				</form>
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
	<script type="text/javascript">
		function send_email(email){
			$.ajax({
				type : 'POST',
				url : script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=patient&mod=send_email',
				data: {email:email},
				success : function(res){
					res=JSON.parse(res);
					if(res.status == "OK"){
						alert("Gửi mail cho bệnh nhân thành công!")
					}else{
						alert("Có lỗi xảy ra!")
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	</script>
	<div class="modal fade" id="ModalAddList" role="dialog">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">

				</div>

			</div>  
		</div>
	</div>
	<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
	<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>


	<script type="text/javascript">
		$('#formsearch').on('submit', function(){
			$('#submitform').prop('disabled', true);
			$('#submitform i').replaceWith('<i class="fa fa-spinner fa-spin"></i>'); 
		})

		$("#date_from,#date_to").datepicker({
			showOn : "both",
			dateFormat : "dd/mm/yy",
			changeMonth : true,
			changeYear : true,
			showOtherMonths : true,
			buttonImage : nv_base_siteurl + "assets/images/calendar.gif",
			buttonImageOnly : true
		}); 


		function delete_patient(userid, token) {
			if(confirm('{LANG.confirm}')) {
				$.ajax({
					url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=patient&action=delete&nocache=' + new Date().getTime(),
					type: 'post',
					dataType: 'json',
					data: 'userid=' + userid + '&token=' + token,
					beforeSend: function() {
						$('#button-delete i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
						$('#button-delete').prop('disabled', true);
					},	
					complete: function() {
						$('#button-delete i').replaceWith('<i class="fa fa-trash-o"></i>');
						$('#button-delete').prop('disabled', false);
					},
					success: function(json) {
						$('.alert').remove();

						if (json['error']) {
							$('#patient-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
						}

						if (json['success']) {
							$('#patient-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
							$.each(json['id'], function(i, id) {
								$('#group_' + id ).remove();
							});
						}		
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		}

		$('#button-delete').on('click', function() {
			if(confirm('{LANG.confirm}')) 
			{
				var listid = [];
				$("input[name=\"selected[]\"]:checked").each(function() {
					listid.push($(this).val());
				});
				if (listid.length < 1) {
					alert("{LANG.please_select_one}");
					return false;
				}

				$.ajax({
					url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=patient&action=delete&nocache=' + new Date().getTime(),
					type: 'post',
					dataType: 'json',
					data: 'listid=' + listid + '&token={TOKEN}',
					beforeSend: function() {
						$('#button-delete i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
						$('#button-delete').prop('disabled', true);
					},	
					complete: function() {
						$('#button-delete i').replaceWith('<i class="fa fa-trash-o"></i>');
						$('#button-delete').prop('disabled', false);
					},
					success: function(json) {
						$('.alert').remove();

						if (json['error']) {
							$('#patient-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
						}

						if (json['success']) {
							$('#patient-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
							$.each(json['id'], function(i, id) {
								$('#group_' + id ).remove();
							});
						}		
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}	
		});

		$('.ajaxchange').on('change', function() {
			var action = $(this).attr('data-action');
			var token = $(this).attr('data-token');
			var userid = $(this).attr('data-userid');
			var new_vid = ( $(this).prop('checked') === true ) ? 1 : 0;
			var id = $(this).attr('id');
			$.ajax({
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=patient&nocache=' + new Date().getTime(),
				type: 'post',
				dataType: 'json',
				data: 'action=' + action + '&userid=' + userid + '&new_vid=' + new_vid + '&token='+token,
				beforeSend: function() {
					$('#'+id ).prop('disabled', true);
					$('.alert').remove();
				},	
				complete: function() {
					$('#'+id ).prop('disabled', false);
				},
				success: function(json) {



					if ( json['error'] ) {
						alert( json['error'] );
						( new_vid ) ? $(this).prop('checked', true) : $(this).prop('checked', false);
					};

					if ( json['link'] ) location.href = json['link'];

				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		});

		$('.formajax').on('change', function() {
			var action = $(this).attr('data-action');
			var token = $(this).attr('data-token');
			var userid = $(this).attr('data-id');
			var new_vid = $(this).val();
			var id = $(this).attr('id');
			$.ajax({
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=patient&nocache=' + new Date().getTime(),
				type: 'post',
				dataType: 'json',
				data: 'action=' + action + '&userid=' + userid + '&new_vid=' + new_vid + '&token='+token,
				beforeSend: function() {
					$('#'+id ).prop('disabled', true);
					$('.alert').remove();
				},	
				complete: function() {
					$('#'+id ).prop('disabled', false);
				},
				success: function(json) {

					if ( json['error'] ) alert( json['error'] );	
					if ( json['link'] ) location.href = json['link'];

				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		});

		$('.export_file').on('click', function(e) {
			var all = $(this).attr('data-all');	
			var form_data = $('#formsearch').serializeArray();
			form_data.push({ name: 'all', value: all });
			
			$.ajax({
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=export&action=patient&nocache=' + new Date().getTime(),
				type: 'post',
				dataType: 'json',
				data: form_data,
				beforeSend: function() {
					$('.export_file i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
					$('.export_file').prop('disabled', true);
				},	
				complete: function() {
					$('.export_file i').replaceWith('<i class="fa fa-download"></i>');
					$('.export_file').prop('disabled', false);
				},
				success: function(json) {
					if( json['error'] ) alert( json['error'] );  		
					if( json['link'] ) window.location.href= json['link'];  		
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
			e.preventDefault(); 	
		});

	</script>
<!-- END: main -->