<?php

return [
    "group" => "Wallet",
    "withdrawal_methods" => [
        "title" => "Withdrawal Methods",
        "columns" => [
            "logo" => "Logo",
            "name" => "Name",
            "minimum_amount" => "Minimum Amount",
            "maximum_amount" => "Maximum Amount",
            "currency" => "Currency",
            "conversion_rate" => "Conversion Rate",
            "is_active" => "Is Active",
            "description" => "Description",
            "active" => "Active",
            "inactive" => "Inactive",
        ],
    ],
    "withdrawal_requests" => [
        "title" => "Withdrawal Requests",
        "single" => "Withdrawal Request",
        "columns" => [
            "process" => "Process",
            "completed" => "Completed",
            "cancelled" => "Cancelled",
            "currency" => "Currency",
        ],
        "notification" => [
            "completed" => [
                "title" => "Withdrawal Completed",
                "body" => "The withdrawal request has been successfully processed.",
            ],
            "insufficient" => [
                "title" => "Insufficient Funds",
                "body" => "Wallet does not have enough balance to complete this withdrawal.",
            ],
            "cancelled" => [
                "title" => "Withdrawal Cancelled",
                "body" => "The withdrawal request has been successfully cancelled.",
            ],
            "amount_too_low" => [
                "title" => "Amount Too Low",
                "body" => "The amount you entered is less than the minimum allowed amount.",
            ],
            "amount_too_high" => [
                "title" => "Amount Too High",
                "body" => "The amount you entered exceeds the maximum allowed amount.",
            ],
            "balance_is_not_enough" => [
                "title" => "Wallet",
                "body" => "Balance Is Not Enough",
            ],
            "request_in_progress" => [
                "title" => "Request In Progress",
                "body" => "You already have a pending or processing withdrawal request. Please wait until it is completed.",
            ]
        ]
    ],
    "forms" => [
        "section" => [
            "information" => "Form Information"
        ],
        "title" => "Form Builder",
        "single" => "Form",
        "columns" => [
            "type" => "Type",
            "method" => "Method",
            "title" => "Title",
            "key" => "Key",
            "description" => "Description",
            "endpoint" => "Endpoint",
            "is_active" => "Is Active",
        ],
        "fields" => [
            "title" => "Fields",
            "single" => "Field",
            "columns" => [
                "type" => "Type",
                "name" => "Name",
                "group" => "Group",
                "default" => "Default",
                "is_relation" => "Is Relation",
                "relation_name" => "Relation Name",
                "relation_column" => "Relation Column",
                "sub_form" => "Sub Form",
                "is_multi" => "Is Multi",
                "has_options" => "Has Options",
                "options" => "Options",
                "label" => "Label",
                "value" => "Value",
                "placeholder" => "Placeholder",
                "hint" => "Hint",
                "is_required" => "Is Required",
                "required_message" => "Required Message",
                "has_validation" => "Has Validation",
                "validation" => "Validation",
                "rule" => "Rule",
                "message" => "Message"
            ],
            "tabs" => [
                "general" => "General",
                "options" => "Options",
                "validation" => "Validation",
                "relation" => "Relation",
                "labels" => "Labels",
            ],
            "actions" => [
                "preview" => "Preview",
            ]
        ],
        "requests" => [
            "title" => "Form Requests",
            "single" => "Request",
            "columns" => [
                "status" => "Status",
                "description" => "Description",
                "time" => "Time",
                "date" => "Date",
                "payload" => "Payload",
                "pending" => "Pending",
                "processing" => "Processing",
                "completed" => "Completed",
                "cancelled" => "Cancelled",
                "username" => "Username",
                "method_name" => "Method Name",
                "amount" => "Amount",
                "currency" => "Currency",
                "rate" => "Rate",
                "rate" => "Rate",
                "final_amount" => "Final Amount",
            ]
        ]
    ],
];
