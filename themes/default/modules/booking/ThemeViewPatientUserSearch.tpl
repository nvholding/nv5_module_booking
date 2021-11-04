<!-- BEGIN: main -->
<!-- BEGIN: data -->
<table class="table table-bordered table-hover">
	<thead>
		<tr>
			<td class="col-md-0 text-center"><strong>{LANG.patient_stt}</strong></td>
			<td class="col-md-1x text-center"><strong>{LANG.patient_date_added}</strong></td>
			<td class="col-md-1x text-center"><strong>{LANG.patient_doctors}</strong></td>
			<td class="col-md-1x text-center"><strong>{LANG.patient_blood_pressure}</strong></td>
			<td class="col-md-1x text-center"><strong>{LANG.patient_price}</strong></td>
			<td class="col-md-1x text-center"><strong>{LANG.patient_patient_result}</strong></td>
			<td class="col-md-1x text-center"><strong>{LANG.patient_typemedicine}</strong></td>
			
		</tr>					
	</thead>
	<tbody>
		<!-- BEGIN: loop --> 
		<tr id="group_{LOOP.patient_id}">
			<td class="text-center">
				{LOOP.stt}
			</td>
			<td class="text-left">
				{LOOP.date_added}
			</td>
			<td class="text-center">
				{LOOP.doctors}
			</td>
			<td class="text-center">
				{LOOP.blood_pressure}
			</td>
			<td class="text-center">
				{LOOP.price}
			</td>
			<td class="text-center">
				{LOOP.patient_result}
			</td>
			 <td class="text-center">
				{LOOP.typemedicine}
			</td>
			 
		</tr>
		<!-- END: loop -->
	</tbody>
</table>
<!-- END: data -->
<!-- BEGIN: no_data -->
<table class="table table-bordered table-hover">

	<tbody>
		<tr >
			<td class="text-center">
				{LANG.no_data}
			</td>
			
		</tr>
	</tbody>
</table>
<!-- END: no_data -->
<!-- BEGIN: generate_page -->
<div class="row">
	<div class="col-sm-24 text-center">
	{GENERATE_PAGE}			
	</div>
</div>
<!-- END: generate_page -->

<!-- END: main -->