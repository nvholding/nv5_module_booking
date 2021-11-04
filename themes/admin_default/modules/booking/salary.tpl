<!-- BEGIN: main -->
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<style>
	.ui-datepicker-calendar {
		display: none;
	}
</style>
<div id="doctors-content">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3><i class="fa fa-list"></i> Thống kê lịch sử trị liệu</h3> 
		</div>
		<div class="panel-body">
			<div class="well">
				<div class="row">
			<form  action="{NV_BASE_ADMINURL}index.php" method="get" id="formsearch">
				<input type="hidden" name ="{NV_NAME_VARIABLE}"value="{MODULE_NAME}" />
				<input type="hidden" name ="{NV_OP_VARIABLE}"value="{OP}" />
				<div class="col-sm-6">
					<div class="form-group">
						<label class="control-label clear" for="input-active">
							Lọc dũ liệu theo thầy <sup style="color:red"> * </sup>
						</label>
						<select  name="select_doctor" id="select_doctor" class="form-control">
						</select>
					</div> 
				</div>
				<div class="col-sm-10">
					<div class="form-group">
						<label class="control-label clear" for="input-date_added">
							Chọn thời gian <sup style="color:red"> * </sup>
						</label>
						<div class="clear"></div>
						<input type="text" name="date_from" value="{DATA.date_from}" id="date_from"  placeholder="{LANG.doctors_date_from}" class="form-control" autocomplete="off" style="display:inline-block;width:100px"> 
						<button type="button" class="btn btn-primary" onclick="view_salary()"> 
							Truy Vấn
						</button>
						<button type="button" class="btn btn-primary export_file"  onclick="export_salary()"> 
							<i class="fa fa-download"></i>
							Xuất file excel
						</button>
					</div> 
				</div>
			</form>
		</div>
			</div>
			<div id="receive_data_salary">

			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<link rel="stylesheet" type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">


<script type="text/javascript">
	function view_salary(){
		var doctor_id = $('#select_doctor').val();
		var month = $("input[name=date_from]").val();
		if(!doctor_id){
			alert("Vui lòng chọn bác sĩ");
		}else if(!month){
			alert("Vui lòng chọn tháng");
		}else{
			$.ajax({
				type : 'GET',
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=salary&mod=load_salary',
				data: {doctor_id:doctor_id,month:month},
				success : function(res){
					res2=JSON.parse(res);
					if(res2.status=="OK"){
						$('#receive_data_salary').html(res2.contents);
					}else{
						alert("có lỗi xảy ra!, vui lòng kiểm tra lại!");
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}
	function export_salary(){
		var doctor_id = $('#select_doctor').val();
		var month = $("input[name=date_from]").val();
		if(!doctor_id){
			alert("Vui lòng chọn bác sĩ");
		}else if(!month){
			alert("Vui lòng chọn tháng");
		}else{
			$.ajax({
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=export&action=export_salary&nocache=' + new Date().getTime(),
				type: 'get',
				dataType: 'json',
				data: {doctor_id:doctor_id,month:month},
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
		}
	}
	$('#date_from').datepicker({
		changeMonth: true,
		changeYear: true,
		showButtonPanel: true,
		dateFormat: 'mm/yy',
		onClose: function(dateText, inst) { 
			$(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
		}
	});

	$('#select_doctor').select2({
		placeholder:"Chọn thầy", 
		width:"100%",
		ajax: {
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '={OP}&mod=get_list_doctor',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				var query = {
					q: params.term
				}
				return query;
			},
			processResults: function (data) {
				return {
					results: data
				};
			},
			cache: true
		}
	}).on('change', function (e) {

	})
</script>
<!-- END: main -->