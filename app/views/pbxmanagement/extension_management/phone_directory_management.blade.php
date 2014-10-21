@extends('layout.main')

@section('main_content')

	@include('layout.tools_top', array('parent' => 'Extension Management'))
	
	@include('layout.sfield_open', array('extra_cls' => 'mini_filter'))
	
	@include('layout.sfield_generator', array('type' => 'select', 'label' => 'Select Account Name', 'id' => 'account_name', 'options_default' => 'Public Phone Directory', 'options' => array("test1" => "test1")))
		
	@include('layout.sfield_close')
	
	<section class="table_wrap small scroll_wrap">
		<table>
			<tr class="row head">
				<td>Account Name</td>
				<td>Contact Name</td>
				<td>Company</td>
				<td>Contact Phone No</td>
				<td>Home Phone No</td>
				<td>Work Phone No</td>
				<td>Mobile</td>
				<td width="5%"></td>
			</tr>
			<tr class="row no_record">
				<td colspan="8">No record found</td>
			</tr>
		</table>
	</section>

@stop