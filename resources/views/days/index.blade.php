@extends('layouts.app')

@section('content')

<div class="container">
	
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			
		@foreach($days as $day)

			<h3>{{ $day->date }}</h3>

		@endforeach

		</div>

	</div>

</div>

@endsection