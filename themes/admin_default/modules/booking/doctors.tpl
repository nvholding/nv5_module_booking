<!-- BEGIN: main -->
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />

<div id="doctors-content">
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
				{LANG.doctors_list}
			</h3> 
			<div class="pull-right">
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
									{LANG.doctors_full_name}
								</label>
								<input type="text" name="full_name" value="{DATA.full_name}" placeholder="{LANG.doctors_full_name}" id="input-full_name" class="form-control" autocomplete="off">								 
							</div>
						</div>

						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label" for="input-phone">
									{LANG.doctors_phone}
								</label>
								<input type="text" name="phone" value="{DATA.phone}" placeholder="{LANG.doctors_phone}" id="input-phone" class="form-control" autocomplete="off">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label clear" for="input-active">
									{LANG.doctors_active}
								</label>
								<select  name="active" class="form-control">
									<option value="">
										{LANG.doctors_active_select} 
									</option>
									<!-- BEGIN: active -->
									<option value="{ACTIVE.key}" {ACTIVE.selected}>
										{ACTIVE.name} 
									</option>
									<!-- END: active -->
								</select>
							</div> 
						</div>
						<div class="col-sm-10">
							<div class="form-group">
								<label class="control-label clear" for="input-date_added">
									{LANG.doctors_regdate}
								</label>
								<div class="clear"></div>
								<input type="text" name="date_from" value="{DATA.date_from}" id="date_from"  placeholder="{LANG.doctors_date_from}" class="form-control" autocomplete="off" style="display:inline-block;width:100px"> 
								<strong>:</strong>
								<input type="text" name="date_to" value="{DATA.date_to}" id="date_to" placeholder="{LANG.doctors_date_to}" class="form-control" autocomplete="off" style="display:inline-block;width:100px">
							</div> 
						</div>

						<div class="col-sm-24">
							<input type="hidden" value="{TOKEN}" name="token"/>
							<button type="submit" id="submitform" class="btn btn-primary" > <i class="fa fa-search"></i> 
								Tìm kiếm 
							</button>
							<!-- <button class="btn btn-info export_file" data-all="0" > <i class="fa fa-download"></i> Xuất danh sách tìm kiếm </button> -->
							<!-- <button class="btn btn-info export_file" data-all="1"> <i class="fa fa-download"></i> Xuất tất cả </button> -->
						</div> 
					</form>

				</div>
			</div>
			<form action="#" method="post" enctype="multipart/form-data" id="form-doctors">
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<td class="col-sm-0 text-center">
									<input name="check_all[]" type="checkbox" value="yes" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" >
								</td>
								<td class="col-sm-4 text-center">
									<a {FULL_NAME_ORDER} href="{URL_FULL_NAME}">
										<strong>
											{LANG.doctors_full_name}
										</strong>
									</a> 
								</td>
								<td class="col-sm-3 text-center">
									<a {USERNAME_ORDER} href="{URL_USERNAME}">
										<strong>
											{LANG.doctors_username}
										</strong>
									</a> 
								</td>
								<td class="col-sm-3 text-center">
									<a {EMAIL_ORDER} href="{URL_EMAIL}">
										<strong>
											{LANG.doctors_email}
										</strong>
									</a> 
								</td>
								<td class="col-sm-3 text-center">
									<a {ADDRESS_ORDER} href="{URL_ADDRESS}">
										<strong>
											{LANG.doctors_address}
										</strong>
									</a>
								</td>
								<td class="col-sm-3 text-center">
									<a {BRANCH_ORDER} href="{URL_BRANCH}">
										<strong>
											{LANG.doctors_branch}
										</strong>
									</a>
								</td>
								<td class="col-sm-3 text-center">
									<a {REGDATE_ORDER} href="{URL_REGDATE}">
										<strong>
											{LANG.doctors_regdate}
										</strong>
									</a> 
								</td>
								<td class="col-sm-3 text-center">
									<a {ACTIVE_ORDER} href="{URL_ACTIVE}">
										<strong>
											{LANG.doctors_active}
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
							<tr id="group_{LOOP.userid}">
								<td class="text-center">
									<input type="checkbox" name="selected[]" value="{LOOP.userid}" >
								</td>
								<td class="text-left">
									<a href="javascript:void(0);">{LOOP.full_name}</a>
								</td>
								<td class="text-center">
									{LOOP.username} 
								</td>
								<td class="text-center">
									{LOOP.email} 
								</td>
								<td class="text-center">
									{LOOP.address} 
								</td>
								<td class="text-center">
									{LOOP.branch} 
								</td>
								<td class="text-center">
									{LOOP.regdate} 
								</td>

								<td class="text-center">
									<select name="active" class="form-control form-sm formajax" data-action="active" data-id="{LOOP.userid}" data-token="{LOOP.token}">
										<!-- BEGIN: active -->
										<option value="{ACTIVE.key}" {ACTIVE.selected}>{ACTIVE.name}</option>
										<!-- END: active -->
									</select>
								</td>

								<td class="text-center">
									<a onclick="send_email('{LOOP.email}')" data-toggle="tooltip" title="{LANG.view}" class="btn btn-info  btn-sm hidden">
										Gửi mail
									</a>
									<a href="{LOOP.edit}" data-toggle="tooltip" title="{LANG.edit}" class="btn btn-primary  btn-sm"><i class="fa fa-pencil"></i></a>
									<a href="javascript:void(0);" onclick="delete_doctors('{LOOP.userid}', '{LOOP.token}')" data-toggle="tooltip" title="{LANG.delete}" class="btn btn-danger  btn-sm"><i class="fa fa-trash-o"></i>
										<script type="text/javascript">
											function send_email(email){
												$.ajax({
													type : 'POST',
													url : script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=doctors&mod=send_email',
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

		function delete_doctors(userid, token) {
			if(confirm('{LANG.confirm}')) {
				$.ajax({
					url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=doctors&action=delete&nocache=' + new Date().getTime(),
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
							$('#doctors-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
						}

						if (json['success']) {
							$('#doctors-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
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
					url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=doctors&action=delete&nocache=' + new Date().getTime(),
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
							$('#doctors-content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
						}

						if (json['success']) {
							$('#doctors-content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
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
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=doctors&nocache=' + new Date().getTime(),
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
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=doctors&nocache=' + new Date().getTime(),
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
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=export&action=is_download&nocache=' + new Date().getTime(),
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