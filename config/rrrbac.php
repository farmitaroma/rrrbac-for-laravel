<?php

return [
    'auth_class' => 'App\Models\User',

    'authorized_routes' => [
        'ignition.healthCheck',
        'ignition.updateConfig',
        'ignition.executeSolution',
        'livewire.preview-file',
        'livewire.upload-file',
        'livewire.update',
        'sanctum.csrf-cookie'
    ]
];
