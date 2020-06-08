<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Bookings;
use DateTime;
use DateInterval;
use App\Room;
use App\SoLuotDat;
class CalendarController extends Controller
{
    //get calendar
    public function getCalendar(){
    	return view('calendar');
    }
    public static function showCalendar($month, $year){
    	//create array contain names of all days in a week('calendar', ['calendar'=>$calendar]);
    	$daysOfWeek = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursdays', 'Friday', 'Saturday');

    	//get the first day of month
    	$firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);

    	//get number of days this months contain
    	$numberDays = date('t', $firstDayOfMonth);
    	//echo $numberDays; //thang 5 2020 co 31 ngay

    	//getting some information about the first days of this month
    	$dateComponents = getdate($firstDayOfMonth);
    	//dd($dateComponents);
	//     	array:11 [▼
	//   "seconds" => 0
	//   "minutes" => 0
	//   "hours" => 0
	//   "mday" => 1
	//   "wday" => 5
	//   "mon" => 5
	//   "year" => 2020
	//   "yday" => 121
	//   "weekday" => "Friday"
	//   "month" => "May"
	//   0 => 1588291200
	// ]

    	$monthName = $dateComponents['month']; //May

    	//Getting the index value 0-6 of the first day of this month
    	$dayOfWeek = $dateComponents['wday'];// 5

    	//getting current date
    	$dateToday = date('Y-m-d');

    	$calendar = "<table style='border: 1px solid black;margin: auto;'>";
    	$calendar .= "<center><h2>$monthName 2020</h2></center>";
    	echo '<br>';
    	$calendar .= 
    	"<form id='room_select_form'>
    		<div class='row'>
    			<div class='col-md-6 col-md-offset-3 form-group'>
    				<label>Select Room</label>
    					<select class='form-control' id='room_select'>";
    	$rooms = Room::all();
    	foreach($rooms as $r)
    	{
    		$calendar .= "<option value='".$r->id."'>".$r->name."</option>";
    	}
    	$calendar .= "</select>
    			</div>
    		</div>
    	</form>";
    	$calendar .= "<tr>";
    		foreach($daysOfWeek as $day)
    		{
    			$calendar .= "<th>$day</th>";
    		}
    	$calendar .= "</tr><tr>";
    	if($dayOfWeek > 0)
    	{
    		for($i = 0; $i < $dayOfWeek; $i++){
    			$calendar .= "<td></td>";
    		}
    	}
    	$currentDay = 1;
    	$month = str_pad($month, 2, "0", STR_PAD_LEFT);
    	while($currentDay <= $numberDays)
    	{
    		if($dayOfWeek == 7)
    		{
    			$dayOfWeek = 0;
    			$calendar .= "</tr><tr>";
    		}
    		$currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
    	 	$date = "$year-$month-$currentDayRel";
    	 	$today = date('Y-m-d') == $date ? 'bg-warning':'';
    	 	if($date < date('Y-m-d'))
    	 	{
    	 		$calendar .= "<td class='".$today."'><h4>$currentDay</h4><a class='btn btn-danger'>N/A</a></td>";
    	 	}
    	 	// elseif(Bookings::where('date', $date)->get()->toArray()){
    	 	// 	$calendar .= "<td class='".$today."'><h4>$currentDay</h4><a class='btn btn-danger'>Already Book</a></td>";
    	 	// }
    	 	else
    	 	{
    	 		//now we have 6 appointment in a day, if full 6, no booking
    	 		$getBookingInThisDate = Bookings::where('date', $date)->get()->toArray();
    	 		if(count($getBookingInThisDate) == 6)
    	 		{
    	 			$calendar .= "<td class='".$today."'><h4>$currentDay</h4><a class='btn btn-danger' disabled=''>Full</a></td>";
    	 		}
    	 		else
    	 		{
    	 			$calendar .= "<td class='".$today."'><h4>$currentDay</h4><a class='btn btn-success' href='timeslot/".$date."'>Book</a></td>";
    	 		}
    	 		
    	 	}
    	 	$currentDay++;
    	 	$dayOfWeek++;
    	}
    	if($dayOfWeek != 7)
    	{
    		$remainingDays = 7-$dayOfWeek;
    		for($j = 0; $j < $remainingDays; $j++)
    		{
    			$calendar .= "<td></td>";
    		}
    	}
    	$calendar .= "</tr>";

    	$calendar .= "</table>";
    	return $calendar;
    }

    
//     public function getTest(){
//     	$currentDay = date("Y-m-d");
//     	// if(Bookings::where('date', $currentDay))
//     	// {
//     	// 	echo 'yes';
//     	// }
//     	// else
//     	// {
//     	// 	echo 'No';
//     	// }
//     	if(Bookings::where('date',$currentDay)->get()->toArray()){
//     		echo 'Yes';
// }
// else{
// 	echo 'No';
// }
    	
//     	// if(in_array($currentDay, $bookings))
//     	// {
//     	// 	echo 'Yes';
//     	// }
//     	// else
//     	// {
//     	// 	echo 'No';
//     	// }
//     }

    public function postFormBooking(Request $request, $dateBooking){
    	$booking = new Bookings;
    	$booking->ten = $request->ten;
    	$booking->email = $request->email;
    	$booking->date = $dateBooking;
    	$booking->time = $request->timeslot;
    	$booking->save();
        $soluotdat = 0;
        //Tùy thuộc vào số nhân viên có trong salon, vì trong 1 giờ có nhiều nhân viên làm việc và cũng có nhiều khách hàng đặt lịch tại một khoảng thời gian nên phải cho phép đặt tối đa bao nhiêu lần tuy theo số nhân viên
        $sld = SoLuotDat::where('date', $dateBooking)->where('time',$request->timeslot)->get()->toArray();
        if(!$sld)
        {
            $new = new SoLuotDat;
            $new->date = $dateBooking;
            $new->time = $request->timeslot;
            $new->soluotdat = 1;
            $new->save();
        }
        else
        {
            foreach($sld as $s)
            {
                $soluotdatcu = $s['soluotdat'];
            }
            SoLuotDat::where('date', $dateBooking)->where('time', $request->timeslot)->update(['soluotdat'=>$soluotdatcu+1]);
        }
        foreach($sld as $s)
            {
                $soluotdat = $s['soluotdat'];
            }
    	return redirect('timeslot/'.$dateBooking)->with('soluotdat', $soluotdat)->with('thongbao', 'Booking successfully');
    }


    //timeslot
    public function getTimeSlot($dateBooking)
    {
    	return view('timeslot', ['dateBooking'=>$dateBooking]);
    }
    public static function timeslot($duration, $cleanup, $start, $end){
		$start = new DateTime($start);
		$end = new DateTime($end);
		$interval = new DateInterval("PT".$duration."M");
		$cleanupInterval = new DateInterval("PT".$cleanup."M");
		$slots = array();
		for($intStart = $start; $intStart < $end; $intStart->add($interval)->add($cleanupInterval))
		{
			$endPeriod = clone $intStart;
			$endPeriod->add($interval);
			if($endPeriod > $end)
			{
				break;
			}

			$slots[] = $intStart->format("H:i:A")."-".$endPeriod->format("H:i:A");
		}
		return $slots;
	}

}

