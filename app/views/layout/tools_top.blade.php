<aside class="top_tools">
	
	<nav class="breadcrumb">
		<a href="/">Home</a>
		<a href="/{{ URL::segment(0) }}">{{ ucwords(preg_replace(array('/pbx/', "/-/"), array('PBX', " "), URL::segment(0))) }}</a>
		@if (isset($parent))
			<a href="/{{ URL::segment(0) }}#{{ str_replace(' ', '', $parent) }}">{{ $parent }}</a>
		@endif
		<span>{{ ucwords(str_replace("-", " ", URL::segment(1))) }}</span>
	</nav>
	
	@if (!isset($no_tools))
		<div class="right_pane">
			<div class="float search rounded2"><input type="text" id="search" placeholder="Type here to search {{ isset($current) ? $current : '' }}" /></div>
			
			<a href="" class="btn gradient icon_wrap new-btn">
				New {{ isset($current) ? $current : '' }}
				<i class="icon_new_ext"></i>
			</a>
			
			<a href="" class="btn gradient icon_wrap">
				Refresh
				<i class="icon_refresh"></i>
			</a>
			
			<div class="clearfix"></div>
		</div>
	@endif
	
</aside>

<h2 class="title">{{ ucwords(str_replace("-", " ", URL::segment(1))) }}</h2>