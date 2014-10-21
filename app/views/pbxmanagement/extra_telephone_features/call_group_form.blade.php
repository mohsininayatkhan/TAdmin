@section('form_title')
	<h2 class="what">Edit Call Group</h2>
@stop

@section('form_content')
	
	<section class="tab_wrap no_tab">
	
		<div class="tab_content_pad active">
			
			@include('layout.sfield_open')
				
				<!-- Callgroup Form -->
				<form name="frmCallgroup" id="frmCallgroup">
				<input type="hidden" name="callpickup_id" id="callpickup_id" value="" />
				<div class="col_wide">
					@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Name', 'id' => 'name'))
					@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Description', 'id' => 'description'))
					@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Call Pickup Code', 'id' => 'code'))
				</div>
				
				
			@include('layout.sfield_close')
				
			<span class="button_wrap topspace">
				<input id="sbtBtn" name="sbtBtn" type="button" value="Save">
				<input type="button" value="Cancel" class="gray" onclick="hidePopup()">
			</span>
			</form>
			<!-- End of Callgroup Form -->
			
			<div class="clearfix"></div>
		</div>
		
	</section>
	
@stop