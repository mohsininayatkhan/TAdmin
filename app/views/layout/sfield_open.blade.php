@if (isset($title) && $title)
	<h2 class="sform_title">{{ $title }}</h2>
@endif

<section class="sfield_wrap {{ $extra_cls or '' }}">

	@if (isset($subtitle) && $subtitle)
		<h2 class="sfield_title"><span class="btn gray icon_wrap_notext"><i class="icon_arrow_blue"></i><i class="icon_arrow_blue_r"></i></span>{{ $subtitle }}</h2>
			
		<div class="sfield_pad">
	@endif