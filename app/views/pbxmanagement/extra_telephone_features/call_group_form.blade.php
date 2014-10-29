@section('form_title')
	<h2 class="what">Edit Call Group</h2>
@stop

@section('form_content')
	
	<section class="tab_wrap no_tab">
	
		<div class="tab_content_pad active">
			
			@include('layout.sfield_open')
				
				<!-- Callgroup Form -->
				<form name="frmCallgroup" id="frmCallgroup" class="validate">
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

<!-- popup form for callpickup -->
<div class="floating_box list hidden">
	<div class="container sform_wrap">
		<div class="sform_pad">
			<section class="tab_wrap no_tab">
			<div class="tab_content_pad active">
				<h2 class="tab_content_title">Add to call pickup list</h2>
				@include('layout.sfield_open')
					<!-- Callpick list Form -->
					<form name="frmCallpickup" id="frmCallpickup" class="validate">
						<input type="hidden" name="callpickup_id" id="callpickup_id" value="" />
						 <input type="hidden"  value="EXTEN" name="exten_type" id="exten_type" />
						<div class="col_wide">
							<?php 
							$extension_options = array('' => '');
							foreach($data['extensions'] as $extension) {
								$extension_options[$extension['extennumber']] = $extension['name'].' - '.$extension['extennumber'];
							}
							?>
							@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Extension Number', 'id' => 'exten', 'options' => $extension_options))
						</div>
						@include('layout.sfield_close')
						
						<span class="button_wrap topspace">
						<input id="sbtAddBtn" name="sbtAddBtn" type="button" value="Add">
						<input type="button" value="Cancel" class="gray" onclick="hidePopup()">
						</span>
					</form>
				<!-- End of Callpickup list form -->
				<div class="clearfix"></div><br />
				
				<!-- GRID -->
				<section id="gridsection" class="table_wrap">
					<div id="gridList"></div>					
					<nav class="dropdown popup">
						<ul>
							<li><a href="javascript:void(0)" onclick="deleteCalllist()">Delete</a><i class="triangle"></i></li>
						</ul>
					</nav>
				</section>
				<span class="button_wrap topspace">
					<input type="button" value="Close" class="gray" onclick="hidePopup()">
				</span>
				
				<div class="clearfix"></div>
			</div>
		
	</section>
		</div>
		<span class="ie_shadow_top"></span>
		<span class="ie_shadow_bottom"></span>
		<span class="ie_shadow_left"></span>
		<span class="ie_shadow_right"></span>
	</div>
	
	
</div> <!-- end floating_box -->