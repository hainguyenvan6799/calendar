<?php
	use \App\Http\Controllers\CalendarController;
?>
<!DOCTYPE html>
<html>
<head>
	<base href="{{asset('')}}">
	<title></title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<style type="text/css">
	</style>
</head>
<body>
	<div class="container">
		@if(session('thongbao'))
			<div class="alert alert-success">
				{{session('thongbao')}}
			</div>
		@endif

		
		<?php
			$currentDate = getdate();
			$currentMonth = $currentDate['mon'];
			$currentYear = $currentDate['year'];
			echo CalendarController::showCalendar($currentMonth, $currentYear);
		?>
	</div>
</body>
</html>