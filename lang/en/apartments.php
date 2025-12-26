<?php 
return [
    'validation' => [

        'title' => [
            'required' => 'Apartment title is required',
            'string'   => 'Apartment title must be a string',
            'min'      => 'Apartment title must be at least 10 characters',
            'max'      => 'Apartment title may not exceed 255 characters',
            'title.regex' => 'Apartment title must contain letters only',

        ],

        'description' => [
            'required' => 'Apartment description is required',
            'string'   => 'Apartment description must be a string',
            'max'      => 'Apartment description may not exceed 512 characters',
        ],

        'city' => [
            'required' => 'City is required',
            'string'   => 'City must be a string',
            'max'      => 'City name is too long',
        ],

        'governorate' => [
            'required' => 'Governorate is required',
            'string'   => 'Governorate must be a string',
            'max'      => 'Governorate name is too long',
        ],

        'rooms' => [
            'required' => 'Number of rooms is required',
            'integer'  => 'Rooms must be an integer',
            'min'      => 'At least one room is required',
        ],

        'area' => [
            'required' => 'Area is required',
            'min'      => 'Area must not be less than 3 letter',
            'max'=>'Area must not be more than 128 letter',
        ],
         'floor_number'  =>[
        'required' => 'floor number is required',
        'integer'  => 'floor number must be a number',
        'min'      => 'floor number must not be less than 0',

    ],
    'is_furnished' =>[
        'boolean'=>'you have to answer with yes or no for is furnished'
    ],

        'price' => [
            'required' => 'Price is required',
            'numeric'  => 'Price must be a number',
            'min'      => 'Price must be positive',
            'max'      => 'Price exceeds the allowed limit',
    ],
       'images' => [
    'required' => 'Images are required.',
    'array'    => 'Images must be provided as an array.',
],

'images.*' => [
    'image' => 'Each file must be a valid image.',
    'max'   => 'Each image size must not exceed 2 MB.',
],

    ],
    
    'added_successfully' => 'Apartment added successfully',
    'already_exists' => 'This apartment already exists in the selected city',
    'not_exist'=>'no available apartments',
    'deletion_failed'=>'can not delete ,apartment not exists',
    'deletion_successful'=>'deleted successfully',
    'updated_successful'=>'updated successfully',
    'searching_failed'=>'there is nothing in the search',
    'description_empty'=> 'no description',
    'no_contant'=>'nothing to show',
    'only_owner_allowed'=>'do not have the permission,only the apartment owner',
    'not_found'=>'No such apartment',
    
];
