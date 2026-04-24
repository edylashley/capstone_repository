<?php

namespace App\Services;

class FileScanner
{
    /**
     * Run signature checks, manual heuristics, and ClamAV if available.
     * Returns array: ['ok' => bool, 'notes' => string]
     */
    public function scan(string $path, string $extractedText = ''): array
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
        // LAYER 3: Malicious Pattern Scanning (Top 10 Heuristics)
        // ═══════════════════════════════════════════════════════════════
        $contents = file_get_contents($path);
        $extractedText = '';

        // If it's a PDF, extract text to catch threats hidden by formatting
        if ($extension === 'pdf') {
            $pdftotext = $this->which('pdftotext');
            if ($pdftotext) {
                $extractedText = (string) shell_exec("\"$pdftotext\" -q -layout " . escapeshellarg($path) . " -");
            }
        }

        $searchSpace = $contents . "\n" . $extractedText;

        $signatures = [
            'PHP Web Shell (eval+base64)' => ['contains' => 'eval(base64_decode'],
            'PHP Web Shell (system+input)' => ['contains' => 'system($_'],
            'PHP Web Shell (exec+input)' => ['contains' => 'exec($_'],
            'PHP Web Shell (shell_exec+input)' => ['contains' => 'shell_exec($_'],
            'JavaScript Alert Prank' => ['contains' => '<script>alert('],
            'JavaScript Document Cookie' => ['contains' => 'document.cookie'],
            'PowerShell Download Cradle' => ['regex' => '/powershell[^;]*\-[eE].*downloadstring/i'],
            'EICAR Antivirus Test File' => ['contains' => 'X50!P%@AP[4\PZX54(P^)7CC)7}$EICAR-STANDARD-ANTIVIRUS-TEST-FILE!$H+H*'],
            'EICAR Antivirus (Fuzzy)' => ['contains' => 'EICAR-STANDARD-ANTIVIRUS-TEST-FILE'],
        ];

        foreach ($signatures as $name => $sig) {
            if (isset($sig['exact']) && strpos($searchSpace, $sig['exact']) !== false) {
                return ['ok' => false, 'notes' => "Threat detected: {$name}."];
            }
            if (isset($sig['contains']) && stripos($searchSpace, $sig['contains']) !== false) {
                return ['ok' => false, 'notes' => "Threat detected: {$name}."];
            }
            if (isset($sig['regex']) && preg_match($sig['regex'], $searchSpace)) {
                return ['ok' => false, 'notes' => "Threat detected: {$name}."];
            }
        }

        // ═══════════════════════════════════════════════════════════════
        // LAYER 4: ClamAV Engine (if enabled)
        // ═══════════════════════════════════════════════════════════════
        if (config('repository.filescan_enabled', false)) {
            $clamscanPath = config('repository.clamscan_path', 'clamscan');
            $executable = $this->which($clamscanPath);

            if (!$executable) {
                return ['ok' => false, 'notes' => "ClamAV not found: {$clamscanPath}", 'error_type' => 'system'];
            }

            // Standardize path for Windows ClamAV
            $realPath = realpath($path) ?: $path;
            $isClamd = str_contains(strtolower($executable), 'clamdscan');
            
            // STAGE 1: Scan the raw file
            $args = ['--no-summary'];
            if ($isClamd) $args[] = '--stream';
            $args[] = $realPath;

            $result = \Illuminate\Support\Facades\Process::timeout(120)->run(array_merge([$executable], $args));

            // STAGE 2: Deep Analysis (Decompression Fallback)
            // If raw scan was clean, we try to uncompress PDF streams manually to expose hidden threats
            if ($result->successful() && $extension === 'pdf') {
                $rawContents = file_get_contents($path);
                $uncompressedParts = "";
                
                // Simple regex to find FlateDecode streams which is where most text/scripts are hidden
                if (preg_match_all('/stream[\r\n]+(.*?)[\r\n]+endstream/s', $rawContents, $matches)) {
                    foreach ($matches[1] as $stream) {
                        try {
                            // Try standard gzuncompress
                            $decompressed = @gzuncompress($stream);
                            
                            // If that fails, try zlib_decode (handles more formats)
                            if (!$decompressed && function_exists('zlib_decode')) {
                                $decompressed = @zlib_decode($stream);
                            }

                            // If still fails, it might be raw deflate (no headers)
                            if (!$decompressed) {
                                $decompressed = @gzinflate($stream);
                            }

                            if ($decompressed) {
                                $uncompressedParts .= $decompressed . "\n";
                                
                                // RECURSIVE HEURISTIC CHECK: 
                                // Check if the decompressed part itself contains a known threat signature
                                foreach ($signatures as $name => $sig) {
                                    if (isset($sig['contains']) && stripos($decompressed, $sig['contains']) !== false) {
                                        return ['ok' => false, 'notes' => "Threat detected in compressed stream: {$name}."];
                                    }
                                }
                            }
                        } catch (\Throwable $e) {}
                    }
                }

                $scanTarget = $extractedText . "\n" . $uncompressedParts;
                
                if (!empty(trim($scanTarget))) {
                    $tmpFile = tempnam(sys_get_temp_dir(), 'pdf_deep_');
                    file_put_contents($tmpFile, $scanTarget);
                    
                    $args = ['--no-summary'];
                    if ($isClamd) $args[] = '--stream';
                    $args[] = $tmpFile;

                    $deepResult = \Illuminate\Support\Facades\Process::timeout(60)->run(array_merge([$executable], $args));
                    
                    if (!$deepResult->successful()) {
                        $result = $deepResult; 
                        $realPath = "Deep Scanned Content (Uncompressed)"; 
                    }
                    @unlink($tmpFile);
                }
            }

            // Log for debugging
            \Illuminate\Support\Facades\Log::debug("ClamAV Scan Result", [
                'exit_code' => $result->exitCode(),
                'output'    => $result->output(),
            ]);

            if (!$result->successful()) {
                $output = $result->output() ?: $result->errorOutput();
                
                // ClamAV Exit Codes: 1 = Virus Found, anything else = Error
                $isThreat = ($result->exitCode() === 1);
                
                // Sanitize output (remove full paths)
                $sanitized = str_replace([$realPath, dirname($realPath)], [basename($realPath), '...'], $output);

                return [
                    'ok' => false,
                    'notes' => trim($sanitized) ?: "Scanner error (Code: {$result->exitCode()})",
                    'error_type' => $isThreat ? 'threat' : 'system'
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
        $output = shell_exec($checker . ' ' . escapeshellarg($cmd));
        if (!$output) return null;

        // 'where' on Windows can return multiple lines; take the first one
        $path = trim(explode("\n", $output)[0]);
        return $path !== '' ? $path : null;
    }
}
