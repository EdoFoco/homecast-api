@extends('emails.EmailLayout')

@section('content')
    <h1> hi </h1>
    <h1>{{$viewing->property->name}}</h1>
    <a href="homecastapp://guest/properties/{{$viewing->property->id}}/viewings/{{$viewing->id}}">{{$viewing->property->name}} {{$viewing->id}}</h2>
@endsection