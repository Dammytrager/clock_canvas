<?php

$cw = 300;
$ch = 300;

// Create image canvas
$im = imageCreate($cw, $ch);

$daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

$months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November',
            'December'];

/**
 * Step 2 – Add Content
 * Create used colours in palette
 * NB. Work out the RBG values for your favourite colours using the
 * interactive colour mixer on the teaching web if needed.
 */
$black = imageColorAllocate($im, 0, 0, 0); // general purposes.
$white = imageColorAllocate($im, 255, 255, 255); // general purposes.
$red = imageColorAllocate($im, 255, 0, 0); // hour hand.
$green = imageColorAllocate($im, 0, 255, 0); // minute hand.
$blue = imageColorAllocate($im, 0, 0, 255); // second hand.
$magenta = imageColorAllocate($im, 255, 0, 255); // number, text, etc.
$darkgrey = imageColorAllocate($im, 196,196,196); // some decorations!

// optionally, create canvas background
// NB. canvas' size is [0,$cw-1] => $cw pixel.
//imageFilledRectangle($im, 0, 0, $cw-1, $ch-1, $white);
imageColorTransparent($im);

// compute (x,y) coordinates for clock center (= canvas center)
$xc = (int) $cw/2; $yc = (int) $ch/2;

// draw circular border(s) for clock
imageArc($im, $xc, $yc, (int) ($cw * 0.95), (int) ($ch * 0.95),
    0, 360, $white); // outer circle
imageArc($im, $xc, $yc, (int) ($cw * 0.9), (int) ($ch * 0.9), 0, 360, $darkgrey); // inner (decorative!) circle

// get time_of_day - localtime(), or use date()
$currentTimeStamp = time();
$lt = localtime($currentTimeStamp); // array elements/indexes ordered in a standard 24-hour format as sec:min:hr

// compute/store breakdown values for hr,min,sec of current time
$meridian = $lt[2] > 12 ? 'PM' : 'AM';
$ts = $lt[0];
$tm = $lt[1];
$th = $lt[2] === 0 ? 12 : $lt[2] > 12 ? $lt[2] - 12 : $lt[2];
$td = $lt[3];
$tdw = $lt[6];
$tmn = $lt[4];


$hXY = imClockPos2XY('hour', $th, $tm);
$mXY = imClockPos2XY('minute', $tm);
$sXY = imClockPos2XY('second', $ts);

// now we can put all our 'hands' on the clock!
imageLine($im, $xc, $yc, $xc + $hXY['x'], $yc + $hXY['y'], $red); // Hour.
imageLine($im, $xc, $yc, $xc + $mXY['x'], $yc + $mXY['y'], $green); // Minute.
imageLine($im, $xc, $yc, $xc + $sXY['x'], $yc + $sXY['y'], $blue); // Second.

// Optionally, put some numbers on clock face?
imageString($im, 5, 142, 25, "12", $white);
imageString($im, 5, 145, 265, "6", $white);
imageString($im, 5, 265, 145, "3", $white);
imageString($im, 5, 25, 144, "9", $white);

// Check your drawings (=sanity!) by displaying the current time in a string.
$ordinalDay = getOrdinalDay($td);
imageString($im, 2, 60, 230, "$daysOfWeek[$tdw] $months[$tmn] $ordinalDay, $th:$tm:$ts $meridian", $magenta);

// steps 3-5 see lecture notes.
header("Refresh:0");
header('Content-type: image/png');
imagePNG($im);
imageDestroy($im);

function imClockPos2XY ($whichHand, $val, $minutes = 0)
{
    if ($whichHand !== 'hour') $degree = ($val * 6) - 90;

    else $degree = (($val * 60 + $minutes) * 0.5) - 90;

    $degree = $degree < 0 ? $degree + 360 : $degree;

    $length = [
        'second' => 100,
        'minute' => 90,
        'hour'  => 70
    ];

    $x = (int) $length[$whichHand] * cos(deg2rad($degree));
    $y = (int) $length[$whichHand] * sin(deg2rad($degree));

    return ['x' => $x, 'y' => $y];
}

function getOrdinalDay($num)
{
    $prefix = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];

    if ((($num % 100) >= 11) && (($num%100) <= 13)) return $num . 'th';

    else return $num . $prefix[$num % 10];
}
