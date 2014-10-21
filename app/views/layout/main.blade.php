@extends('layout.master')

@section('content')

	@include('layout.top_pane')

	<div class="main_pane">

		<div id="content">
			
			@include('layout.left_pane')

			<div class="main_pane_pad">
			
				<div class="centered">
				
					@yield('main_content')
									
				</div>
				
				<div class="expanded">
				
					@yield('wide_content')
					
				</div>
			
			</div> <!-- end main_pane_pad -->

		</div> <!-- end content -->
		
	</div> <!-- end main_pane -->
	
	<!-- popup form -->
	<div class="floating_box hidden">

		<div class="container sform_wrap">

			<div class="sform_pad">

				@yield('form_title')

				@yield('form_content')
				
			</div>

			<span class="ie_shadow_top"></span>
			<span class="ie_shadow_bottom"></span>
			<span class="ie_shadow_left"></span>
			<span class="ie_shadow_right"></span>
		</div>
		
	</div> <!-- end floating_box -->
	
	<div class="overlay"></div>	
@stop

