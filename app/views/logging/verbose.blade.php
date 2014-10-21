@extends('layout.master')

@section('content')
	<h1>Verbose</h1>
	
	@include('logging.form')
	
	@if (isset($log))
	    
	    <table>
	        @foreach ($log as $entry)
    	        <tr>
                    <td>{{$entry['date']}}</td>
                    <td>{{$entry['message']}}</td> 
                    <td>{{$entry['trace']}}</td>
                </tr>
            @endforeach
	    </table>
	    
    @endif
    
@stop