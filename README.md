# gateway-request-laravel

Пакет предназначен для проверки запросов из gateway. Содержит в себе класс пользователя 
guard, auth для Laravel/Lumen.

## Настройки

### Провайдеры

Необходимо зарегистрировать 2 сервис провайдера:
- Yetione\GatewayRequest\Logging\LaravelLoggingServiceProvider &mdsah; используется для настройки логгера, добавляя 
к нему TagProcessor и устанавливая request_id;
- Yetione\GatewayRequest\Providers\GatewayServiceProvider &mdash; используется для регистрации guards и auth в сервисах фреймворка

#### Laravel
```php
<?php 
// config/app.php
use Yetione\GatewayRequest\Logging\LaravelLoggingServiceProvider;
use Yetione\GatewayRequest\Providers\GatewayServiceProvider;


return [
    'providers'=>[
        LaravelLoggingServiceProvider::class,
        GatewayServiceProvider::class
    ],
];
```

#### Lumen
```php
<?php
// bootstrap/app.php
use Yetione\GatewayRequest\Logging\LumenLoggingServiceProvider;
use Yetione\GatewayRequest\Providers\GatewayServiceProvider;

$app->register(LumenLoggingServiceProvider::class);
$app->register(GatewayServiceProvider::class);

```

### .env

В файле  `.env` необходимо указать следющие параметры:

- _JWT_SECRET_ &mdash; используется для подписи ключа
- _JWT_PRIVATE_KEY_ &mdash; путь до приватного ключа
- _JWT_PUBLIC_KEY_ &mdash; путь до публичного ключа
- _JWT_ALGO_ &mdash; алгоритм шифрования

#### Пример
```dotenv
JWT_SECRET=XUu7XghJ3NFmoZ4nz7fKRRmaGogE4xZbCTT2xbyqESLeGeAQ3klIziNvaj6OuT0d
JWT_PRIVATE_KEY=file:///app/storage/keys/private_key.pem
JWT_PUBLIC_KEY=file:///app/storage/keys/ecdsa-p521-public.pem
JWT_ALGO=ES512
```

### config/auth.php

Для использования необходимо в `config/auth.php` добавить новый guardm provider и установить guard по-умолчанию.

При такой настройке будут проверяться все входящие запросы.

```php
<?php
// config/auth.php
use Yetione\GatewayRequest\Enums\GatewayAuth;
[
    'defaults' => [
        'guard' => GatewayAuth::AUTH,           
        'passwords' => 'users',
    ],
    'guards'=> [
        GatewayAuth::AUTH=>[
            'driver'=> GatewayAuth::AUTH_GUARD,
            'provider'=> GatewayAuth::AUTH_PROVIDER
        ]
    ],
    'provider'=>[
          GatewayAuth::AUTH_PROVIDER=>[
            'driver'=> GatewayAuth::AUTH_PROVIDER,
          ]  
    ]
];
``` 
