# socialiteprovider-flexkids

## Installation
Manual installation

```
composer require socialiteproviders/manager
```

### Configuration for .env
This information is given by Flexkids B.V.
```
FLEXKIDS_SERVER="https://hub.flexkids.nl"
FLEXKIDS_KEY="xxxxxx-xxxxxx-xxxxxx-xxxxxx-xxxxxx"
FLEXKIDS_SECRET="xxxxxx-xxxxxx-xxxxxx-xxxxxx-xxxxxx-xxxxxx"
FLEXKIDS_REDIRECT_URI="https://yourdomain.com/login/callback/flexkids"
FLEXKIDS_APIUSER="Your API User Key Here"
FLEXKIDS_RESOURCE="https://yourdomain.com/login/redirect/flexkids"
FLEXKIDS_AUTHURL="https://companyname.flexkids.nl/ouderapp/#/extern"
```

### Configuration for services.php
```
    'flexkids' => [
        'server' => env('FLEXKIDS_SERVER'),
        'client_id' => env('FLEXKIDS_KEY'),
        'client_secret' => env('FLEXKIDS_SECRET'),
        'redirect' => env('FLEXKIDS_REDIRECT_URI'),
        'resource' => env('FLEXKIDS_RESOURCE'),
        'apiuser' => env('FLEXKIDS_APIUSER'),
        'authurl' => env('FLEXKIDS_AUTHURL'),
    ]
```

### Routes for web
```
Route::get ( '/login/redirect/{service}', 'Auth\SocialAuthController@redirect' );
Route::get ( '/login/callback/{service}', 'Auth\SocialAuthController@callback' );
```

### Copy SocialAuthController.php
examples\SocialAuthController.php to App\Http\Controllers\Auth

### Copy other files
Copy all other files to your {{projectRoot}}\SocialiteProviders\Flexkids
- composer.json
- README.md
- src
    - FlexkidsExtendSocialite.php
    - Provider.php

### Edit composer.json
```
"autoload": {
        "psr-4": {
            "App\\": "app/",
            "SocialiteProviders\\Flexkids\\": "SocialiteProviders/Flexkids/src/"
        },
        "classmap": [
            "database/seeds",
            "database/factories"
        ]
    },
```

### Edit EventServiceProvider.php
```
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\Flexkids\FlexkidsExtendSocialite@handle'
        ],
    ];
```

### Create database columns
Create two database columns in table users
- provider (string, nullable)
- provider_id (string, nullable)