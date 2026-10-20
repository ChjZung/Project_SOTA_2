<?php

return [
    // ISO 8601 with an explicit timezone; expired deals display 00 in every box.
    'deal_ends_at' => env('STOREFRONT_DEAL_ENDS_AT', '2026-12-31T23:59:59+07:00'),
];
