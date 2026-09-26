# rasim/php-mvc-core

Shared PHP MVC core library extracted from Group A repos (PhoneBook-php, rest-api-php, rest-api-mvc-php).

## Contents

- `Core` — Router/dispatcher class
- `Database` — PDO database wrapper
- `Controller` — Base controller class
- `helpers` — Flash messages, redirect, IP detection, JWT helpers, logging
- `GoogleAuthenticator` — TOTP (RFC 6238) based on `spomky-labs/otphp`
- `endroid/qr-code` — QR code generation (replaces vendored phpqrcode)
- `firebase/php-jwt` — JWT encode/decode (replaces vendored firebase/php-jwt 3.x)

## Installation

```bash
composer require rasim/php-mvc-core
```

## Requirements

- PHP 8.2+
- PDO, JSON, Session extensions
