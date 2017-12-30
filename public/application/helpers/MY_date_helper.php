<?php
/*
 * Lay ngay tu dang int
 * @$time : int - thoi gian muon hien thi ngay
 * @$full_time : cho biet co lay ca gio phut giay hay khong
 */
function get_date($time, $full_time = true)
{
    $fomat = '%d-%m-%Y';
    if($full_time)
    {
        $fomat = $fomat.' - %H:%i:%s';
    }
    $date = mdate($fomat , $time);
    return $date;
}