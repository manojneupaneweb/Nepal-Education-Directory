<?php

return [
    'google_site_verification' => env('SEO_GOOGLE_SITE_VERIFICATION'),

    /*
    | A published institution is indexable when its name is present and at
    | least one quality signal is present. An official website is one signal,
    | not a requirement. A name and address alone are not enough.
    |
    | Signals: description length, a public phone or email, a linked program,
    | programs stored on the institution, or an http(s) official website.
    */
    'quality' => [
        'min_description_length' => 80,
    ],
];
