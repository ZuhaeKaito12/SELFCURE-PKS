<?php

return [

    // Google Gemini API key. Get a free one (no credit card required) at
    // https://aistudio.google.com/apikey
    'gemini_api_key' => env('GEMINI_API_KEY'),

    // Model id as shown in your AI Studio dashboard's model picker, e.g.
    // one of the current "Flash-Lite" models (cheapest/fastest tier).
    'gemini_model' => env('GEMINI_MODEL', 'gemini-3.5-flash-lite'),

];
