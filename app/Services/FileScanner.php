<?php

namespace App\Services;

class FileScanner
{
    /**
     * Run signature checks, manual heuristics, and ClamAV if available.
     * Returns array: ['ok' => bool, 'notes' => string]
     */
    public function scan(string $path): array
    {
        // 1. Basic properties
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        // Use mime_content_type if available to get true mime, else fallback
        $mimeType = function_exists('mime_content_type') ? mime_content_type($path) : 'application/octet-stream';

        // ═══════════════════════════════════════════════════════════════
        // LAYER 1: Dangerous File Types
        // ═══════════════════════════════════════════════════════════════
        $dangerousExtensions = ['exe', 'bat', 'cmd', 'ps1', 'vbs', 'js', 'scr', 'com', 'pif', 'msi', 'hta', 'cpl', 'inf', 'reg', 'ws', 'wsf', 'wsc', 'wsh', 'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phar'];
        $dangerousMimes = ['application/x-msdownload', 'application/x-executable', 'application/x-dosexec', 'application/x-msdos-program', 'application/x-php', 'text/x-php'];

        if ($extension && in_array($extension, $dangerousExtensions)) {
            return ['ok' => false, 'notes' => "Blocked: Extension '.{$extension}' is a dangerous executable or script type."];
        }
        if (in_array($mimeType, $dangerousMimes)) {
            return ['ok' => false, 'notes' => "Blocked: MIME type indicates an executable binary or server-side script."];
        }

        // ═══════════════════════════════════════════════════════════════
        // LAYER 2: Magic Bytes Analysis 
        // ═══════════════════════════════════════════════════════════════
        $header = file_get_contents($path, false, null, 0, 16);
        $signature = $this->detectFileType($header);

        if ($signature === 'PE Executable (EXE/DLL)' && !in_array($extension, ['exe', 'dll', 'sys', 'drv'])) {
            return ['ok' => false, 'notes' => "Blocked: Disguised File - PE Executable disguised as .{$extension}."];
        }

        // ═══════════════════════════════════════════════════════════════
        // LAYER 3: Malicious Pattern Scanning
        // ═══════════════════════════════════════════════════════════════
        $contents = file_get_contents($path);

        $signatures = [
            //'EICAR Test Virus' => ['exact' => 'X5O!P%@AP[4\PZX54(P^)7CC)7}$EICAR-STANDARD-ANTIVIRUS-TEST-FILE!$H+H*'],
            'PHP Web Shell (eval)' => ['contains' => 'eval('],
            'PHP Web Shell (system)' => ['contains' => 'system('],
            'PHP Web Shell (exec)' => ['contains' => 'exec('],
            'JavaScript eval injection' => ['contains' => 'eval('],
            'PowerShell Download Cradle' => ['regex' => '/powershell[^;]*\-[eE].*downloadstring/i'],
        ];

        foreach ($signatures as $name => $sig) {
            if (isset($sig['exact']) && strpos($contents, $sig['exact']) !== false) {
                return ['ok' => false, 'notes' => "Threat detected: {$name}."];
            }
            if (isset($sig['contains']) && stripos($contents, $sig['contains']) !== false) {
                return ['ok' => false, 'notes' => "Threat detected: {$name}."];
            }
            if (isset($sig['regex']) && preg_match($sig['regex'], $contents)) {
                return ['ok' => false, 'notes' => "Threat detected: {$name}."];
            }
        }

        // ═══════════════════════════════════════════════════════════════
        // LAYER 4: ClamAV Engine (if enabled)
        // ═══════════════════════════════════════════════════════════════
        if (config('repository.filescan_enabled', false)) {
            $clamscan = config('repository.clamscan_path', 'clamscan');
            $which = $this->which($clamscan);
            if (!$which) {
                return [
                    'ok' => false, 
                    'notes' => "ClamAV not found at path: {$clamscan}",
                    'error_type' => 'system'
                ];
            }

            $cmd = escapeshellarg($which) . ' --no-summary ' . escapeshellarg($path);
            $output = [];

            $originalLimit = (int) ini_get('max_execution_time');
            set_time_limit(300); // 5 minutes for slow servers
            @exec($cmd . ' 2>&1', $output, $exit);
            set_time_limit($originalLimit);

            if ($exit !== 0) {
                $rawNotes = implode('\n', $output);
                // Sanitize: Replace the full path with just the filename
                $sanitizedNotes = str_replace($path, basename($path), $rawNotes);
                
                // ClamAV Exit Codes: 0 = Clean, 1 = Virus Found, 2 = Error occurred
                $errorType = ($exit === 1) ? 'threat' : 'system';

                return [
                    'ok' => false, 
                    'notes' => $sanitizedNotes,
                    'error_type' => $errorType
                ];
            }
        }

        return ['ok' => true, 'notes' => 'Passed basic heuristics and signature scanning'];
    }

    private function detectFileType(string $header): string
    {
        if (empty($header))
            return 'Empty / Unknown';
        $hex = bin2hex(substr($header, 0, 8));

        $sigs = [
            '25504446' => 'PDF Document',
            '504b0304' => 'ZIP Archive',
            '4d5a' => 'PE Executable (EXE/DLL)',
            'ffd8ff' => 'JPEG Image',
            '89504e47' => 'PNG Image',
        ];

        foreach ($sigs as $magic => $type) {
            if (str_starts_with($hex, $magic))
                return $type;
        }
        return 'Unknown / Plain Text';
    }

    protected function which(string $cmd): ?string
    {
        try {
            if (strpos($cmd, '/') !== false || strpos($cmd, '\\') !== false) {
                if (file_exists($cmd))
                    return $cmd;
                $base = base_path($cmd);
                if (file_exists($base))
                    return $base;
            }
        } catch (\Throwable $e) {
        }

        $checker = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';
        $path = trim((string) shell_exec($checker . ' ' . escapeshellarg($cmd)));
        return $path !== '' ? $path : null;
    }
}
