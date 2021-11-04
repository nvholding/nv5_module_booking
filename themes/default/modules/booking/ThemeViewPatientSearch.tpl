<!-- BEGIN: main -->
<!-- BEGIN: data -->
<table class="table table-bordered table-hover">
	<thead>
		<tr>
			<td class="col-md-0 text-center"><strong>{LANG.patient_stt}</strong></td>
			<td class="col-md-1x text-center"><strong>{LANG.patient_fullname}</strong></td>
			<td class="col-md-1x text-center"><strong>{LANG.patient_gender}</strong></td>
			<td class="col-md-1x text-center"><strong>{LANG.patient_birthday}</strong></td>
			<td class="col-md-1x text-center"><strong>{LANG.patient_phone}</strong></td>
			<td class="col-md-1x text-center"><strong>{LANG.patient_address}</strong></td>								
		</tr>
	</thead>
	<tbody>
		<!-- BEGIN: loop --> 
		<tr id="group_{LOOP.patient_id}">
			<td class="text-center">
				{LOOP.stt}
			</td>
			<td class="text-left">
				<a href="{LOOP.link}">{LOOP.full_name}</a>
			</td>
			<td class="text-center">
				{LOOP.gender}
			</td>
			<td class="text-center">
				{LOOP.age}
			</td>
			<td class="text-center">
				{LOOP.username}
			</td>
			<td class="text-center">
				{LOOP.address}
			</td>
			 
		</tr>
		<!-- END: loop -->
	</tbody>
</table>
<!-- END: data -->
<!-- BEGIN: no_data -->
<table class="table table-bordered table-hover">
 
	<tbody>
		
		<tr>
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