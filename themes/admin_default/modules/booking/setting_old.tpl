<!-- BEGIN: main -->
<div id="photo-content">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title" style="float:left"><i class="fa fa-pencil"></i> {LANG.setting}</h3>
            <div class="pull-right">
                <button type="submit" form="form-stock" data-toggle="tooltip" class="btn btn-primary" title="{LANG.save}"><i class="fa fa-save"></i>
                </button> <a href="{CANCEL}" data-toggle="tooltip" class="btn btn-default" title="{LANG.cancel}"><i class="fa fa-reply"></i></a>
            </div>
            <div style="clear:both"></div>
        </div>
		<div class="panel-body">
			<form action="" method="post" enctype="multipart/form-data" id="form-setting" class="form-horizontal">
				<input type="hidden" value="1" name="savesetting" />				
				<input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" />
				<input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}" />
				
				
				<div class="form-group">
					<label class="col-sm-6 control-label">{LANG.setting_booking_time}:</label>
					<div class="col-sm-18">
						<input type="text" class="form-control" name="booking_time" value="{DATA.booking_time}" placeholder="{LANG.booking_time_help}" style="width:150px;display:inline-block"> {LANG.setting_hour}			 
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label">{LANG.setting_space_time}:</label>
					<div class="col-sm-18">
						<input type="text" class="form-control" name="space_time" value="{DATA.space_time}" placeholder="{LANG.space_time_help}"style="width:150px;display:inline-block"> {LANG.setting_minute}					 
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-6 control-label">{LANG.setting_apikey}:</label>
					<div class="col-sm-18">
						<input type="text" class="form-control" name="apikey" value="{DATA.apikey}">
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-6 control-label">{LANG.setting_secretkey}:</label>
					<div class="col-sm-18">
						<input type="text" class="form-control" name="secretkey" value="{DATA.secretkey}">
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-6 control-label">{LANG.setting_brandname}:</label>
					<div class="col-sm-18">
						<input type="text" class="form-control" name="brandname" value="{DATA.brandname}">
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-6 control-label">{LANG.setting_activesms}:</label>
					<div class="col-sm-18">
						<input type="checkbox" class="form-control" name="activesms" value="1" {DATA.activesms} style="position:relative;top:6px">		 
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label">{LANG.setting_timesmsbegin}:</label>
					<div class="col-sm-18">
						<input type="text" class="form-control" name="timesmsbegin" value="{DATA.timesmsbegin}" style="width:150px;display:inline-block">
						{LANG.setting_day}						
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label">{LANG.setting_timesmsstart}:</label>
					<div class="col-sm-18">
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
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label">{LANG.setting_infosms}:</label>
					<div class="col-sm-18">
						<textarea type="text" class="form-control" name="infosms" col="20" rows="4">{DATA.infosms}</textarea>	
						[HOTEN]: {LANG.setting_full_name}, 
						[TIME]: {LANG.setting_time}, 
						[DATE]: {LANG.setting_date}, 
						[SERVICE]: {LANG.setting_service}
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-6 control-label">{LANG.setting_activeemail}:</label>
					<div class="col-sm-18">
						<input type="checkbox" class="form-control" name="activeemail" value="1" {DATA.activeemail} style="position:relative;top:6px">		 
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label">{LANG.setting_email}:</label>
					<div class="col-sm-18">
						<input type="text" class="form-control" name="email" value="{DATA.email}">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label">{LANG.setting_timeemailbegin}:</label>
					<div class="col-sm-18">
						<input type="text" class="form-control" name="timeemailbegin" value="{DATA.timeemailbegin}" style="width:150px;display:inline-block">
						{LANG.setting_day}							
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label">{LANG.setting_timeemailstart}:</label>
					<div class="col-sm-18">
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
 	
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label">{LANG.setting_numberemail}:</label>
					<div class="col-sm-18">
						<input type="text" class="form-control" name="numberemail" value="{DATA.numberemail}" style="width:150px;display:inline-block">					 
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label">{LANG.setting_titleemail}:</label>
					<div class="col-sm-18">
						<input type="text" class="form-control" name="titleemail" value="{DATA.titleemail}" >
						[HOTEN]: {LANG.setting_full_name}, [PHONE]: {LANG.setting_phone}, [TIME]: {LANG.setting_time}, [DATE]: {LANG.setting_date}, [SERVICE]: {LANG.setting_service}
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label">{LANG.setting_infoemail}:</label>
					<div class="col-sm-18">
						{DATA.infoemail}
						[HOTEN]: {LANG.setting_full_name}, 
						[PHONE]: {LANG.setting_phone}, 
						[TIME]: {LANG.setting_time}, 
						[DATE]: {LANG.setting_date}, 
						[SERVICE]: {LANG.setting_service}				
					</div>
				</div>    
				<hr />
				<div class="form-group">
					<label class="col-sm-6 control-label">{LANG.setting_location}:</label>
					<div class="col-sm-18">
						<textarea class="form-control" name="location" rows="5">{DATA.location}</textarea>
					</div>
				</div>
				
			</form>
		</div>
    </div>
</div>
 
<script type="text/javascript">

$('button[type=\'submit\']').on('click', function() {
	$("form[id*='form-']").submit();
});
 
</script>
<!-- END: main -->