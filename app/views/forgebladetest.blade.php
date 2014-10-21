@extends('layout.master')

@section('content')
	<h1>Hello World</h1>
	
	@foreach($content as $paragraph)
		<p>{{ $paragraph['content'] }}</p>
	@endforeach
	
@stop