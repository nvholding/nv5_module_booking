<!-- BEGIN: main -->

<div class="panel panel-default">
	<div class="panel-heading">
			<h3 class="panel-title">
				<i class="fa fa-list"></i> 
				Mua gói dịch vụ
			</h3> 
			<div style="clear:both"></div>
	</div>
	<div class="panel-body">
		<form class="form-horizontal" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
    <input type="hidden" name="id" value="{ROW.id}" />
	
	<div class="form-group">
        <label class="col-sm-5 col-md-4 control-label"><strong>Ngày đăng ký</strong></label>
        <div class="col-sm-19 col-md-20">
            <input class="form-control" id="date_added" name="date_added" value="{ROW.date_added}" placeholder="Ngày đăng ký" type="text" maxlength="10" readonly style="display:inline-block;width:150px;background:#fff">
        </div>
    </div>
	
    <div class="form-group">
        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.service_id}</strong> <span class="red">(*)</span></label>
        <div class="col-sm-19 col-md-20">
            <select class="form-control" name="service_id">
                <option value=""> --- </option>
                <!-- BEGIN: select_service_id -->
                <option value="{OPTION.key}" {OPTION.selected}>{OPTION.title}</option>
                <!-- END: select_service_id -->
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.userid}</strong> <span class="red">(*)</span></label>
        <div class="col-sm-19 col-md-20">
            <select class="form-control" name="userid">
                <option value=""> --- </option>
                <!-- BEGIN: select_userid -->
                <option value="{OPTION.key}" {OPTION.selected}>{OPTION.title}</option>
                <!-- END: select_userid -->
            </select>
        </div>
    </div>
    <div class="form-group" style="text-align: center"><input class="btn btn-primary" name="submit" type="submit" value="{LANG.save}" /></div>
</form>
	</div>
</div>


<!-- BEGIN: view -->
<div class="panel panel-default">
	<div class="panel-heading">
			<h3 class="panel-title">
				<i class="fa fa-list"></i> 
				Lịch sử mua gói dịch vụ
			</h3> 
			<div style="clear:both"></div>
	</div>
	<div class="panel-body">

		<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
			<div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th class="w100">STT</th>
                    <th>Gói dịch vụ</th>
                    <th>Số lần trị liệu</th>
                    <th>Ngày đăng ký</th>
                    <th class="w150">&nbsp;</th>
                </tr>
            </thead>
            <!-- BEGIN: generate_page -->
            <tfoot>
                <tr>
                    <td class="text-center" colspan="4">{NV_GENERATE_PAGE}</td>
                </tr>
            </tfoot>
            <!-- END: generate_page -->
            <tbody>
                <!-- BEGIN: loop -->
                <tr>
                    <td> {VIEW.number} </td>
                    <td> {VIEW.service_id} </td>
                    <td> {VIEW.num} </td>
                    <td> {VIEW.date_added} </td>
                    <td class="text-center"><i class="fa fa-edit fa-lg">&nbsp;</i> <a href="{VIEW.link_edit}#edit">{LANG.edit}</a> - <em class="fa fa-trash-o fa-lg">&nbsp;</em> <a href="{VIEW.link_delete}" onclick="return confirm(nv_is_del_confirm[0]);">{LANG.delete}</a></td>
                </tr>
                <!-- END: loop -->
            </tbody>
        </table>
    </div>
		</form>
	</div>
</div>
<!-- END: view -->

<!-- BEGIN: error -->
<div class="alert alert-warning">{ERROR}</div>
<!-- END: error -->

<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />

<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>

<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>

<script type="text/javascript">
	$('#date_added').datepicker({
			dateFormat: 'dd/mm/yy',
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true,
		});
</script>













<!-- END: main -->