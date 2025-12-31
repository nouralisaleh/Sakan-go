<?php
 return [
    'start_date'=>[
        'required'=>'start date is required',
    ],
     'end_date'=>[
        'required'=>'end date is required',
        'after_or_equal'=>'the end date must be after or equal of start date'
    ],
    
    'date'=>'must be like date format :year/month/day',
    'conflict'=>'Sorry, there is another booking within the same time period.',
    'pending'=>'Your booking has been registered, you must wait for approval.',
    'can_not_reject_or_cancel'=>'the booking is already rejected or cancelled',
    'not_found'=>'the booking not found',
    'cancel_ok'=>'booking cancelled',
    'update_ok'=>'booking updated',
    'delete_ok'=>'booking deleted',
    'rejected'=>'owner rejected the booking,contact with the owner to learn more',
    'not_booked'=>'no booking for approve'

 ];