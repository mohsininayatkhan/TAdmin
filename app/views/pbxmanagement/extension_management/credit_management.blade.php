@extends('layout.main')

@section('main_content')

	@include('layout.tools_top', array('parent' => 'Extension Management'))
	
	@include('layout.sform_open')

		@include('layout.sfield_open', array('title' => 'Transfer Credit'))
		
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'select', 'label' => 'Credit Source', 'id' => 'credit_source', 'options_default' => 'Tenant Credit'))
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'select', 'label' => 'Credit Destination', 'id' => 'credit_destination', 'options_default' => 'Extension Credit'))
			@include('layout.sfield_generator', array('wrap' => 'col2', 'type' => 'select', 'label' => 'Extension', 'id' => 'extension', 'options_default' => 'Select extension'))
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'text', 'label' => 'Amount to Transfer', 'id' => 'amount_to_transfer'))
			
		@include('layout.sfield_close')
		
		<div class="table_wrap inside">
			<table>
				<tr class="row head bold">
					<td></td>
					<td>Current Credit</td>
					<td>New Credit</td>
				</tr>
				<tr class="row">
					<td class="semibold">Source</td>
					<td>1000.0000</td>
					<td>1000.0000</td>
				</tr>
				<tr class="row">
					<td class="semibold">Destination</td>
					<td></td>
					<td></td>
				</tr>
			</table>
		</div>
		
		@include('layout.sfield_open', array('title' => 'Credit Transfer History', 'subtitle' => 'Search Credit History', 'extra_cls' => 'bordered'))
			
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'select', 'label' => 'From', 'id' => 'from', 'options' => array('2014-08-15' => '2014-08-15')))
			@include('layout.sfield_generator', array('wrap' => 'col2', 'type' => 'select', 'label' => 'To', 'id' => 'to', 'options' => array('2014-08-15' => '2014-08-15')))
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'select', 'label' => 'Account', 'id' => 'accout', 'options_default' => 'All'))

		@include('layout.sfield_close', array('subtitle' => true, 'buttons' => true))
			
	@include('layout.sform_close')

@stop