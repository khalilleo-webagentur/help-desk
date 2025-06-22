<?php

declare(strict_types=1);

namespace App\Helper;

final class AppHelper
{
    // General Configs
    public const bool NOTIFY_CUSTOMER_ON_TICKET_RESOLVED = true;
    public const bool NOTIFY_WEBMASTER_ON_TICKET_CREATED = true;

    // Ticket-Status
    public const string STATUS_ALL = 'All';
    public const string STATUS_OPEN = 'Open';
    public const string STATUS_IN_PROGRESS = 'In-progress';
    public const string STATUS_PENDING = 'Pending';
    public const string STATUS_ESCALATED = 'Escalated';
    public const string STATUS_HOLD = 'On-hold';
    public const string STATUS_WAITING_FOR_CUSTOMER = 'Waiting-for-customer';
    public const string STATUS_REOPENED = 'Reopened';
    public const string STATUS_RESOLVED = 'Resolved';
    public const string STATUS_CLOSED = 'Closed';

    // Ticket Priority
    public const string PRIORITY_URGENT = 'Urgent';
    public const string PRIORITY_HIGH = 'High';
    public const string PRIORITY_MEDIUM = 'Medium';
    public const string PRIORITY_LOW = 'Low';

    // Roles
    public const array ROLES = [
        'USER' => 'User',
        'CUSTOMER' => 'Customer',
        'SUPER_ADMIN' => 'Super Admin',
    ];

    public const string ROLE_CUSTOMER = 'ROLE_CUSTOMER';
    public const string ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    // Default Limits
    public const int DEFAULT_LIMIT_MESSAGES_ENTRIES = 15;
    public const int DEFAULT_MAX_LIMIT_MESSAGES_ENTRIES = 100;
    public const int DEFAULT_LIMIT_SYSTEM_LOGS_ENTRIES = 50;
    public const int DEFAULT_MAX_LIMIT_SYSTEM_LOGS_ENTRIES = 500;

    // System Logs
    public const array SYSTEM_LOG_EVENTS = [
        self::SYSTEM_LOG_EVENT_INFO,
        self::SYSTEM_LOG_EVENT_EXCEPTION,
        self::SYSTEM_LOG_EVENT_TICKET_COMMENT,
    ];

    public const string SYSTEM_LOG_EVENT_INFO = 'Info';
    public const string SYSTEM_LOG_EVENT_EXCEPTION = 'Exception';
    public const string SYSTEM_LOG_EVENT_TICKET_COMMENT = 'Ticket Comment Message';
}