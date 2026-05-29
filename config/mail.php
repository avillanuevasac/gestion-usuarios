<?php
return [
    // Cambiar a 'smtp' para usar Gmail/otro proveedor, o 'mail' para el mail() nativo
    'driver'     => 'smtp',

    // --- Configuración SMTP (Gmail) ---
    'host'       => 'smtp.gmail.com',
    'port'       => 587,
    'encryption' => 'tls',
    'username'   => 'angelvillanuevasantacruz@gmail.com',
    'password'   => 'vjij lwcq dcdu qntr',

    // --- Remitente ---
    'from_email' => 'angelvillanuevasantacruz@gmail.com',
    'from_name'  => 'Sistema de Usuarios',

];
