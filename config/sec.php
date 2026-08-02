<?php

return [
    'user_agent' => env('SEC_USER_AGENT'),
    'archive_disk' => env('SEC_ARCHIVE_DISK', 'local'),
    'nport_base_url' => 'https://www.sec.gov/files/dera/data/form-n-port-data-sets',
    'timeout_seconds' => 900,
    'retry_attempts' => 3,
    'retry_delay_milliseconds' => 250,
];
