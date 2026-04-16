<?php

return [
    'min_manuscript_pages' => env('MIN_MANUSCRIPT_PAGES', 5),
    'manuscript_required_keywords' => explode(',', env('MANUSCRIPT_REQUIRED_KEYWORDS', 'approval,approved,approval page,signature,approved by')),

    // File scanning
    'filescan_enabled' => env('FILESCAN_ENABLED', false),
    'clamscan_path' => env('CLAMSCAN_PATH', 'clamscan'),
];
