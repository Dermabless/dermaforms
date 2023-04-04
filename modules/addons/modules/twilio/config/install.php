<?php

$config = [
    'id' => 'twilio',
    'name'=> 'Twilio SMS',
    'class' => 'app\modules\addons\modules\twilio\Module',
    'description'=> [
        'en-US' => 'Send SMS notifications in real time by integrating with the Twilio SMS service.',
        'es-ES' => 'Envía notificaciones por SMS en tiempo real mediante el servicio SMS de Twilio.',
    ],
    'version' => '1.4.1',
];

return $config;
