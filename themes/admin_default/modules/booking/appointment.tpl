<!-- BEGIN: main -->
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />

<div id="appointment-content">
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
				{LANG.appointment_list}
			</h3> 
			<div class="pull-right">
				<a href="{ADD_NEW}" data-toggle="tooltip" data-placement="top" title="{LANG.add_new}" class="btn btn-success btn-sm">
					<i class="fa fa-plus"></i>
				</a>
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
								<label class="control-label" for="input-customer_full_name">
									{LANG.appointment_customer_full_name}
								</label>
								<input type="text" name="customer_full_name" value="{DATA.customer_full_name}" placeholder="{LANG.appointment_customer_full_name}" id="input-customer_full_name" class="form-control" autocomplete="off">								 
							</div>
						</div>

						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label" for="input-customer_phone">
									{LANG.appointment_customer_phone}
								</label>
								<input type="text" name="customer_phone" value="{DATA.customer_phone}" placeholder="{LANG.appointment_customer_phone}" id="input-customer_phone" class="form-control" autocomplete="off">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label clear" for="input-service">
									{LANG.appointment_branch}
								</label>
								<select  name="branch_id" class="form-control">
									<option value="0"> 
										{LANG.appointment_branch_select} 
									</option>
									<!-- BEGIN: branch -->
									<option value="{BRANCH.key}" {BRANCH.selected}>
										{BRANCH.name} 
									</option>
									<!-- END: branch -->
								</select>
							</div> 
						</div>
						<div class="col-sm-10">
							<div class="form-group">
								<label class="control-label clear" for="input-date_added">
									{LANG.appointment_customer_date_booking}
								</label>
								<div class="clear">
									
								</div>
								<input type="text" name="date_from" value="{DATA.date_from}" id="date_from"  placeholder="{LANG.appointment_date_from}" class="form-control" autocomplete="off" style="display:inline-block;width:100px">
								<strong>:</strong>
								<input type="text" name="date_to" value="{DATA.date_to}" id="date_to" placeholder="{LANG.appointment_date_to}" class="form-control" autocomplete="off" style="display:inline-block;width:100px">
							</div> 
						</div>

						<div class="col-sm-24">
							<input type="hidden" value="{TOKEN}" name="token"/>
							<button type="submit" class="btn btn-primary" > 
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
					</form>

				</div>
			</div>
			<form action="#" method="post" enctype="multipart/form-data" id="form-appointment">
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<td class="col-sm-0 text-center">
									<input name="check_all[]" type="checkbox" value="yes" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" >
								</td>
								<td class="col-sm-2 text-center">
									<a {CUSTOMER_FULL_NAME_ORDER} href="{URL_CUSTOMER_FULL_NAME}">
										<strong>
											{LANG.appointment_customer_full_name}
										</strong>
									</a> 
								</td>
								<td class="col-sm-2 text-center">
									<strong>
										Thầy Trị liệu
									</strong>
									
								</td>
								<td class="col-sm-2 text-center">
									<a {CUSTOMER_PHONE_ORDER} href="{URL_CUSTOMER_PHONE}">
										<strong>
											{LANG.appointment_customer_phone}
										</strong>
									</a> 
								</td>
								<td class="col-sm-2 text-center">
									<a {CUSTOMER_EMAIL_ORDER} href="{URL_CUSTOMER_EMAIL}"><strong>
										{LANG.appointment_customer_email}
									</strong>
								</a> 
							</td>
							<td class="col-sm-3 text-center">
								<a {CUSTOMER_DATE_BOOKING_ORDER} href="{URL_CUSTOMER_DATE_BOOKING}">
									<strong>{LANG.appointment_customer_date_booking}
									</strong>
								</a> 
							</td>

							<td class="col-sm-3 text-center">
								<a {EMAIL_ORDER} href="{URL_EMAIL}">
									<strong>
										{LANG.appointment_email_status}
									</strong>
								</a> 
							</td>
							<td class="col-sm-3 text-center">
								<a {SMS_ORDER} href="{URL_SMS}">
									<strong>
										{LANG.appointment_sms}
									</strong>
								</a> 
							</td>
							<td class="col-sm-5 text-center"> 
								<strong>
									{LANG.action}
								</strong>
							</td>
						</tr>
					</thead>
					<tbody>
						<!-- BEGIN: loop --> 
						<tr id="group_{LOOP.appointment_id}">
							<td class="text-center">
								<input type="checkbox" name="selected[]" value="{LOOP.appointment_id}" >
							</td>
							<td class="text-left">
								<a href="javascript:void(0);" onclick="show_appointment('{LOOP.appointment_id}', '{LOOP.token}')"  >
									{LOOP.customer_full_name}
								</a>
							</td>
							<td class="text-center">
								{LOOP.info_doctor.last_name} {LOOP.info_doctor.first_name} 
							</td>
							<td class="text-center">
								{LOOP.customer_phone} 
							</td>
							<td class="text-center">
								{LOOP.customer_email} 
							</td>
							<td class="text-center">
								{LOOP.customer_date_booking} 
							</td>

							<td class="text-center">
								<input type="checkbox" class="ajaxchange" {LOOP.is_send_email_checked} data-token="{LOOP.token}" data-action="email" data-appointment_id="{LOOP.appointment_id}" id="email-{LOOP.appointment_id}" value="1" class="form-control">
							</td>
							<td class="text-center">
								<input type="checkbox" class="ajaxchange" {LOOP.is_send_sms_checked} data-token="{LOOP.token}" data-action="sms" data-appointment_id="{LOOP.appointment_id}" id="sms-{LOOP.appointment_id}" value="1" class="form-control">
							</td>
							<td class="text-center">
								<a href="javascript:void(0);" id="send_email{LOOP.appointment_id}" onclick="send_email('{LOOP.customer_email}', '{LOOP.appointment_id}')" class="btn btn-success btn-sm" title="Gửi mail thông báo lịch hẹn">
									<i class="fa fa-envelope" aria-hidden="true"></i>
								</a>
								<a href="javascript:void(0);" id="send_sms{LOOP.appointment_id}" onclick="send_sms('{LOOP.appointment_id}', '{LOOP.token}')" class="btn btn-success btn-sm" title="Gửi tin nhắn thông báo lịch hẹn">
									<i class="fa fa-comments-o" aria-hidden="true"></i>
								</a>
								<a href="{LOOP.edit}" data-toggle="tooltip" title="Cập nhật lịch hẹn" class="btn btn-primary  btn-sm"><i class="fa fa-pencil"></i></a>
								<a href="javascript:void(0);" onclick="delete_appointment('{LOOP.appointment_id}', '{LOOP.token}')" data-toggle="tooltip" title="{LANG.delete}" class="btn btn-danger  btn-sm"><i class="fa fa-trash-o"></i>
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
	$("#date_from,#date_to").datepicker({
		showOn : "both",
		dateFormat : "dd/mm/yy",
		changeMonth : true,
		changeYear : true,
		showOtherMonths : true,
		buttonImage : nv_base_siteurl + "assets/images/calendar.gif",
		buttonImageOnly : true
	}); 

	function send_sms( appointment_id, token ) {
		$.ajax({
			url: nv_base_siteurl +'cronjob/sms.php?nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'action=send_sms&token=' + token + '&appointment_id=' + appointment_id,
			beforeSend: function() {
				$('#group_' + appointment_id).addClass('checking');
				$('#send_sms'+appointment_id+' i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
				$('#send_sms'+appointment_id+'').prop('disabled', true);
			},	
			complete: function() {



			},
			success: function(json) {

			},
			error: function(xhr, ajaxOptions, thrownError) {
			//location.reload();
		}
	});	 
	}
	function send_email(email,id){
		$.ajax({
			type : 'POST',
			url : script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=appointment&mod=send_email',
			data: {email:email, id:id},
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

	function show_appointment(appointment_id, token) {

		$.ajax({
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=appointment&action=show_appointment&nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: { appointment_id:appointment_id, token:token },
			beforeSend: function() {

			},	
			complete: function() {

			},
			success: function(json) {

				if ( json['info'] ) 
				{

					$('#ModalAddList .modal-title').html( '{LANG.appointment_info}: ' + json['customer_full_name'] ); 
					$('#ModalAddList .modal-body').html( json['info'] ); 
					$('#ModalAddList').modal('show') 

				}
				else if ( json['error'] ) 
				{
					alert( json['error'] );	
				}

			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});

	}
	function delete_appointment(appointment_id, token) {
		if(confirm('{LANG.confirm}')) {
			$.ajax({
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=appointment&action=delete&nocache=' + new Date().getTime(),
				type: 'post',
				dataType: 'json',
				data: 'appointment_id=' + appointment_id + '&token=' + token,
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
						$('#appointment-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
					}

					if (json['success']) {
						$('#appointment-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
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
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=appointment&action=delete&nocache=' + new Date().getTime(),
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
						$('#appointment-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
					}

					if (json['success']) {
						$('#appointment-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
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
		var appointment_id = $(this).attr('data-appointment_id');
		var new_vid = ( $(this).prop('checked') === true ) ? 1 : 0;
		var id = $(this).attr('id');
		$.ajax({
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=appointment&nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'action=' + action + '&appointment_id=' + appointment_id + '&new_vid=' + new_vid + '&token='+token,
			beforeSend: function() {
				$('#'+id ).prop('disabled', true);
				$('.alert').remove();
			},	
			complete: function() {
				$('#'+id ).prop('disabled', false);
			},
			success: function(json) {

				console.log(json);

				if ( json['error'] ) {
					alert( json['error'] );
					( new_vid ) ? $(this).prop('checked', true) : $(this).prop('checked', false);
				};
				
				if ( json['success'] ) {
					alert( json['success'] );
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
		var appointment_id = $(this).attr('data-id');
		var new_vid = $(this).val();
		var id = $(this).attr('id');
		$.ajax({
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=appointment&nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'action=' + action + '&appointment_id=' + appointment_id + '&new_vid=' + new_vid + '&token='+token,
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
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=export&action=export_appointment&nocache=' + new Date().getTime(),
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