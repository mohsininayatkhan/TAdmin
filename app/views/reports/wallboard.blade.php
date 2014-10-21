@extends('layout.main')

@section('main_content')
	
	@include('layout.tools_top', array('parent' => 'Queue Reports'))
	
	@include('layout.sform_open')
		
		@include('layout.sfield_open', array('subtitle' => 'Search Wallboard', 'extra_cls' => 'bordered'))
					
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'text', 'label' => 'Enter Wallboard Name', 'id' => 'wallboard'))
			@include('layout.sfield_generator', array('wrap' => 'col_wide', 'type' => 'checkbox', 'subwrap_tag' => 'label', 'subwrap_cls' => 'wide', 'id' => 'cqueues', 'text' => 'Queues with calls in waiting only'))
			
			<div class="col_wide">
				@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => ['radio', 'select'], 'id' => ['rqueuegroup', 'queue_group'], 'name' => ['rqueues', 'queue_group'], 'options' => array("Quickdox Ltd" => "Quickdox Ltd"), 'text' => 'Queue Group', 'ischecked' => true))
				
				<span class="or float semibold">OR</span>
				
				@include('layout.sfield_generator', array('type' => 'radio', 'subwrap_tag' => 'label', 'subwrap_cls' => '', 'id' => 'rqueues', 'name' => 'rqueues', 'text' => 'Select Queues'))
			</div>
			
			<div class="col_wide checkbox_parent">			
				<label for="">Select Columns:</label>
				
				<label class="field_wrap"><span class="checkbox_wrap"><input type="checkbox" class="checkAll" data-child="columns" data-parent=".checkbox_parent" /></span> All</label>
				
				<div class="checkbox_group wide">
					@include('layout.sfield_generator', array('type' => 'checkbox', 'subwrap_tag' => 'label', 'name' => 'columns[]', 'value' => 'queue', 'cls' => 'columns', 'text' => 'Queue'))
					@include('layout.sfield_generator', array('type' => 'checkbox', 'subwrap_tag' => 'label', 'name' => 'columns[]', 'value' => 'offered', 'cls' => 'columns', 'text' => 'Offered'))
					@include('layout.sfield_generator', array('type' => 'checkbox', 'subwrap_tag' => 'label', 'name' => 'columns[]', 'value' => 'answered', 'cls' => 'columns', 'text' => 'Answered'))
					@include('layout.sfield_generator', array('type' => 'checkbox', 'subwrap_tag' => 'label', 'name' => 'columns[]', 'value' => 'abandoned', 'cls' => 'columns', 'text' => 'Abandoned'))
					@include('layout.sfield_generator', array('type' => 'checkbox', 'subwrap_tag' => 'label', 'name' => 'columns[]', 'value' => 'callwaiting', 'cls' => 'columns', 'text' => 'Call Waiting'))
					@include('layout.sfield_generator', array('type' => 'checkbox', 'subwrap_tag' => 'label', 'name' => 'columns[]', 'value' => 'longestinqueue', 'cls' => 'columns', 'text' => 'Longest in Queue'))
					@include('layout.sfield_generator', array('type' => 'checkbox', 'subwrap_tag' => 'label', 'name' => 'columns[]', 'value' => 'currentcalls', 'cls' => 'columns', 'text' => 'Current Calls'))
					@include('layout.sfield_generator', array('type' => 'checkbox', 'subwrap_tag' => 'label', 'name' => 'columns[]', 'value' => 'freeagents', 'cls' => 'columns', 'text' => 'Free Agents'))
					@include('layout.sfield_generator', array('type' => 'checkbox', 'subwrap_tag' => 'label', 'name' => 'columns[]', 'value' => 'totalagents', 'cls' => 'columns', 'text' => 'Total Agents'))
				</div>
				
			</div>
				
		@include('layout.sfield_close', array('subtitle' => true, 'buttons' => true, 'submit_text' => 'Run Wallboard'))
			
	@include('layout.sform_close')
	
@stop