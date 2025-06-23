<?php

return [
    // strategy used when more than chunk_size rows must be processed
    'default_strategy' => 'sync', // sync|queue

    // number of records processed before jobs are chunked and dispatched
    'chunk_size' => 1000,

    // queue connection used when dispatching jobs
    'queue_connection' => env('QUEUE_CONNECTION', 'sync'),

    // array of pivot table names that contain soft delete columns
    'pivot_tables' => [],
];
