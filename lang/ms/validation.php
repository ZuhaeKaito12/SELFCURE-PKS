<?php

// Rules this app actually uses. Anything not listed falls back to English.
return [
    'accepted' => ':Attribute perlu diterima.',
    'between' => [
        'numeric' => ':Attribute mestilah antara :min hingga :max.',
        'string' => ':Attribute mestilah antara :min hingga :max aksara.',
    ],
    'boolean' => ':Attribute mestilah ya atau tidak.',
    'confirmed' => 'Pengesahan :attribute tidak sepadan.',
    'current_password' => 'Kata laluan tidak betul.',
    'digits' => ':Attribute mestilah :digits digit.',
    'email' => 'Sila masukkan alamat e-mel yang sah.',
    'exists' => ':Attribute yang dipilih tidak sah.',
    'in' => ':Attribute yang dipilih tidak sah.',
    'integer' => ':Attribute mestilah nombor bulat.',
    'lowercase' => ':Attribute mestilah dalam huruf kecil.',
    'max' => [
        'numeric' => ':Attribute tidak boleh melebihi :max.',
        'string' => ':Attribute tidak boleh melebihi :max aksara.',
    ],
    'min' => [
        'numeric' => ':Attribute mestilah sekurang-kurangnya :min.',
        'string' => ':Attribute mestilah sekurang-kurangnya :min aksara.',
    ],
    'numeric' => ':Attribute mestilah nombor.',
    'password' => [
        'letters' => ':Attribute mesti mengandungi sekurang-kurangnya satu huruf.',
        'mixed' => ':Attribute mesti mengandungi huruf besar dan huruf kecil.',
        'numbers' => ':Attribute mesti mengandungi sekurang-kurangnya satu nombor.',
        'symbols' => ':Attribute mesti mengandungi sekurang-kurangnya satu simbol.',
        'uncompromised' => ':Attribute ini pernah terdedah dalam kebocoran data. Sila pilih :attribute yang lain.',
    ],
    'required' => ':Attribute perlu diisi.',
    'string' => ':Attribute mestilah teks.',
    'unique' => ':Attribute ini sudah digunakan.',

    'attributes' => [
        'name' => 'nama',
        'email' => 'e-mel',
        'password' => 'kata laluan',
        'current_password' => 'kata laluan semasa',
        'business_name' => 'nama perniagaan',
        'business_type' => 'industri',
        'ssm_number' => 'no. SSM',
        'phone' => 'no. telefon',
        'state' => 'negeri',
        'code' => 'kod pengesahan',
        'message' => 'mesej',
        'status' => 'status',
    ],
];
