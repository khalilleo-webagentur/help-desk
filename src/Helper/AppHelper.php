<?php

declare(strict_types=1);

namespace App\Helper;

final class AppHelper
{
    public const DEFAULT_HOME_CARDS_COUNT = 9;

    public const ROLES = [
        'USER' => 'User',
        'CUSTOMER' => 'Customer',
        'SUPER_ADMIN' => 'Super Admin',
    ];

    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
}