<?php
 return [
    'start_date'=>[
        'required'=>'start date is required',
        'after_or_equal'=>'the start date must be today or after'

    ],
    'update_start_date'=>[
        'required'=>'the update start date is required',
        'after_or_equal'=>'the update start date must be after or equal of update start date'
    ],
    'update_end_date'=>[
        'required'=>'the update end date is required',
        'after_or_equal'=>'the update end date must be after or equal of update start date'
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
    'not_booked'=>'no booking for approve',
    'update_request_not_found'=>'booking update request not found',
    'update_request_cancelled'=>'booking update request cancelled',
    'update_request_rejected'=>'booking update request rejected',
    'update_request_approved'=>'booking update request approved',
    'update_request_created'=>'booking update request created, you must wait for owner approval',
    'update_request_done_updated'=>'booking updated as your request',
    'booking_owner'=>'you must be the booking owner',
    'booking_not_complete'=>'the booking must be completed to make review',
    'no_exist_booking'=>' no existing booking ',


 ];