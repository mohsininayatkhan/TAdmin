@if (isset($type))

	@if (isset($wrap) && $wrap)
		<div class="{{ $wrap }}">
	@endif
	
	@if (isset($label) && $label)
		<label for="{{ isset($id) ? (is_array($id) ? $id[0] : $id) : '' }}">{{ $label }}</label>
	@endif
	
	{{-- select with radio --}}
	@if (is_array($type))
		<label for="{{ $id[0] }}">
			<span class="radio_wrap"><input type="radio" name="{{ isset($name[0]) ? $name[0] : $id[0] }}" id="{{ $id[0] }}" {{ isset($ischecked) && $ischecked ? 'checked' : '' }} /></span>
			{{ $text or '' }}
		</label>
		<span class="field_wrap select_wrap">
			<select name="{{ isset($name[1]) ? $name[1] : $id[1] }}" id="{{ $id[1] }}">
				@if (isset($options_default) && $options_default)
					<option value="">{{ $options_default }}</option>
				@endif
				
				@if (isset($options) && $options)					
					@for ($i = 0; $i < count($options); $i++)
						@if (isset($options[$i]))
							<option value="{{ $options[$i] }}">{{ $options[$i] }}</option>
							<?php unset($options[$i]);//unset from array ?>
						@endif
					@endfor
					
					@if (count($options))
						@foreach ($options as $key => $option)
							<option value="{{ $key }}">{{ $option }}</option>
						@endforeach
					@endif
				@endif
			</select>
		</span>
	@endif
	
	@if (!is_array($type) && $type == 'select')
	
		{{-- usually for duration --}}
		@if (is_array($id) && !is_array($type))
			<div class="field_wrap">
				<span class="clone_wrap1">
					<span class="select_wrap rounded">
						<select name="{{ $id[0] }}" id="{{ $id[0] }}">
							<option value=">=">>=</option>
							<option value="<="><=</option>
							<option value="=">=</option>
						</select>
					</span>
				</span>
				<span class="clone_wrap2">
					<span class="select_wrap rounded">
						<select name="{{ $id[1] }}" id="{{ $id[1] }}">
							@if (isset($options_default) && $options_default)
								<option value="">{{ $options_default }}</option>
							@endif
							
							@if (isset($options) && $options)					
								@for ($i = 0; $i < count($options); $i++)
									@if (isset($options[$i]))
										<option value="{{ $options[$i] }}">{{ $options[$i] }}</option>
										<?php unset($options[$i]);//unset from array ?>
									@endif
								@endfor
								
								@if (count($options))
									@foreach ($options as $key => $option)
										<option value="{{ $key }}">{{ $option }}</option>
									@endforeach
								@endif
							@endif
						</select>
					</span>
				</span>
				
				<div class="clearfix"></div>
			</div>		
		@else
			<span class="field_wrap select_wrap">
				<select name="{{ $name or $id }}" id="{{ $id or '' }}" {{ isset($onchange) ? "onchange=$onchange": '' }}>
					@if (isset($options_default) && $options_default)
						<option value="">{{ $options_default }}</option>
					@endif

					@if (isset($options) && $options)					
						<?php /*@for ($i = 0; $i < count($options); $i++)
							@if (isset($options[$i]))
								<option value="{{ $options[$i] }}">{{ $options[$i] }}</option>
								<?php unset($options[$i]);//unset from array ?>
							@endif
						@endfor*/
						?>
						
						@if (count($options))
							@foreach ($options as $key => $option)
								<option value="{{ $key }}">{{ $option }}</option>
							@endforeach
						@endif
					@endif
				</select>
			</span>
		@endif
	@endif

	@if (!is_array($type) && ($type == 'checkbox' || $type == 'radio'))
		<{{ $subwrap_tag or 'span' }} class="{{ $subwrap_cls or 'field_wrap' }}">
			<span class="{{ $type }}_wrap"><input type="{{ $type }}" name="{{ $name or $id }}" id="{{ $id or '' }}" value="{{ $value or '' }}" {{ isset($cls) ? 'class='.$cls : '' }} {{ isset($ischecked) && $ischecked ? 'checked' : '' }} /></span>
			{{ $text or '' }}
		</{{ $subwrap_tag or 'span' }}>
	@endif
	
	@if (!is_array($type) && $type == 'text')
		<span class="field_wrap">
			<input type="text" name="{{ $name or $id }}" id="{{ $id or '' }}" />{{ isset($hint) ? '<i class="hint">'.$hint.'</i></span>' : '' }}
		</span>
	@endif
	
	@if (!is_array($type) && $type == 'file')
		<span class="field_wrap file">
			<input type="file" name="{{ $name or $id }}" id="{{ $id or '' }}" />{{ isset($hint) ? '<i class="hint">'.$hint.'</i></span>' : '' }}
		</span>
	@endif
	
	@if (isset($wrap) && $wrap)
		</div>
	@endif
	
@endif