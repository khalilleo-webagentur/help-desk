<?php

declare(strict_types=1);

namespace App\Helper;

final class AppHelper
{
    // General Configs
    public const NOTIFY_CUSTOMER_ON_TICKET_STATUS_CLOSED = true;

    // Ticket-Status
    public const STATUS_ALL = 'ALL';
    public const STATUS_OPEN = 'OPEN';
    public const STATUS_IN_PROGRESS = 'IN-PROGRESS';
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_RESOLVED = 'RESOLVED';
    public const STATUS_CLOSED = 'CLOSED';

    // Ticket Priority
    public const PRIORITY_URGENT = 'Urgent';
    public const PRIORITY_HIGH = 'High';
    public const PRIORITY_MEDIUM = 'Medium';
    public const PRIORITY_LOW = 'Low';

    // Roles
    public const ROLES = [
        'USER' => 'User',
        'CUSTOMER' => 'Customer',
        'SUPER_ADMIN' => 'Super Admin',
    ];

    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    // Default Limits
    public const DEFAULT_LIMIT_MESSAGES_ENTRIES = 15;
    public const DEFAULT_MAX_LIMIT_MESSAGES_ENTRIES = 100;
    public const DEFAULT_LIMIT_SYSTEM_LOGS_ENTRIES = 50;
    public const DEFAULT_MAX_LIMIT_SYSTEM_LOGS_ENTRIES = 500;
}