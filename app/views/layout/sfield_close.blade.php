@if (isset($buttons) && $buttons)
	<div class="clearfix"></div>
			
	<div class="button_wrap topspace">
		<input type="submit" value="{{ $submit_text or 'Search' }}">
		<input type="reset" value="Reset" class="gray">
	</div>
@endif

@if (isset($subtitle) && $subtitle)
	</div> <!-- sfield_pad end -->
@else
	<div class="clearfix"></div>
@endif
	
</section>