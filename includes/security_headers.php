<?php
// Prevenir que el navegador MIME-sniff la respuesta.
header("X-Content-Type-Options: nosniff");

// Habilitar la protección XSS del navegador
header("X-XSS-Protection: 1; mode=block");

// Prevenir clickjacking
header("X-Frame-Options: DENY");

// Habilitar HSTS (HTTP Strict Transport Security)
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

// Política de seguridad de contenido (CSP)
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';");
?>
