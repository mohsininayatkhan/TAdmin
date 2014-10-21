@extends('layout.main')

@section('main_content')
	
	@include('layout.tools_top', array('parent' => 'Telephony Management'))
	
	<section class="table_wrap small scroll_wrap">
		<table>
			<tr class="row head">
				<td>Name</td>
				<td>CLI Prefix</td>
				<td>Number</td>
				<td>Strategy</td>
				<td width="5%"></td>
			</tr>
			@for ($i = 0; $i < 5; $i++)
				<tr class="row">
					<td>Ringgroup{{ $i + 1 }}</td>
					<td></td>
					<td class="searchExtension">{{ substr(str_shuffle("0123456789"), 0, 4) }}</td>
					<td>RINGALL</td>
					<td><a href="javascript:void(0)" class="dropdownSetter btn gray icon_wrap_block icon_gear_small" data-dropdown="actionSetter">Actions<i class="icon_arrow_gray right"></i></a></td>
				</tr>
			@endfor
		</table>
		
		<nav class="dropdown" id="actionSetter">
			<ul>
				<li><a href="javascript:void(0)" onClick="openPopup()">Edit</a><i class="triangle"></i></li>
				<li><a href="javascript:void(0)">Delete</a></li>
			</ul>
		</nav>
	</section>

@stop

<!-- popup form -->
@include('pbxmanagement.telephony_management.ring_group_form')