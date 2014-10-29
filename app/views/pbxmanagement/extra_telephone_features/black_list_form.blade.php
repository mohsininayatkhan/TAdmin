@section('form_title')
	<h2 class="what">Edit Blacklist</h2>
@stop

@section('form_content')
	
	<section class="tab_wrap no_tab">
	
		<div class="tab_content_pad active">
			
			@include('layout.sfield_open')
				
				<!-- Callgroup Form -->
				<form name="frmBlacklist" id="frmBlacklist" class="validate">
				<input type="hidden" name="blacklist_id" id="blacklist_id" value="" />
				<div class="col_wide">
					<?php 
					$extension_options = array('ALL' => 'ALL');
					foreach($data['extensions'] as $extension) {
						$extension_options[$extension['account_id']] = $extension['name'].' - '.$extension['extennumber'];
					}
					?>
					@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Extension', 'id' => 'exten', 'options' => $extension_options))
					@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Blacklist Number', 'id' => 'phonenumber'))
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