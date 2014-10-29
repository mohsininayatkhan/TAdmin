@section('form_title')
	<h2 class="what">New Music On Hold Group</h2>
@stop

@section('form_content')
	
	<section class="tab_wrap no_tab">
	
		<div class="tab_content_pad active">
			
			<form name="form" id="form" class="validate">
				@include('layout.sfield_open')
					
					<div class="col_wide">
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Group Name', 'id' => 'name'))
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

			<h2 class="what">MOH Group - <span class="module"></span></h2>

			<section class="tab_wrap">
					
				<div class="tab_link_wrap">
					<span class="tab_link active" data-content="tabList">Files</span>
					<span class="tab_link" data-content="tabAdd">Upload File</span>
					
					<div class="clearfix"></div>
				</div>
				
				<div class="tab_content">
					
					<div class="tab_content_pad active" id="tabList">
						
						<section class="table_wrap">
							<div id="gridList"></div>
							
							<nav class="dropdown popup">
								<ul>
									<li><a href="javascript:void(0)" onclick="downloadFile()">Download</a><i class="triangle"></i></li>
									<li><a href="javascript:void(0)" onclick="deleteFile()">Delete</a></li>
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
							
							<h2 class="tab_content_title">Upload file to group</h2>
							
							@include('layout.sfield_open')
								<input type="hidden" name="musiconhold_id" id="musiconhold_id" value="" />
								<div class="col_wide">
									<div class="crow">
										<label for="name">Choose File<br/>(wav, mp3 format)</label>
										<span class="field_wrap">
											<input type="file" id="moh_file" name="moh_file" />
										</span>
									</div>
								</div>
							
							@include('layout.sfield_close')
							
							<span class="button_wrap topspace">
								<input type="submit" value="Save">
								<input type="button" value="Close" class="gray" onclick="hidePopup()">
							</span>

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