<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Helpers;

if (! function_exists('human_readable_file_size')) {
    function human_readable_file_size(int $bytes, int $decimals = 2): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        $factor = floor(log($bytes, 1024));

        return sprintf("%.{$decimals}f ", $bytes / pow(1024, $factor)) . [
                'B',
                'KB',
                'MB',
                'GB',
                'TB',
                'PB',
                'EB',
                'ZB',
            ][$factor];
    }
}
