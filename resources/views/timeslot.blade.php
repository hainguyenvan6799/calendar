<?php
	$duration = 60;
	$cleanup = 0;
	$start = "09:00";
	$end = "15:00";
	use App\Http\Controllers\CalendarController;
	use App\Bookings;
	use App\SoLuotDat;
?>
<!DOCTYPE html>
<html>
<head>
	<base href="{{asset('')}}">
	<title></title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
	{{-- <form action="../formBooking/{{$dateBooking}}" method="post">
		{{csrf_field()}}
		Ten<input type="text" name="txtName"><br>
		Email<input type="email" name="txtEmail"><br>
		<input type="submit" name="submit" value="Book">
	</form> --}}
	<div class="container">
		<h1 class="text-center">Book for Date {{$dateBooking}}</h1>
		@if(session('thongbao'))
			<div class="alert alert-success">
				{{session('thongbao')}}
			</div>
		@endif
		<div class="row">
			<?php
				$timeslot = CalendarController::timeslot($duration, $cleanup, $start, $end);
				foreach($timeslot as $t){ ?>
				<div class="col-md-2">
					{{-- <a href="formBooking/{{$dateBooking}}/{{$t}}">{{$t}}</a> --}}
					{{-- <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal">{{$t}}</button> --}}
					@if(SoLuotDat::where('time', $t)->where('date', $dateBooking)->where('soluotdat', 2)->get()->toArray())
					<button type="button" class="btn btn-danger btn-lg book" data-toggle="modal" data-target="#myModal" data-timeslot="{{$t}}" disabled="" title="Booked">{{$t}}</button>
					@else
					<button type="button" class="btn btn-info btn-lg book" data-toggle="modal" data-target="#myModal" data-timeslot="{{$t}}" title="Book now">{{$t}}</button>
					@endif
				</div>
			<?php } ?>
		</div>

		{{-- modal --}}
					<div id="myModal" class="modal fade" role="dialog">
					  <div class="modal-dialog">

					    <!-- Modal content-->
					    <div class="modal-content">
					      <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal">&times;</button>
					        <h4 class="modal-title text-center">Booking <span id="slot"></span></h4>
					      </div>
					      <div class="modal-body">
					        <div class="row">
					        	<div class="col-md-12">
					        		<form action="formBooking/{{$dateBooking}}" method="post" id="formBooking">
					        			{{csrf_field()}}
					        			<div class="form-group">
					        				<label for="">Timeslot</label>
					        				<input type="text" readonly="" name="timeslot" id="timeslot" class="form-control">
					        			</div>

					        			<div class="form-group">
					        				<label for="ten">Tên</label>
					        				<input type="text" name="ten" id="ten" class="form-control" required="">
					        			</div>
					        			<div class="form-group">
					        				<label for="email">Email</label>
					        				<input type="email" name="email" id="email" class="form-control" required="">
					        			</div>
					        			<div class="form-group">
					        				<input class="btn btn-primary" type="submit" name="submit" value="Book">
					        			</div>
					        		</form>
					        	</div>
					        </div>
					      </div>
					      <div class="modal-footer">
					        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					      </div>
					    </div>

					  </div>
					</div>

					{{-- end modal --}}
	</div>	
</body>
<script type="text/javascript">
	$('.book').click(function(){
		var timeslot = $(this).attr('data-timeslot');
		$('#slot').html(timeslot);
		$('#timeslot').val(timeslot);
	});
</script>
</html>