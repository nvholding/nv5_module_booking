<!-- BEGIN: main -->
<div id="appointment-content">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left;text-transform:uppercase" ><i class="fa fa-list"></i> {LANG.appointment_list_sms} {DATA.tenlop}</h3> 
			<div class="clearfix"></div>
		</div>
		<div class="panel-body">

			<form action="#" method="post" enctype="multipart/form-data" id="form-appointment">
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<td class="col-sm-0 text-center"></td>
								<td class="col-sm-6 text-center"><a {CUSTOMER_FULL_NAME_ORDER} href="{URL_CUSTOMER_FULL_NAME}"><strong>{LANG.appointment_customer_full_name}</strong></a> </td>
								<td class="col-sm-4 text-center"><a {CUSTOMER_PHONE_ORDER} href="{URL_CUSTOMER_PHONE}"><strong>{LANG.appointment_customer_phone}</strong></a> </td>
								<td class="col-sm-4 text-center"><a {CUSTOMER_EMAIL_ORDER} href="{URL_CUSTOMER_EMAIL}"><strong>{LANG.appointment_customer_email}</strong></a> </td>
								<td class="col-sm-4 text-center"><a {CUSTOMER_DATE_BOOKING_ORDER} href="{URL_CUSTOMER_DATE_BOOKING}"><strong>{LANG.appointment_customer_date_booking}</strong></a> </td>
								<td class="col-sm-6 text-center"><a {SMS_ORDER} href="{URL_SMS}"><strong>{LANG.appointment_sms}</strong></a> </td>
							</tr>
						</thead>
						<tbody>
							 <!-- BEGIN: loop --> 
							<tr id="group_{LOOP.appointment_id}" class="nonecheck" data-scan="1" data-appointment_id="appointment_id" data-token="{LOOP.token}"  >
								<td class="text-center">
									{LOOP.stt} 
								</td>
								<td class="text-left">
									<a href="javascript:void(0);" onclick="show_appointment('{LOOP.appointment_id}', '{LOOP.token}')"  >{LOOP.customer_full_name}</a>
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
									<!-- BEGIN: sms_wait -->
									<a href="javascript:void(0);" id="scan_sms{LOOP.appointment_id}" data-appointment_id="{LOOP.appointment_id}"  data-token="{LOOP.token}" class="btn btn-success checksms"><i class="fa fa-envelope" aria-hidden="true"></i></i> Chưa gửi </a>
									<!-- END: sms_wait -->
									<!-- BEGIN: sms_sended -->
									<a href="javascript:void(0);" id="scan_sms{LOOP.appointment_id}" class="btn btn-success"><i class="fa fa-envelope-open-o" aria-hidden="true"></i></i> Đã gửi </a>
									<!-- END: sms_sended -->
								</td>
							
							</tr>
							 <!-- END: loop -->
						</tbody>
					</table>
				</div>
			</form>
			
		</div>
	</div>
</div>
<script type="text/javascript">
function scan_sms( token, appointment_id ) {
	$.ajax({
		url: nv_base_siteurl +'cronjob/sms.php?nocache=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data: 'action=send_sms&token=' + token + '&appointment_id=' + appointment_id,
		beforeSend: function() {
			$('#group_' + appointment_id).addClass('checking');
			$('#scan_sms'+appointment_id+' i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
			$('#scan_sms'+appointment_id+'').prop('disabled', true);
		},	
		complete: function() {
			<!-- $('#scan_sms'+appointment_id+' i').replaceWith('<i class="fa fa-paper-plane"></i>'); -->
			<!-- $('#scan_sms'+appointment_id+'').prop('disabled', false); -->
			
			setTimeout(function(){
				if( $('.checksms:first').length > 0 )
				{
					var appointment_id = $('.checksms:first').attr('data-appointment_id');
					var token = $('.checksms:first').attr('data-token');
					scan_sms( token, appointment_id );

				}
			}, 1000);
			
			
			
		},
		success: function(json) {
			if( json['data'] )
			{
				$('#scan_sms'+appointment_id+'').html('<i class="fa fa-envelope-open-o" aria-hidden="true"></i></i> Đã gửi');
				
				$('#scan_sms' + appointment_id).removeClass('checksms');
			}
			else if( json['error'] )
			{
				$('#scan_sms' + appointment_id).removeClass('btn-success').addClass('btn-error');
				$('#scan_sms' + appointment_id).html( '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> ' + json['error'] );
				$('#scan_sms' + appointment_id).find('i').removeClass( 'fa-paper-plane').addClass('fa-exclamation-triangle');
				$('#scan_sms' + appointment_id).removeClass('checksms');
			}
			$('#group_' + appointment_id).removeClass('checking').removeClass('nonecheck').addClass('success');
			
			
			
			setTimeout(function(){ if( $('.nonecheck').length == 0 ) location.reload(); }, 1000);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//location.reload();
		}
	});	 
}

setTimeout(function(){
	if( $('.checksms:first').length > 0 )
	{
		var appointment_id = $('.checksms:first').attr('data-appointment_id');
		var token = $('.checksms:first').attr('data-token');
		scan_sms( token, appointment_id );

	}
}, 1000);

</script>
<!-- END: main -->