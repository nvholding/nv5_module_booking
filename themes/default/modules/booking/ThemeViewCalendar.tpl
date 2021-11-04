<!-- BEGIN: main -->
<div class="panel panel-default">
	<div class="panel-body">
		<form action="#" method="post" enctype="multipart/form-data" id="form-calendar">
			<div class="table-responsive">
				<table class="table table-bordered table-hover">
					<caption class="text-center">
						<a href="#" id="selectdate">
							<i class="fa fa-calendar" aria-hidden="true"> </i>
							LỊCH LÀM VIỆC
							<span id="dateText">
								THÁNG {CURRENT_MONTH}
							</span>
							<input name="date_from" id="date_from" type="text" />
						</a>
					</caption>
					<thead>
						<tr>
							<td class="col-sm-2 text-center">
								<strong>
									Ngày làm việc
								</strong>
							</td>	
							<td class="col-sm-1 text-center">
								<strong>
									Lịch làm việc
								</strong>
							</td>
						</tr>
					</thead>
					<tbody id="showContent">
						<!-- BEGIN: data -->
						<tr>
							<td class="text-center">
								{DATA.date_start}
							</td>
							<td class="text-center">
								<input type="hidden" name="getCalendar[{DATA.day}][token]" value="{DATA.token}">
								<input type="hidden" name="getCalendar[{DATA.day}][calendar_id]" value="{DATA.calendar_id}">
								<input type="hidden" name="getCalendar[{DATA.day}][date_start]" value="{DATA.date_start}">
								<select shift name="getCalendar[{DATA.day}][shift]" class="form-control btn-sm" {DATA.disabled}>
									<!-- BEGIN: shift -->
									<option value="{SHIFT.key}" {SHIFT.selected} {SHIFT.disabled}>
										{SHIFT.name} 
									</option>
									<!-- END: shift -->

								</select>
							</td>
						</tr>
						<!-- END: data -->
					</tbody>
				</table>
			</div>
		</form>
	</div>
</div>
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<style>
	.ui-datepicker-trigger{
		display:none !important;
	}
	#selectdate{
		position:relative;
		display:inline-block;
		cursor: pointer
	}
	#selectdate input{
		position:absolute;
		opacity: 0;
		left:0;
		width:100%;
		cursor: pointer

	}

</style>

<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>

<script>
	$(document).ready(function(){
		arrayshift = {ARRAYSHIFT}; 
		$("#date_from").datepicker({
			showOn : "both",
			dateFormat : "dd/mm/yy",
			changeMonth : true,
			changeYear : true,
			showOtherMonths : true,
			buttonImage : nv_base_siteurl + "assets/images/calendar.gif",
			buttonImageOnly : true,
			onChangeMonthYear: function (e, f) {

				var getday =  'THÁNG ' + ("0"+f).slice(-2) + '/'+e;

				$('#dateText').text(getday);

				var getday = '01/' + ("0"+f).slice(-2) + '/'+e;

				$.ajax({
					url: nv_base_siteurl + 'index.php?' + nv_name_variable + '='+ nv_module_name +'&' + nv_fc_variable + '=ajax&action=getcalendar&second=' + new Date().getTime(),
					type: 'post',
					dataType: 'json',
					data: 'getday=' + getday + '&type=0&' + getday + '&token={TOKEN}',
					beforeSend: function() {

					},	
					complete: function() {

					},
					success: function(json) {


						if (json['error']) {
							alert( json['error'] );
						}
						var tmp ='';
						if (json['data']) {

							$.each(json['data'], function(i, item){

								tmp+='<tr>';
								tmp+='	<td class="text-center">';
								tmp+= item['date_start'];
								tmp+='	</td>';
								tmp+='	<td class="text-center">';
								tmp+='		<input type="hidden" name="getCalendar['+ item['day'] +'][token]" value="'+ item['token'] + '">';
								tmp+='		<input type="hidden" name="getCalendar['+ item['day'] +'][calendar_id]" value="'+ item['calendar_id'] + '">';
								tmp+='		<input type="hidden" name="getCalendar['+ item['day'] +'][date_start]" value="'+ item['date_start'] + '">';
								tmp+='		<select shift name="getCalendar['+ item['day'] +'][shift]" class="form-control btn-sm" '+ item['disabled'] +'>';
								$.each(arrayshift, function(k, v){
									selected = ( k == item['shift'] ) ? 'selected="selected"' : '';
									tmp+='			<option value="'+ k +'" '+ selected +' '+ item['disabled'] +'> '+ v +' </option>';
								});	 
								tmp+='		</select>';
								tmp+='	</td>';
								tmp+='</tr>';

							});

						}
						$('#showContent').html( tmp ); 					
					},
					error: function(xhr, ajaxOptions, thrownError) {
						console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			},

			onSelect: function (dateText, inst) {
				console.log(dateText);
				console.log(inst);

				var getday = 'NGÀY ' + dateText;
				$('#dateText').text(getday);


				$.ajax({
					url: nv_base_siteurl + 'index.php?' + nv_name_variable + '='+ nv_module_name +'&' + nv_fc_variable + '=ajax&action=getcalendar&second=' + new Date().getTime(),
					type: 'post',
					dataType: 'json',
					data: 'getday=' + dateText + '&type=1&token={TOKEN}',
					beforeSend: function() {

					},	
					complete: function() {

					},
					success: function(json) {


						if (json['error']) {
							alert( json['error'] );
						}
						var tmp ='';
						if (json['data']) {

							$.each(json['data'], function(i, item){

								tmp+='<tr>';
								tmp+='	<td class="text-center">';
								tmp+= item['date_start'];
								tmp+='	</td>';
								tmp+='	<td class="text-center">';
								tmp+='		<input type="hidden" name="getCalendar['+ item['day'] +'][token]" value="'+ item['token'] + '">';
								tmp+='		<input type="hidden" name="getCalendar['+ item['day'] +'][calendar_id]" value="'+ item['calendar_id'] + '">';
								tmp+='		<input type="hidden" name="getCalendar['+ item['day'] +'][date_start]" value="'+ item['date_start'] + '">';
								tmp+='		<select shift name="getCalendar['+ item['day'] +'][shift]" class="form-control btn-sm" '+ item['disabled'] +'>';
								$.each(arrayshift, function(k, v){
									selected = ( k == item['shift'] ) ? 'selected="selected"' : '';
									tmp+='			<option value="'+ k +'" '+ selected +' '+ item['disabled'] +'> '+ v +' </option>';
								});	 
								tmp+='		</select>';
								tmp+='	</td>';
								tmp+='</tr>';

							});

						}	
						$('#showContent').html( tmp );	
					},
					error: function(xhr, ajaxOptions, thrownError) {
						console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});

			}
		}); 

$(document).on('change', 'select[shift]', function(e){
	var obj = $(this);
	e.preventDefault();
	$.ajax({
		type: "POST",
		dataType: 'json',
		url: nv_base_siteurl + 'index.php?' + nv_name_variable + '='+ nv_module_name +'&' + nv_fc_variable + '=ajax&action=savecalendar&second=' + new Date().getTime(),
		data: obj.parent().find('input,select'),
		beforeSend: function() {
			$('#form-calendar').css('opacity', '0.7');
			<!-- $('#submitpermission').prop('disabled', true); -->
			<!-- $('.message').remove(); -->
		},	
		complete: function() {
			$('#form-calendar').css('opacity', 1);
			<!-- $('#submitpermission').prop('disabled', false); -->
			$('div[class="tooltip fade top in"]').remove();
		},
		success: function(json) {
			if( json['success'] )
			{
				<!-- $('#submitpermission').after('<span class="message success">'+ json['success'] +'</span>').slideDown(1000); -->
			}
			else if( json['error'] )
			{
				<!-- $('#submitpermission').after('<span class="message error">'+ json['error'] +'</span>').slideDown(1000); -->
			}
			setTimeout(function(){$('.message').slideUp(1000).remove()}, 1000)	
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});

})

}); 


</script>

<!-- END: main -->