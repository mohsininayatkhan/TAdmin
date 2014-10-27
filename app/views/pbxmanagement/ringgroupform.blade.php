@section('form_title')
	<h2 class="what">Edit Call Group</h2>
@stop

@section('form_content')
	
	<section class="tab_wrap no_tab">
	
		<div class="tab_content_pad active">
			
			<form name="form" id="form" class="validate">
				@include('layout.sfield_open')
					
					<input type="hidden" name="ringgroup_id" id="ringgroup_id" value="" />
					<div class="col1">
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Name', 'id' => 'name'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'CLI Prefix', 'id' => 'cli_name_prefix'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'number', 'label' => 'Ring Duration', 'id' => 'ringtime'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Ringgroup Strategy', 'id' => 'strategy', 'options' => array('RINGALL' => 'Ringall', 'HUNT' => 'Hunt', 'MEMORYHUNT' => 'Memory Hunt', 'FIRSTAVAILABLE' => 'First Available')))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Ringgroup Number', 'id' => 'ringgroup_num', 'maxlength' => 6, 'hint' => 'This is auto-generated, change only if you want to use custom number'))
					</div>
					<div class="col2">
						<?php 
						$failovermsg = array('' => '');
						foreach($data['failovermsg'] as $dd) {
							$failovermsg[$dd['announcement_number']] = $dd['name'].' - '.$dd['announcement_number'];
						}
						?>
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Failover Message', 'id' => 'failover_announcement_no', 'options' => $failovermsg))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Failover App', 'id' => 'failover_app', 'options' => array('' => 'None', 'ANNOUNCEMENT' => 'Announcement', 'EXTEN' => 'Extension', 'EXTERNAL' => 'External', 'QUEUE' => 'Queue', 'RINGGROUP' => 'RingGroup', 'HANGUP' => 'Terminate Call', 'VOICEMAIL' => 'Voicemail')))						
						<div id="fo_other">
							@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Failover App No.', 'id' => 'failover_appnumber'))
						</div>
						<div id="fo_external" style="display:none">
							@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Failover App No.', 'id' => 'external_failover_appnumber'))
						</div>
					</div>
					
					
				@include('layout.sfield_close')
				
				<span class="button_wrap topspace">
					<input type="submit" value="Save">
					<input type="button" value="Cancel" class="gray" onclick="hidePopup()">
				</span>
				<i class="global error"></i>
				<div class="clearfix"></div>
			</form>
		</div>
		
	</section>
	
@stop


<!-- popup form -->
<div class="floating_box list hidden">

	<div class="container sform_wrap">

		<div class="sform_pad">

			<h2 class="what">RingGroup - <span class="module"></span></h2>

			<section class="tab_wrap">
					
				<div class="tab_link_wrap">
					<span class="tab_link active" data-content="tabList">List</span>
					<span class="tab_link" data-content="tabAdd">Add/Edit</span>
					
					<div class="clearfix"></div>
				</div>
				
				<div class="tab_content">
					
					<div class="tab_content_pad active" id="tabList">
						
						<section class="table_wrap">
							<div id="gridList"></div>
							
							<nav class="dropdown popup">
								<ul>
									<li><a href="javascript:void(0)" onclick="editList()">Edit</a><i class="triangle"></i></li>
									<li><a href="javascript:void(0)" onclick="deleteList()">Delete</a></li>
								</ul>
							</nav>
						</section>
						
						<span class="button_wrap topspace">
							<input type="button" value="Close" class="gray" onclick="hidePopup()">
						</span>
						
						<div class="clearfix"></div>
				
					</div>

					
					<div class="tab_content_pad" id="tabAdd">
						<form name="formList" id="formList" class="validate">
							
							<h2 class="tab_content_title">Add to RingGroup List</h2>
							
							@include('layout.sfield_open')
								<input type="hidden" name="name" id="name" value="" />
								<div class="col_wide">
									@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Extension Type', 'id' => 'extentype', 'options' => array('EXTEN' => 'Extension', 'EXTERNAL' => 'External')))
									<div id="dst_extension">
										<?php 
										$extensions = array();
										foreach($data['extensions'] as $dd) {
											$extensions[$dd['extennumber']] = $dd['extennumber'];
										}
										?>
										@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Destination', 'id' => 'dst_number', 'options' => $extensions))
									</div>
									<div id="dst_external" style="display:none">
										@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Destination', 'id' => 'external_dst_number'))
									</div>
									
								</div>
							
							@include('layout.sfield_close')
							
							<span class="button_wrap topspace">
								<input type="submit" value="Save">
								<input type="button" value="Close" class="gray" onclick="hidePopup()">
							</span>
							<i class="global error"></i>
							<div class="clearfix"></div>
							
						</form>
					</div>
					
				</div>

			</section>
			
		</div>

		<span class="ie_shadow_top"></span>
		<span class="ie_shadow_bottom"></span>
		<span class="ie_shadow_left"></span>
		<span class="ie_shadow_right"></span>
	</div>
	
</div> <!-- end floating_box -->