<div class="blue_box" id="{{ isset($title) ? preg_replace('/[\s\/]+/', '', ucwords($title)) : '' }}">
			
	<div class="blue_box_pad">
	
		<h2 class="blue_box_title icon_arrow_blue">{{ isset($title) ? ucwords($title) : '' }}</h2>
		
		@if (isset($menus))
			<ul class="grid_wrap">
				@for ($i = 0; $i < count($menus); $i++)
					<li>
						<a href="/{{ URL::segment(0) }}/{{ isset($menus[$i][2]) ? $menus[$i][2] : strtolower(preg_replace('/[\s\/]+/', '-', $menus[$i][0])) }}" class="{{ isset($menus[$i][3]) ? $menus[$i][3] : 'icon_'.($cssref ? $cssref : 'default').'_'.($i+1) }}">
							<h2 class="grid_title">{{ preg_replace('/( \/ )/', '/', ucwords($menus[$i][0])) }}</h2>
							<p class="grid_desc">{{ $menus[$i][1] }}</p>
						</a>
					</li>
				@endfor
			</ul>
			
			<div class="clearfix"></div>
		@endif

	</div>
	
</div>