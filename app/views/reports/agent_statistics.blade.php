@extends('layout.main')

@section('main_content')
	
	@include('layout.tools_top', array('parent' => 'Queue Reports'))
	
	@include('layout.sform_open')
		
		@include('layout.sfield_open', array('subtitle' => 'Search Agent Statistics', 'extra_cls' => 'bordered'))
					
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'select', 'label' => 'From', 'id' => 'from', 'options' => array('2014-08-15' => '2014-08-15')))
			@include('layout.sfield_generator', array('wrap' => 'col2', 'type' => 'select', 'label' => 'To', 'id' => 'to', 'options' => array('2014-08-15' => '2014-08-15')))
			
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => ['radio', 'select'], 'id' => ['ragent', 'agent'], 'name' => ['radio_agent', 'agent'], 'options_default' => 'Anyone', 'text' => 'Agent', 'ischecked' => true))
			
			@include('layout.sfield_generator', array('wrap' => 'col2', 'type' => ['radio', 'select'], 'id' => ['ragent_group', 'agent_group'], 'name' => ['radio_agent', 'agent_group'], 'text' => 'Agent Group'))
				
		@include('layout.sfield_close', array('subtitle' => true, 'buttons' => true))
			
	@include('layout.sform_close')
	
@stop

@section('wide_content')

	<section class="table_wrap wide scroll_wrap">
		<table>
			<tr class="row head">
				<td>ID</td>
				<td>Agent</td>
				<td>Extension</td>
				<td width="64">Date</td>
				<td>Inbound</td>
				<td>Inbound Duration</td>
				<td>Inbound Avg</td>
				<td>Outbound</td>
				<td>Outbound Duration</td>
				<td>Outbound Avg</td>
				<td>Internal</td>
				<td>Internal Duration</td>
				<td>Internal Avg</td>
				<td>Total Calls</td>
				<td>On Email</td>
				<td>On Training</td>
				<td>Auto ACW</td>
				<td>ACW</td>
				<td>Available</td>
				<td>Admin</td>
				<td>Break</td>
				<td>Lunch</td>
				<td>On Login</td>
				<td>Outbound</td>
				<td>Missed Calls</td>
			</tr>
			@for ($i = 1; $i <= 5; $i++)
				<tr class="row">
					<td>{{ $i }}</td>
					<td>{{ ucwords(substr(str_shuffle("abcdefjhijklmnopqrstuvwxyz"), 0, 6)) }}</td>
					<td class="searchExtension">{{ substr(str_shuffle("0123456789"), 0, 4) }}</td>
					<td>2014-08-1{{ substr(str_shuffle("123456789"), 0, 1) }}</td>
					<td>{{ substr(str_shuffle("123456789"), 0, 1) }}</td>
					<td>-</td>
					<td>-</td>
					<td>{{ substr(str_shuffle("123456789"), 0, 1) }}</td>
					<td>-</td>
					<td>-</td>
					<td>{{ substr(str_shuffle("123456789"), 0, 1) }}</td>
					<td>-</td>
					<td>-</td>
					<td>{{ substr(str_shuffle("123456789"), 0, 1) }}</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
				</tr>
			@endfor
		</table>
	</section>
	
@stop