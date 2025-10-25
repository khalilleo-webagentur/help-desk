<?php

declare(strict_types=1);

namespace App\Helper;

final class AppHelper
{
    // General Configs
    public const bool NOTIFY_CUSTOMER_ON_TICKET_RESOLVED = true;
    public const bool NOTIFY_WEBMASTER_ON_TICKET_CREATED = true;

    // Files location
    public const string TICKET_ATTACHMENT = 'attachments';
    public const array DIRECTORIES = [
        self::TICKET_ATTACHMENT,
    ];

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
        self::SYSTEM_LOG_CRITICAL,
        self::SYSTEM_LOG_EVENT_EXCEPTION,
        self::SYSTEM_LOG_EVENT_TICKET_COMMENT,
        self::SYSTEM_LOG_EVENT_FILE_DELETED,
        self::SYSTEM_LOG_EVENT_USER_ACCOUNT_CREATED
    ];

    public const string SYSTEM_LOG_EVENT_INFO = 'Info';
    public const string SYSTEM_LOG_CRITICAL = 'Critical';
    public const string SYSTEM_LOG_EVENT_EXCEPTION = 'Exception';
    public const string SYSTEM_LOG_EVENT_TICKET_COMMENT = 'Ticket Comment Message';
    public const string SYSTEM_LOG_EVENT_FILE_DELETED = 'File Deleted';
    public const string SYSTEM_LOG_EVENT_USER_ACCOUNT_CREATED = 'User Account Created';

    // FAQs
    public const array FAQS = [
        [
            'uuid' => 'k1l2m3n4o5p6',
            'title' => 'What is the role of the HelpDesk?',
            'description' => 'The HelpDesk provides essential support by assisting users with technical issues, troubleshooting problems, and ensuring smooth operation of systems and software.'
        ],
        [
            'uuid' => 'l2m3n4o5p6q7',
            'title' => 'How can I contact the HelpDesk?',
            'description' => 'You can contact the HelpDesk via email, phone, or through the support portal available on our website.'
        ],
        [
            'uuid' => 'm3n4o5p6q7r8',
            'title' => 'What types of issues can the HelpDesk assist with?',
            'description' => 'The HelpDesk can assist with a variety of issues, including software installation, network connectivity problems, and hardware malfunctions.'
        ],
        [
            'uuid' => 'n4o5p6q7r8s9',
            'title' => 'What are the HelpDesk hours of operation?',
            'description' => 'The HelpDesk operates from 8 AM to 6 PM, Monday through Friday, excluding public holidays.'
        ],
        [
            'uuid' => 'o5p6q7r8s9t0',
            'title' => 'How long does it take to resolve an issue?',
            'description' => 'Resolution times vary depending on the complexity of the issue, but the HelpDesk aims to address all inquiries within 24 hours.'
        ],
        [
            'uuid' => 'p6q7r8s9t0u1',
            'title' => 'Can I track the status of my support request?',
            'description' => 'Yes, you can track the status of your support request through the support portal where you submitted your inquiry.'
        ]
    ];
}