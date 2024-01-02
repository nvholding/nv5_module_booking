<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/fancybox/jquery.fancybox.css">
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{NV_BASE_SITEURL}themes/{TEMPLATE}/css/booking.css">


<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/jquery.scrollTo-min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/fancybox/jquery.fancybox.pack.js"></script>

<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>

<div id="Appointment">
	<button type="button" id="showAppointment" class="call-booking-btn btn btn-primary">
		{LANG.appointment}
	</button>
</div>

<div class="top-booking-outer">
	<span class="top-booking-close">
		{LANG.close}
	</span>
	<div class="top-booking-wrp">
		<div class="top-booking-inner">
			<div class="top-booking-locations">
				<img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/logo.png" alt="" width="150">
			</div>
			<div class="clear"></div>
			<div class="top-booking-addresses">
				<p class="top-booking-address-1">
					{CONFIG.location}
				</p>
			</div>
			<div class="clear"></div>
			<p>
				<strong>
					{LANG.service}
				</strong>
			</p>
			<div class="spacer20"></div>
			<ul class="booking-choose">
				<!-- BEGIN: service -->
				<li>
					<a href="#">
						{SERVICE.service_name} 
						<i class="fa fa-check-circle"></i>
					</a>
					<input type="checkbox" value="{SERVICE.service_id}" name="service[]" style="display:none" />	
				</li>
				<!-- END: service -->
			</ul>
			<div class="spacer20"></div>
			<p><strong>{LANG.time_go}:</strong></p>
			<div class="spacer20"></div>
			<div class="timing-tabs">
				<a href="#" class="timing-tab timing-tab-first timing-tab-active" id="timing-tab-1" rel="timing-panel-1" data-rel="1">{LANG.today}</a>
				<a href="#" class="timing-tab timing-tab-last" id="timing-tab-2" rel="timing-panel-2" data-rel="2">{LANG.other_day}</a>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
			<div class="timing-panels">
				<div class="timing-panel-date" id="timing-panel-date-1">
					{TODAY}
				</div>
				<div class="timing-panel-date" id="timing-panel-date-2">
					{TODAY}
				</div>
				<div class="timing-date-edit">
					{LANG.change}
				</div>
				<div class="timing-panel" id="timing-panel-1">
					<span class="timing-panel-arrow">&nbsp;</span>
					<ul class="timing-list">
						<!-- BEGIN: time1 -->
						
						<li {ACTIVE}>{TIME}</li>
						
						<!-- END: time1 -->
					</ul>
				</div>
				<div class="timing-panel" id="timing-panel-2">
					<span class="timing-panel-arrow">&nbsp;</span>
					<div class="timing-date-wrp">
						<div id="timing-date"></div>
					</div>
					<ul class="timing-list timing-list-other">
						<!-- BEGIN: time2 -->
						
						<li>{TIME}</li>
						
						<!-- END: time2 -->
					</ul>
				</div>
			</div>
			<div class="clear"></div>
			<div class="booking-info">
				<input type="hidden" name="booking_location" id="booking_location" value="" />
				<input type="hidden" name="booking_date" id="booking_date" value="{TODAY}" />
				<input type="hidden" name="booking_hour" id="booking_hour" value="" />
				<input type="hidden" name="branch_id" id="branch_id" >
				<select id="branch" class="form-control booking-info-inp" required >

				</select>
				<select name="doctors" id="doctors" class="form-control booking-info-inp" required >

				</select>
				<input type="text" name="booking_name" value="{USER.full_name}" id="booking_name" class="booking-info-inp" placeholder="{LANG.full_name}" required />
				<input type="text" name="booking_phone" value="{USER.username}" id="booking_phone" class="booking-info-inp" placeholder="{LANG.phone}" required />
				<input type="text" name="booking_email" value="{USER.email}" id="booking_email" class="booking-info-inp" placeholder="{LANG.email}" required />
				<textarea name="booking_message" id="booking_message" class="booking-info-txt" placeholder="{LANG.message}"></textarea>
				<button type="submit" name="booking_send" id="booking_send" class="booking-info-btn" style="">{LANG.booking_send}</button>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(function(){
		$("#timing-date").datepicker({ dateFormat: 'dd/mm/yy', minDate: 0, dayNamesMin: [ "CN", "T2", "T3", "T4", "T5", "T6", "T7" ], monthNames: [ "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12" ] });
	} );
	$(document).on("change", "#timing-date", function () {
		var choosenDate = $(this).val();
		$('.timing-date-wrp').hide();
		$('.timing-list-other').show();
		$('#booking_date').val(choosenDate);
	//Add in
	$('#timing-panel-date-1').hide();
	$('#timing-panel-date-2').text(choosenDate);
	$('#timing-panel-date-2').show();
	$('.timing-date-edit').show();
})
	$(window).on('load', function(){
	//call-booking-btn
	$('.call-booking-btn').on('click', function(e){
		e.preventDefault();
		$('.top-contact-outer').hide();
		$('.top-booking-outer').fadeIn(300);
		$('body').addClass('overlay');
		
		$("#branch").select2({  
			multiple: true,        
			maximumSelectionLength: 1,
			placeholder: 'Chọn phòng khám *',
			ajax: {                
				url: nv_base_siteurl + 'index.php?' + nv_name_variable + '={MODULE_NAME}&' + nv_fc_variable + '=ajax&action=getBranch&second=' + new Date().getTime(),
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
			placeholder: 'Chọn bác sỹ *',
			ajax: {                
				url: nv_base_siteurl + 'index.php?' + nv_name_variable + '={MODULE_NAME}&' + nv_fc_variable + '=ajax&action=getDoctors&second=' + new Date().getTime(),
				type: "post",      
				dataType: 'json',  
				delay: 250,        
				data: function (params) {

					var query = {
						search : params.term,
						branch_id : $('#branch_id').val(),
						booking_date:  $('#booking_date').val(),
						booking_hour:  $('#booking_hour').val()
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
		
	});
	//.top-booking-close
	$('.top-booking-close').on('click', function(){
		$('.top-booking-outer').hide();
		$('body').removeClass('overlay');
	});
	//timing-list
	$('ul.timing-list li').on('click', function(){
		$('ul.timing-list li').removeClass('timing-list-active');
		$(this).addClass('timing-list-active');
	});
	//timing-tab
	$('.timing-tab').on('click', function(e){
		e.preventDefault();
		$('.timing-tab').removeClass('timing-tab-active');
		$(this).addClass('timing-tab-active');
		var panel = $(this).attr('rel');
		$('.timing-panel').hide();
		$('#'+panel).fadeIn(200);
		$('.timing-list-other').hide();
		$('.timing-date-wrp').show();
		//Add in
		var dataRel = $(this).attr('data-rel');
	});
	var now = getToday();
	$('ul.timing-list li').on('click', function(){
		var bookingTime = $(this).text();
		$('#booking_hour').val(bookingTime);
		$('#doctors').val('').trigger("change");


	});
	$('#timing-tab-1').on('click', function(){
		$('#booking_date').val(now);
		//Add in
		$('#timing-panel-date-2').hide();
		$('.timing-date-edit').hide();
		$('#timing-panel-date-1').show();
	});
	$('#timing-tab-2').on('click',function(){
		$('#timing-panel-date-1').hide();
	});
	$('.timing-date-edit').on('click', function(){
		$('#timing-panel-date-2').hide();
		$('.timing-date-edit').hide();
		$('.timing-list-other').hide();
		$('.timing-date-wrp').show();
	});
	
	
	
	
	$('#booking_send').on('click', function(){                                
		var bookingLocation = $('#booking_location').val();
		var bookingDate = $('#booking_date').val();
		var bookingHour = $('#booking_hour').val();
		var bookingName = $('#booking_name').val();
		var bookingPhone = $('#booking_phone').val();
		var bookingEmail = $('#booking_email').val();
		var bookingMessage = $('#booking_message').val();
		var branch_id = $('#branch').val();
		var doctors_id = $('#doctors').val();
		console.log(doctors_id);
		var bookingService = [];
		$("input[name=\"service[]\"]:checked").each(function() {
			bookingService.push($(this).val());
		});
		
		if(bookingName != '' && bookingPhone != ''){
			$.ajax({
				url: nv_base_siteurl + 'index.php?' + nv_name_variable + '={MODULE_NAME}&' + nv_fc_variable + '=ajax&action=register_medical&second=' + new Date().getTime(),
				type: 'POST',
				dataType: 'json',
				data : {
					booking_location: bookingLocation,
					booking_date: bookingDate,
					booking_hour: bookingHour,
					booking_name: bookingName,
					booking_phone: bookingPhone,
					booking_email: bookingEmail,
					booking_message: bookingMessage,
					booking_service: bookingService,
					branch_id: branch_id,
					doctors_id: doctors_id
				},
				success:function(json, textStatus, jqXHR){
					if(json['success'] ){
						$.fancybox('<p style="width:300px;text-align:center;padding:40px 0 0;font-size:15px;">{LANG.thankyou}</p>');
						$('.top-booking-outer').hide();
						//Reset
						<!-- $('#booking_name, #booking_phone, #booking_email, #booking_message').val(''); -->
						<!-- $('#booking_date').val(now); -->
						<!-- $('#booking_hour').val('9:00'); -->
						<!-- $('ul.timing-list li').removeClass('timing-list-active'); -->
						<!-- $('ul.timing-list li:first-child').addClass('timing-list-active'); -->
						<!-- $('.timing-tab').removeClass('timing-tab-active'); -->
						<!-- $('#timing-tab-1').addClass('timing-tab-active'); -->
					}else if(json['error'] ){
						
						var error  = '';
						$.each( json['error'], function(i, err){
							error+='<span style="display:block">'+ err +'</span>';
						} )
						
						$.fancybox('<p style="width:300px;text-align:center;padding:40px 0 0;font-size:15px;">'+ error +'</p>');
						<!-- $('.top-booking-outer').hide(); -->
					}
					
				},
				error: function(jqXHR, textStatus, errorThrown){
					//If fail return textStatus
				}
			});
		}else{
			$.fancybox('<p style="width:300px;text-align:center;padding:40px 0 0;font-size:15px;">{LANG.phone_error}.</p>');
		}
	});
	//booking-choose
	$('ul.booking-choose li').on('click', function(){
		if( ! $(this).hasClass( 'choose-this' ) )
		{
			$(this).addClass('choose-this');
			$(this).find('input').prop('checked', true);
		}
		else
		{
			$(this).removeClass('choose-this');
			$(this).find('input').prop('checked', false);
		}
		
		
		
	});
});
function getToday() {
	var now = new Date();
	var dd = now.getDate();
	var MM = now.getMonth();
	var yyyy = now.getFullYear();
	var currentDate= dd + "/" +( MM+1) + "/" + yyyy;
	return currentDate;
}
$(document).mouseup(function (e){
	var container_1 = $(".language-chooser");
	if (!container_1.is(e.target)){
		container_1.hide();
	}
	var container_2 = $(".doctor-item-1,.doctor-item-2,.doctor-item-3,.doctor-item-4,.doctor-bio");
	if (!container_2.is(e.target)){
		$('.doctor-bio').hide();
		$('.doctor-items img').addClass('doctor-item-active');
		$('.doctor-item-1').css({'z-index': 50},);
		$('.doctor-item-2').css({'z-index': 40},);
		$('.doctor-item-3').css({'z-index': 30},);
		$('.doctor-item-4').css({'z-index': 20},);
	}
});


</script>

<!-- END: main -->