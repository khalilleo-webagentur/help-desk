<?php

declare(strict_types=1);

namespace App\Helper;

final class AppHelper
{
    // General Configs
    public const NOTIFY_CUSTOMER_ON_TICKET_RESOLVED = true;
    public const NOTIFY_WEBMASTER_ON_TICKET_CREATED = true;

    // Ticket-Status
    public const STATUS_ALL = 'All';
    public const STATUS_OPEN = 'Open';
    public const STATUS_IN_PROGRESS = 'In-progress';
    public const STATUS_PENDING = 'Pending';
    public const STATUS_ESCALATED = 'Escalated';
    public const STATUS_HOLD = 'On-hold';
    public const STATUS_WAITING_FOR_CUSTOMER = 'Waiting-for-customer';
    public const STATUS_REOPENED = 'Reopened';
    public const STATUS_RESOLVED = 'Resolved';
    public const STATUS_CLOSED = 'Closed';

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

    // System Logs
    public const SYSTEM_LOG_EVENTS = [
        self::SYSTEM_LOG_EVENT_EXCEPTION,
        self::SYSTEM_LOG_EVENT_TICKET_COMMENT,
    ];
    public const SYSTEM_LOG_EVENT_EXCEPTION = 'Exception';
    public const SYSTEM_LOG_EVENT_TICKET_COMMENT = 'Ticket Comment Message';
}