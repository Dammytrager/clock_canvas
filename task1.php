<?php

$cw = 300;
$ch = 300;

// Create image canvas
$im = imageCreate($cw, $ch);

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
imageFilledRectangle($im, 0, 0, $cw-1, $ch-1, $white);

// compute (x,y) coordinates for clock center (= canvas center)
$xc = (int) $cw/2; $yc = (int) $ch/2;

// draw circular border(s) for clock
imageArc($im, $xc, $yc, (int) ($cw * 0.95), (int) ($ch * 0.95),
    0, 360, $black); // outer circle
imageArc($im, $xc, $yc, (int) ($cw * 0.9), (int) ($ch * 0.9), 0, 360, $darkgrey); // inner (decorative!) circle

// get time_of_day - localtime(), or use date()
$lt = localtime(); // array elements/indexes ordered in a standard 24-hour format as sec:min:hr

// compute/store breakdown values for hr,min,sec of current time
$ts = $lt[0];
$tm = $lt[1];
$th = $lt[2] === 0 ? 12 : $lt[2] > 12 ? $lt[2] - 12 : $lt[2];

// convert hour variable $th into a 12-hour display format.
/* Calculate the angles made by the individual clock hands
* from the (standard) three o'clock position.
* This will be achieved by the user defined imTime2Degree function
* that you have yet to complete in this practical.
*/
$th_deg = imTime2DegreeRad('hour', $th);
$tm_deg = imTime2DegreeRad('minute', $tm);
$ts_deg = imTime2DegreeRad('second', $ts);

/* This done, create another function (imDegree2XY) to compute the
* end-point (x,y) of the individual clock hands, assuming that the
* latter are drawn as lines that extend from ($xc, $yc); see
* figure/illustrations Appendix.
*/
$hXY = imDegree2XY('hour', $th_deg, $cw);
$mXY = imDegree2XY('minute', $tm_deg, $cw);
$sXY = imDegree2XY('second', $ts_deg, $cw);
// now we can put all our 'hands' on the clock!
imageLine($im, $xc, $yc, $xc + $hXY['x'], $yc + $hXY['y'], $red); // Hour.
imageLine($im, $xc, $yc, $xc + $mXY['x'], $yc + $mXY['y'], $green); // Minute.
imageLine($im, $xc, $yc, $xc + $sXY['x'], $yc + $sXY['y'], $blue); // Second.

// Optionally, put some numbers on clock face?
imageString($im, 5, 142, 25, "12", $black);
imageString($im, 5, 145, 265, "6", $black);
imageString($im, 5, 265, 145, "3", $black);
imageString($im, 5, 25, 144, "9", $black);

// Check your drawings (=sanity!) by displaying the current time in a string.
imageString($im, 5, 60, 230, "Time now is $th:$tm:$ts", $magenta);

// steps 3-5 see lecture notes.
header('Content-type: image/png');
imagePNG($im);
imageDestroy($im);

function imTime2DegreeRad ($whichHand, $val)
{
    $step = $whichHand === 'hour' ? 30 : 6;

    $degree = ($val * $step) - 90;
    $degree = $degree < 0 ? $degree + 360 : $degree;

    return $degree;
}

function imDegree2XY($whichHand, $degree, $clkRadius)
{
    // 2-element associative array with keys 'x' and 'y' referring
    // to (x,y) coordinates
    $length = [
        'second' => 100,
        'minute' => 90,
        'hour'  => 70
    ];
    $x = (int) $length[$whichHand] * cos(deg2rad($degree));
    $y = (int) $length[$whichHand] * sin(deg2rad($degree));

    return ['x' => $x, 'y' => $y];
}
