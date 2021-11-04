<!-- BEGIN: main -->

<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>

<!-- BEGIN: view -->
<div class="well">
<form action="{NV_BASE_ADMINURL}index.php" method="post">
    <input type="hidden" name="{NV_LANG_VARIABLE}"  value="{NV_LANG_DATA}" />
    <input type="hidden" name="{NV_NAME_VARIABLE}"  value="{MODULE_NAME}" />
    <input type="hidden" name="{NV_OP_VARIABLE}"  value="{OP}" />
    <div class="row">
		<div class="col-xs-24 col-md-4">
            <div class="form-group">
               <input class="form-control" value="{search.keyword}" type="text" name="keyword" placeholder="Nhập mã hoặc tên KH">
            </div>
        </div>
		
        <div class="col-xs-24 col-md-4">
            <div class="form-group">
                <select class="form-control" name="brand">
					<option value="0">-- Chọn chi nhánh --</option>
					<!-- BEGIN: brand -->
					<option {brand_selected} value="{brand.branch_id}">{brand.title}</option>
					<!-- END: brand -->
				</select>
            </div>
        </div>
		<div class="col-xs-24 col-md-4">
            <div class="form-group">
                <select class="form-control" name="package">
					<option value="0">-- Chọn gói dịch vụ --</option>
					<!-- BEGIN: package -->
					<option {package_selected} value="{package.service_package_id}">{package.title}</option>
					<!-- END: package -->
				</select>
            </div>
        </div>
		
        <div class="col-xs-12 col-md-3">
            <div class="form-group">
                <input class="btn btn-primary" type="submit" value="{LANG.search_submit}" />
            </div>
        </div>
    </div>
</form>
</div>
<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th class="w100">STT</th>
                    <th>Mã khách hàng</th>
                    <th>Tên khách hàng</th>
                    <th>Số lần trị liệu còn lại</th>
                    <th>Số lần trị liệu đã dùng</th>
                   
                </tr>
            </thead>
            <!-- BEGIN: generate_page -->
            <tfoot>
                <tr>
                    <td class="text-center" colspan="6">{NV_GENERATE_PAGE}</td>
                </tr>
            </tfoot>
            <!-- END: generate_page -->
            <tbody>
                <!-- BEGIN: loop -->
                <tr>
                    <td> {VIEW.number} </td>
                    <td>{VIEW.patient_code}</td>
                    <td>{VIEW.full_name}</td>
                    <td> {VIEW.kham_conlai} </td>
                    <td> {VIEW.count} </td>
                    
                </tr>
                <!-- END: loop -->
            </tbody>
        </table>
    </div>
</form>
<!-- END: view -->

<script>

$('select[name=sea_flast]').change(function() {
    var time_from = "";
    var time_to = "";
    var time = $('select[name=sea_flast]').val();
    if (time == 1) {
        time_from = "{HOMNAY}"
        time_to = "{HOMNAY}"
    } else if (time == 2) {
        time_from = "{HOMQUA}"
        time_to = "{HOMQUA}"
    } else if (time == 3) {
        time_from = "{TUANNAY.from}"
        time_to = "{TUANNAY.to}"
    } else if (time == 4) {
        time_from = "{TUANTRUOC.from}"
        time_to = "{TUANTRUOC.to}"
    } else if (time == 5) {
        time_from = "{THANGNAY.from}"
        time_to = "{THANGNAY.to}"
    } else if (time == 6) {
        time_from = "{THANGTRUOC.from}"
        time_to = "{THANGTRUOC.to}"
    } else if (time == 7) {
        time_from = "{NAMNAY.from}"
        time_to = "{NAMNAY.to}"
    } else if (time == 8) {
        time_from = "{NAMTRUOC.from}"
        time_to = "{NAMTRUOC.to}"
    } else if (time == 9) {
        time_from = "Không chọn"
        time_to = "Không chọn"
    } else if (time == 0) {
        time_from = ""
        time_to = ""
    }
    $('#date_from').val(time_from);
    $('#date_to').val(time_to); 
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
		
		$('select[name=brand]').change(function(){
			
			var brand_id = $(this).val();
			$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=statics&brand_id_ajax=' + brand_id, function(res) {
					$('select[name=doctor]').html(res);
					
				});
		});
</script>

<!-- END: main -->