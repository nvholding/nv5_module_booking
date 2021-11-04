<!-- BEGIN: main -->
<!-- BEGIN: view -->
<a class="btn btn-primary" href="{add}">Thêm lịch sử luân chuyển thầy</a>
</br>
</br>
<div class="well">
<form action="{NV_BASE_ADMINURL}index.php" method="get">
    <input type="hidden" name="{NV_LANG_VARIABLE}"  value="{NV_LANG_DATA}" />
    <input type="hidden" name="{NV_NAME_VARIABLE}"  value="{MODULE_NAME}" />
    <input type="hidden" name="{NV_OP_VARIABLE}"  value="{OP}" />
    <div class="row">
        <div class="col-xs-24 col-md-4">
            <div class="form-group">
                <select class="form-control" name="brand">
					<option value="0">-- Chọn chi nhánh --</option>
					<!-- BEGIN: select_id_branch -->
					<option {OPTION.selected} value="{OPTION.key}">{OPTION.title}</option>
					<!-- END: select_id_branch -->
				</select>
            </div>
        </div>
		<div class="col-xs-24 col-md-4">
            <div class="form-group">
                <select class="form-control" name="doctor">
					<option value="0">-- Chọn thầy --</option>
					<!-- BEGIN: select_userid_doctor -->
					<option {OPTION.selected} value="{OPTION.key}">{OPTION.title}</option>
					<!-- END: select_userid_doctor -->
				</select>
            </div>
        </div>
		
		<div class="col-sm-4 col-md-4">
                    <div class="p-1 bg-light rounded rounded-lg shadow-sm">
                       
						<select class="form-control border-0 bg-light" id="sea_flast" name="sea_flast">
                            <option value="0">
                                -- Chọn thời gian --
                            </option>
                            <option value="1" {SELECT1}>Ngày hôm nay</option>
                            <option value="2" {SELECT2}>Ngày hôm qua</option>
                            <option value="3" {SELECT3}>Tuần này</option>
                            <option value="4" {SELECT4}>Tuần trước</option>
                            <option value="5" {SELECT5}>Tháng này</option>
                            <option value="6" {SELECT6}>Tháng trước</option>
                            <option value="7" {SELECT7}>Năm này</option>
                            <option value="8" {SELECT8}>Năm trước</option>
                            <option value="9" {SELECT9}>Toàn thời gian
                            </option>
                        </select>
						
                    </div>
                    </div>
		
		<div class="col-sm-8 col-md-8">
							<div class="form-group">
								<input type="text" name="date_from" value="{search.date_from}" id="date_from"  placeholder="{LANG.patient_date_from}" class="form-control" autocomplete="off" style="display:inline-block;width:100px"> 
								<strong>:</strong>
								<input type="text" name="date_to" value="{search.date_to}" id="date_to" placeholder="{LANG.patient_date_to}" class="form-control" autocomplete="off" style="display:inline-block;width:100px">
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
                    <th>{LANG.userid_doctor}</th>
                    <th>{LANG.id_branch}</th>
                    <th>{LANG.date_change}</th>
                    <th>Trạng thái</th>
                    <th class="w150">&nbsp;</th>
                </tr>
            </thead>
            <!-- BEGIN: generate_page -->
            <tfoot>
                <tr>
                    <td class="text-center" colspan="5">{NV_GENERATE_PAGE}</td>
                </tr>
            </tfoot>
            <!-- END: generate_page -->
            <tbody>
                <!-- BEGIN: loop -->
                <tr>
                    <td> {VIEW.number} </td>
                    <td> {VIEW.userid_doctor} </td>
                    <td> {VIEW.id_branch} </td>
                    <td> {VIEW.date_change} </td>
                    <td> <input disabled class="form-control" type="checkbox" {checked} /> </td>
                    <td class="text-center">
					<!-- BEGIN: edit -->
					<i class="fa fa-edit fa-lg">&nbsp;</i> <a href="{VIEW.link_edit}#edit">{LANG.edit}</a> - <em class="fa fa-trash-o fa-lg">&nbsp;</em> <a href="{VIEW.link_delete}" onclick="return confirm(nv_is_del_confirm[0]);">{LANG.delete}</a>
					<!-- END: edit -->
					</td>
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
	
</script>

<!-- END: main -->