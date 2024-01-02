<!-- BEGIN: main -->
<form class="form-horizontal" action="{NV_BASE_ADMINURL}index.php" method="post">
	<input type="hidden" value="1" name="savesetting" />
	<input type="hidden" name ="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" />
	<input type="hidden" name ="{NV_OP_VARIABLE}" value="{OP}" />
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover">
			<tbody>
				<tr>
					<td style="width:280px"><strong>{LANG.setting_group_doctors}</strong></td>
					<td>
						<div class="boxajax">
							<input type="hidden" class="form-control" name="default_group_doctors" value="{DATA.default_group_doctors}" >	 
							<i class="fa fa-times {SHOW1}" aria-hidden="true"></i>							
							<input type="text" class="form-control" id="default_group_doctors" value="{DATA.default_group_doctors_title}" placeholder="{LANG.setting_group_user_select}">	
						</div> 
					</td>
				</tr>
				<tr>
					<td style="width:280px"><strong>{LANG.setting_booking_time}</strong></td>
					<td>						<input type="text" class="form-control" name="booking_time" value="{DATA.booking_time}" placeholder="{LANG.booking_time_help}" style="width:150px;display:inline-block"> {LANG.setting_hour}					</td>
				</tr>
				<tr>
					<td style="width:280px"><strong>{LANG.setting_space_time}</strong></td>
					<td>
						<input type="text" class="form-control" name="space_time" value="{DATA.space_time}" placeholder="{LANG.space_time_help}"style="width:150px;display:inline-block"> {LANG.setting_minute}					 
					</td>
				</tr>
				<tr>
					<td style="width:280px"><strong>{LANG.setting_apikey}</strong></td>
					<td>
						<input type="text" class="form-control" name="apikey" value="{DATA.apikey}">
					</td>
				</tr>
				<tr>
					<td style="width:280px"><strong>{LANG.setting_secretkey}</strong></td>
					<td>
						<input type="text" class="form-control" name="secretkey" value="{DATA.secretkey}">
					</td>
				</tr>
				<tr>
					<td style="width:280px"><strong>{LANG.setting_brandname}</strong></td>
					<td>
						<input type="text" class="form-control" name="brandname" value="{DATA.brandname}">
					</td>
				</tr>
 
				<tr>
					<td><strong>{LANG.setting_activesms}</strong></td>
					<td>
						<input type="checkbox" class="form-control" name="activesms" value="1" {DATA.activesms} style="position:relative;top:6px">		 
					</td>
				</tr>
				
				<tr>
					<td><strong>{LANG.setting_timesmsbegin}</strong></td>
					<td>
						<input type="text" class="form-control" name="timesmsbegin" value="{DATA.timesmsbegin}" style="width:150px;display:inline-block">
						{LANG.setting_day}
					</td>
				</tr>
				<tr>
					<td><strong>{LANG.setting_timesmsstart}</strong></td>
					<td>
						<select class="form-control" name="smshourstart" style="width:100px;display:inline-block">
							<!-- BEGIN: smshourstart -->
							<option value="{SMSHOURSTART.key}" {SMSHOURSTART.selected}>{SMSHOURSTART.name}</option>
							<!-- END: smshourstart -->		
						</select>:

						<select class="form-control" name="smsminutestart" style="width:100px;display:inline-block">
							<!-- BEGIN: smsminutestart -->
							<option value="{SMSMINUTESTART.key}" {SMSMINUTESTART.selected}>{SMSMINUTESTART.name}</option>
							<!-- END: smsminutestart -->
						</select>			
					
					</td>
				</tr>
				
				<tr>
					<td><strong>{LANG.setting_infosms}</strong></td>
					<td>
						<textarea type="text" class="form-control" name="infosms" col="20" rows="4">{DATA.infosms}</textarea>	
						[HOTEN]: {LANG.setting_full_name}, 
						[TIME]: {LANG.setting_time}, 
						[DATE]: {LANG.setting_date}, 
						[SERVICE]: {LANG.setting_service}
					</td>
				</tr>
				 <tr>
					<td><strong>{LANG.setting_activeemail}</strong></td>
					<td>
						<input type="checkbox" class="form-control" name="activeemail" value="1" {DATA.activeemail} style="position:relative;top:6px">	
					</td>
				</tr>
				 <tr>
					<td><strong>{LANG.setting_email}</strong></td>
					<td>
						<input type="text" class="form-control" name="email" value="{DATA.email}">
					</td>
				</tr>
				<tr>
					<td><strong>{LANG.setting_timeemailbegin}</strong></td>
					<td>
						<input type="text" class="form-control" name="timeemailbegin" value="{DATA.timeemailbegin}" style="width:150px;display:inline-block">
						{LANG.setting_day}	
					</td>
				</tr>
				<tr>
					<td><strong>{LANG.setting_timeemailstart}</strong></td>
					<td>
						<select class="form-control" name="emailhourstart" style="width:100px;display:inline-block">
							<!-- BEGIN: emailhourstart -->
							<option value="{EMAILHOURSTART.key}" {EMAILHOURSTART.selected}>{EMAILHOURSTART.name}</option>
							<!-- END: emailhourstart -->		
						</select>:

						<select class="form-control" name="emailminutestart" style="width:100px;display:inline-block">
							<!-- BEGIN: emailminutestart -->
							<option value="{EMAILMINUTESTART.key}" {EMAILMINUTESTART.selected}>{EMAILMINUTESTART.name}</option>
							<!-- END: emailminutestart -->
						</select>
					</td>
				</tr>
				<tr>
					<td><strong>{LANG.setting_numberemail}</strong></td>
					<td>
						<input type="text" class="form-control" name="numberemail" value="{DATA.numberemail}" style="width:150px;display:inline-block">					 
					
					</td>
				</tr>
				<tr>
					<td><strong>{LANG.setting_titleemail}</strong></td>
					<td>
						<input type="text" class="form-control" name="titleemail" value="{DATA.titleemail}" >
						[HOTEN]: {LANG.setting_full_name}, [PHONE]: {LANG.setting_phone}, [TIME]: {LANG.setting_time}, [DATE]: {LANG.setting_date}, [SERVICE]: {LANG.setting_service}
					
					</td>
				</tr>
				<tr>
					<td><strong>{LANG.setting_infoemail}</strong></td>
					<td>
						{DATA.infoemail}
						[HOTEN]: {LANG.setting_full_name}, 
						[PHONE]: {LANG.setting_phone}, 
						[TIME]: {LANG.setting_time}, 
						[DATE]: {LANG.setting_date}, 
						[SERVICE]: {LANG.setting_service}			
					</td>
				</tr>
				<tr>
					<td><strong>{LANG.setting_location}</strong></td>
					<td>
						<textarea class="form-control" name="location" rows="5">{DATA.location}</textarea>
					</td>
				</tr>
				 
				 
				<tr>
					<td style="text-align: left; padding-left:290px;" colspan="2">
					<input class="btn btn-primary" type="submit" value="{LANG.save}" name="Submit1" />
				</tr>        
			</tbody>
		</table>
	</div>
</form>
<script type="text/javascript">
$('#default_group_doctors').autofill({
	'source': function(request, response) {	 
		$.ajax({
			url: script_name + '?' + nv_name_variable + '='+ nv_module_name  +'&' + nv_fc_variable + '=group_user&action=get_group&title='+ encodeURIComponent(request) +'&nocache=' + new Date().getTime(),		
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
				return {
					label: item['title'],
					value: item['group_user_id']
				}
			}));
			}
		});	 
	},
    'select': function(item) {
		$('#default_group_doctors').val( item['label'] );
		$('input[name="default_group_doctors"]').val( item['value'] );
		$('#default_group_doctors').parent().find('i').show();
	}
});  
   
$(document).delegate('.boxajax i', 'click', function() {
	$(this).parent().find('input').val('');
	$(this).hide();
});	
 
$("input[name=selectimg]").click(function() {
	var area = "default_form_import";
	var path = "{UPLOAD_CURRENT}";
	var currentpath = "{UPLOAD_CURRENT}";
	var type = "files";
	nv_open_browse(script_name + "?" + nv_name_variable + "=upload&popup=1&area=" + area + "&path=" + path + "&type=" + type + "&currentpath=" + currentpath, "NVImg", 850, 420, "resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
	return false;
});
</script>
<!-- END: main -->