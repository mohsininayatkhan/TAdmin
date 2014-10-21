@section('form_title')
	<h2 class="what">Edit Ring Group</h2>
@stop

@section('form_content')
	
	<section class="tab_wrap no_tab">
	
		<div class="tab_content_pad active">
			
			@include('layout.sfield_open')
				
				<div class="col_wide">
					@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Name', 'id' => 'name'))
					@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'CLI Prefix', 'id' => 'cliprefix'))
					@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Ringgroup Number', 'id' => 'number'))
					@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Ringgroup Strategy', 'id' => 'strategy', 'options' => array('Ringall')))
					@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Ringgroup Duration', 'id' => 'duration', 'options' => array(100, 120, 150)))
					@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Failover Message', 'id' => 'failover_message'))
					@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Failover App', 'id' => 'failover_app', 'options' => array('Voicemail')))
					@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Failover App No.', 'id' => 'failover_app_no'))
				</div>
				
			@include('layout.sfield_close')
				
			<span class="button_wrap topspace">
				<input type="submit" value="Save">
				<input type="button" value="Cancel" class="gray" onclick="hidePopup()">
			</span>
			
			<div class="clearfix"></div>
		</div>
		
	</section>
	
@stop