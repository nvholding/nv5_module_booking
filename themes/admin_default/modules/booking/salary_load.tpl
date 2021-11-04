<!-- BEGIN: main -->
<div class="container-fluid">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h2>Dữ liệu Thầy</h2>
		</div>
		<div class="panel-body">
			<p>
				Tên Thầy: <strong>{DATA.first_name} {DATA.last_name}</strong>
			</p>
			<p>
				Thống kê tháng <strong> {MONTH} </strong>
			</p>
			<p>
				Tổng số: <strong> {COUNT} trị liệu trong tháng </strong>
			</p>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3>
				Danh sách khách hàng đã trị liệu
			</h3>
		</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<td class="text-center">
								Mã Khách Hàng
							</td>
							<td class="text-center">
								Xưng hô
							</td>
							<td class="text-center">
								Tên khách hàng
							</td>
							
							<td class="text-center">
								Số điện thoại
							</td>
							<td class="text-center">
								Ngày trị liệu
							</td>
						</tr>
					</thead>
					<tbody>
						<!-- BEGIN: loop --> 
						<tr id="group_{LOOP.appointment_id}">
			
							<td class="text-center">
								{ROW.patient_code}
							</td>
							<td class="text-center">
								{ROW.confess}
							</td>
							<td class="text-center">
								{ROW.full_name}
							</td>
							
							<td class="text-center">
								{ROW.phone}
							</td>
							<td class="text-center">
								{ROW.customer_date_booking} 
							</td>    
						</tr>
						<!-- END: loop -->
					</tbody>
				</table>
			</div>
		

			<!-- BEGIN: no_info -->
			<div clas="well">
				Không có ca khám bệnh trong tháng này!
			</div>
			<!-- END: no_info -->
		</div>
	</div>
</div>

<!-- END: main -->