@extends('layout.main')

@section('main_content')

	@include('layout.tools_top', array('parent' => 'Admin Reports'))
	
	@include('layout.sform_open')

		@include('layout.sfield_open', array('subtitle' => 'Trunk Group Calls', 'extra_cls' => 'bordered'))
				
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'select', 'label' => 'From', 'id' => 'from', 'options' => array('2014-08-15' => '2014-08-15')))
			@include('layout.sfield_generator', array('wrap' => 'col2', 'type' => 'select', 'label' => 'To', 'id' => 'to', 'options' => array('2014-08-15' => '2014-08-15')))
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'select', 'label' => 'Trunk Group', 'id' => 'trunk_group', 'options_default' => 'Any'))
			
		@include('layout.sfield_close', array('subtitle' => true, 'buttons' => true))
			
		<div class="table_wrap topspace">
			<table>
				<tr class="row head bold">
					<td width="40%">Title</td>
					<td>Total</td>
				</tr>
				<tr class="row">
					<td class="col1 semibold">Total Calls</td>
					<td>2</td>
				</tr>
				<tr class="row">
					<td class="col1 semibold">Total Duration</td>
					<td>05:40:40</td>
				</tr>
				<tr class="row">
					<td class="col1 semibold">Total Answered</td>
					<td>2</td>
				</tr>
			</table>
		</div>
			
	@include('layout.sform_close')

@stop

@section('wide_content')
	
	@include('layout.sfield_open', array('extra_cls' => 'mini_filter tgr'))
	
	@include('layout.sfield_generator', array('type' => 'select', 'label' => 'Agent Group', 'id' => 'agent_group', 'options' => array("Quickdox" => "Quickdox")))
		
	@include('layout.sfield_close')
		
	<section class="table_wrap medium scroll_wrap">

		<table>
			<tr class="row head">
				<td>Queue</td>
				<td>Caller ID</td>
				<td>Status</td>
				<td>Wait Time</td>
				<td>Call Time</td>
				<td>Agent</td>
				<td>Extension</td>
				<td width="186">Action</td>
			</tr>
			<tr class="row">
				<td>QD Queue &lt;506&gt;</td>
				<td>1204689375&lt;1045&gt;</td>
				<td>Answered</td>
				<td>00:00:21</td>
				<td>00:00:26</td>
				<td>Rach</td>
				<td class="searchExtension">1028</td>
				<td class="btn_group"><a href="javascript:void(0)" class="btn gray icon_wrap">Listen in <i class="icon_sound"></i></a><a href="javascript:void(0)" class="btn gray icon_wrap">Whisper <i class="icon_whisper"></i></a></td>
			</tr>
			@for ($i = 1; $i <= 2; $i++)
				<tr class="row">
					<td>QD Queue <{{ substr(str_shuffle("0123456789"), 0, 3) }}></td>
					<td>{{ substr(str_shuffle("0123456789"), 0, 11) }}<{{ substr(str_shuffle("0123456789"), 0, 4) }}></td>
					<td>Answered</td>
					<td>00:00:2{{ substr(str_shuffle("0123456789"), 0, 1) }}</td>
					<td><span class="alert">838:59:59</span></td>
					<td>{{ ucwords(substr(str_shuffle("abcdefjhijklmnopqrstuvwxyz"), 0, 6)) }}</td>
					<td class="searchExtension">{{ substr(str_shuffle("0123456789"), 0, 4) }}</td>
					<td class="btn_group"><a href="javascript:void(0)" class="btn gray icon_wrap">Listen in <i class="icon_sound"></i></a><a href="javascript:void(0)" class="btn gray icon_wrap">Whisper <i class="icon_whisper"></i></a></td>
				</tr>
			@endfor
		</table>

	</section>

	<section class="table_wrap small scroll_wrap topspace">

		<table>
			<tr class="row head">
				<td>Agent</td>
				<td>Extension</td>
				<td>Status</td>
				<td>Duration</td>
				<td>Call Details</td>
				<td>Caller ID</td>
			</tr>
			<tr class="row">
				<td>xSpare-1030</td>
				<td class="searchExtension">1030</td>
				<td>Login</td>
				<td>00:00:21</td>
				<td></td>
				<td></td>
			</tr>
		</table>
		
	</section>

@stop