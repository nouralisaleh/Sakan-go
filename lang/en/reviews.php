<?php
return [
    'rating'=>[
        'required'=>'the rating is required',
        'integer'=>'',
        // 'min'=>'',
        // 'max'=>'',

    ],
    'commant'=>[
        'string'=>'the comment field must be a text',
        'max'=>'the comment must not  more than 1024 ',
    ],
    'review_already_exists'=>'the review is already exists',
    'created'=>'review created successfully',
    'fetched'=>'review fetched successfully',
    'validation'=>[
        'rating'=>[
            'required'=>'the rating is required',
            'integer'=>'the rating must be an integer',
            'min'=>'the rating must be at least 1',
            'max'=>'the rating must not be greater than 5',

        ],
        'comment'=>[
            'string'=>'the comment field must be a text',
            'max'=>'the comment must not  more than 1024 ',

        ],
    ],

];