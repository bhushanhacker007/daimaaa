<?php

return [
    'enabled' => env('AUTO_DISPATCH_ENABLED', true),

    'weights' => [
        'quality' => 40,
        'distance' => 30,
        'reliability' => 15,
        'fairness' => 15,
    ],

    'max_radius_km' => 10,
    'travel_buffer_minutes' => 30,
    'accept_window_minutes' => 15,
    'instant_accept_window_minutes' => 5,

    'reliability_penalty_decline' => 3,
    'reliability_penalty_timeout' => 2,

    'max_candidates' => 5,
    'min_reviews_for_full_quality' => 5,

    // Daimaa gets this % of the booking value per session; platform keeps the rest
    'daimaa_share_percent' => env('DAIMAA_SHARE_PERCENT', 70),
];
