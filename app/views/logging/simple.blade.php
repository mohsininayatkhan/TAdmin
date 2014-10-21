@extends('layout.master')

@section('content')
	<h1>Simple</h1>
	
	@include('logging.form')
	
	@if (isset($log))
	    
	    <table>
	        @foreach ($log as $entry)
    	        <tr>
                    <td>{{$entry['date']}}</td>
                    <td>{{$entry['message']}}</td>
                </tr>
            @endforeach
	    </table>
	    
    @endif
	
@stop