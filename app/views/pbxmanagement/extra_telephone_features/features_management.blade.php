@extends('layout.main')

@section('main_content')
	
	@include('layout.tools_top', array('parent' => 'Extra Telephone Features'))
	
	<section class="table_wrap small scroll_wrap">
		<table>
			<tr class="row head">
				<td>Feature</td>
				<td width="25%">Feature Code</td>
				<td width="74">Use Default</td>
				<td width="138">Enabled</td>
			</tr>
			<tbody>
				<tr class="row all bold">
					<td>QUEUE</td>
					<td></td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="checkAll" data-child="useDefault" data-parent="tbody" checked /></span>
						All
					</td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="checkAll" data-child="enabled" data-parent="tbody" checked /></span>
						<a href="javascript:void(0)" class="btn white">Save All <span class="raquo">&raquo;</span></a>
					</td>
				</tr>
				<tr class="row">
					<td>Agent After Call Work</td>
					<td><span class="field_wrap"><input type="text" value="*{{ substr(str_shuffle('0123456789'), 0, 2) }}" /></span></td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="useDefault" checked /></span>
					</td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="enabled" checked /></span>
						<a href="javascript:void(0)" class="btn gray">Save</a>
					</td>
				</tr>
				<tr class="row">
					<td>Agent Login</td>
					<td><span class="field_wrap"><input type="text" value="*{{ substr(str_shuffle('0123456789'), 0, 2) }}" /></span></td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="useDefault" checked /></span>
					</td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="enabled" checked /></span>
						<a href="javascript:void(0)" class="btn gray">Save</a>
					</td>
				</tr>
				<tr class="row">
					<td>Agent Logoff</td>
					<td><span class="field_wrap"><input type="text" value="*{{ substr(str_shuffle('0123456789'), 0, 2) }}" /></span></td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="useDefault" checked /></span>
					</td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="enabled" checked /></span>
						<a href="javascript:void(0)" class="btn gray">Save</a>
					</td>
				</tr>
				<tr class="row">
					<td>Agent on Admin Work</td>
					<td><span class="field_wrap"><input type="text" value="*{{ substr(str_shuffle('0123456789'), 0, 2) }}" /></span></td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="useDefault" checked /></span>
					</td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="enabled" checked /></span>
						<a href="javascript:void(0)" class="btn gray">Save</a>
					</td>
				</tr>
				<tr class="row">
					<td>Agent on Break</td>
					<td><span class="field_wrap"><input type="text" value="*{{ substr(str_shuffle('0123456789'), 0, 2) }}" /></span></td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="useDefault" checked /></span>
					</td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="enabled" checked /></span>
						<a href="javascript:void(0)" class="btn gray">Save</a>
					</td>
				</tr>
				<tr class="row">
					<td>Agent After Call Work</td>
					<td><span class="field_wrap"><input type="text" value="*{{ substr(str_shuffle('0123456789'), 0, 2) }}" /></span></td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="useDefault" checked /></span>
					</td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="enabled" checked /></span>
						<a href="javascript:void(0)" class="btn gray">Save</a>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr class="row all bold">
					<td>General</td>
					<td></td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="checkAll" data-child="useDefault" data-parent="tbody" checked /></span>
						All
					</td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="checkAll" data-child="enabled" data-parent="tbody" checked /></span>
						<a href="javascript:void(0)" class="btn white">Save All <span class="raquo">&raquo;</span></a>
					</td>
				</tr>
				<tr class="row">
					<td>Call answer by BLF GUI</td>
					<td><span class="field_wrap"><input type="text" value="*{{ substr(str_shuffle('0123456789'), 0, 2) }}" /></span></td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="useDefault" checked /></span>
					</td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="enabled" checked /></span>
						<a href="javascript:void(0)" class="btn gray">Save</a>
					</td>
				</tr>
				<tr class="row">
					<td>Call Parking</td>
					<td><span class="field_wrap"><input type="text" value="*{{ substr(str_shuffle('0123456789'), 0, 2) }}" /></span></td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="useDefault" checked /></span>
					</td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="enabled" checked /></span>
						<a href="javascript:void(0)" class="btn gray">Save</a>
					</td>
				</tr>
				<tr class="row">
					<td>Change status to Available</td>
					<td><span class="field_wrap"><input type="text" value="*{{ substr(str_shuffle('0123456789'), 0, 2) }}" /></span></td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="useDefault" checked /></span>
					</td>
					<td>
						<span class="checkbox_wrap"><input type="checkbox" class="enabled" checked /></span>
						<a href="javascript:void(0)" class="btn gray">Save</a>
					</td>
				</tr>
			</tbody>
		</table>
		
		<nav class="dropdown" id="actionSetter">
			<ul>
				<li><a href="" class="icon_status_online">Edit</a><i class="triangle"></i></li>
				<li><a href="" class="icon_status_busy">Delete</a></li>
			</ul>
		</nav>
	</section>

@stop

<!-- popup form -->
@section('form_title')
	<h2 class="what"></h2>
@stop

@section('form_content')
	
@stop