<?php

declare(strict_types=1);

namespace App\Helper;

final class AppHelper
{
    public const STATUS_OPEN = 'Open';
    public const STATUS_IN_PROGRESS = 'In Progress';
    public const STATUS_PENDING = 'Pending';
    public const STATUS_RESOLVED = 'Resolved';
    public const STATUS_CLOSED = 'Closed';

    public const ROLES = [
        'USER' => 'User',
        'CUSTOMER' => 'Customer',
        'SUPER_ADMIN' => 'Super Admin',
    ];

    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
}