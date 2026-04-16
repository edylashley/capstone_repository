<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SecurityScanDemoController extends Controller
{
    /**
     * Show the Security Scan Demo page.
     */
    public function index()
    {
        return view('admin.security-scan-demo');
    }

    /**
     * Run the security scan on an uploaded file and return detailed results.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200', // 50 MB max
        ]);

        $file = $request->file('file');

        if (!$file->isValid() || !$file->getRealPath() || !file_exists($file->getRealPath())) {
            return response()->json([
                'message' => 'Upload interrupted: Your local operating system (e.g., Windows Defender) intercepted and quarantined this file immediately before the server application could even process it! This confirms your host server has active low-level antivirus.',
            ], 422);
        }

        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $fileSize = $file->getSize();
        $extension = strtolower($file->getClientOriginalExtension());

        // Store temporarily
        $tempDir = 'security-scan-demo';
        $tempPath = $file->storeAs($tempDir, 'demo_' . time() . '_' . $originalName, 'public');
        $fullPath = Storage::disk('public')->path($tempPath);

        $results = [
            'file_info' => [
                'name' => $originalName,
                'size' => $fileSize,
                'size_human' => $this->humanFileSize($fileSize),
                'mime_type' => $mimeType,
                'extension' => $extension,
                'sha256' => hash_file('sha256', $fullPath),
            ],
            'steps' => [],
            'overall_verdict' => 'clean',
            'threats_found' => [],
        ];

        // ═══════════════════════════════════════════════════════════════
        // STEP 1: Content Integrity Hash
        // ═══════════════════════════════════════════════════════════════
        $step1 = [
            'name' => 'Content Integrity Hash',
            'description' => 'Generate SHA-256 hash for file integrity verification and duplicate detection',
            'status' => 'pass',
            'details' => [],
            'algorithm' => 'Compute SHA-256 cryptographic hash of full file contents. This hash is stored in the database and compared against future uploads to detect duplicate/plagiarized submissions.',
        ];

        $hash = hash_file('sha256', $fullPath);
        $step1['details'][] = "SHA-256: {$hash}";

        // Check for known hashes in the database
        $existingFile = \App\Models\ProjectFile::where('file_hash', $hash)->first();
        if ($existingFile) {
            $step1['status'] = 'warning';
            $step1['details'][] = "⚠ This file has been submitted before (duplicate detected)";
            $step1['details'][] = "Matching project ID: {$existingFile->project_id}";
        } else {
            $step1['details'][] = "✓ No duplicates found in database";
        }

        $results['steps'][] = $step1;

        // ═══════════════════════════════════════════════════════════════
        // STEP 2: File Type Validation
        // ═══════════════════════════════════════════════════════════════
        $step2 = [
            'name' => 'File Type Validation',
            'description' => 'Verify MIME type matches extension and check against dangerous file types',
            'status' => 'pass',
            'details' => [],
            'algorithm' => 'Compare detected MIME type against file extension. Check for known dangerous extensions (.exe, .bat, .cmd, .ps1, .vbs, .js, .scr, .com, .pif).',
        ];

        $dangerousExtensions = ['exe', 'bat', 'cmd', 'ps1', 'vbs', 'js', 'scr', 'com', 'pif', 'msi', 'hta', 'cpl', 'inf', 'reg', 'ws', 'wsf', 'wsc', 'wsh'];
        $dangerousMimes = ['application/x-msdownload', 'application/x-executable', 'application/x-dosexec', 'application/x-msdos-program'];

        $step2['details'][] = "Detected MIME: {$mimeType}";
        $step2['details'][] = "File Extension: .{$extension}";

        if (in_array($extension, $dangerousExtensions)) {
            $step2['status'] = 'fail';
            $step2['details'][] = "⚠ BLOCKED: Extension '.{$extension}' is a known dangerous executable type";
            $results['overall_verdict'] = 'malicious';
            $results['threats_found'][] = "Dangerous file extension: .{$extension}";
        } elseif (in_array($mimeType, $dangerousMimes)) {
            $step2['status'] = 'fail';
            $step2['details'][] = "⚠ BLOCKED: MIME type '{$mimeType}' indicates an executable binary";
            $results['overall_verdict'] = 'malicious';
            $results['threats_found'][] = "Dangerous MIME type: {$mimeType}";
        } else {
            $step2['details'][] = "✓ File type appears safe";
        }

        $results['steps'][] = $step2;

        // ═══════════════════════════════════════════════════════════════
        // STEP 3: Magic Bytes / File Signature Analysis
        // ═══════════════════════════════════════════════════════════════
        $step3 = [
            'name' => 'Magic Bytes Analysis',
            'description' => 'Read the first bytes of the file to verify its true type (cannot be spoofed by renaming)',
            'status' => 'pass',
            'details' => [],
            'algorithm' => 'Read byte header and compare against known magic number signatures: PDF (%PDF), ZIP (PK), PE/EXE (MZ), JPEG (FFD8FF), PNG (89504E47), etc.',
        ];

        $header = file_get_contents($fullPath, false, null, 0, 16);
        $hexHeader = strtoupper(bin2hex(substr($header, 0, 8)));
        $step3['details'][] = "First 8 bytes (hex): {$hexHeader}";

        $detectedType = $this->detectFileType($header);
        $step3['details'][] = "Detected signature: {$detectedType}";

        // Check for mismatch between extension and magic bytes
        $isMismatch = false;
        if ($detectedType === 'PE Executable (EXE/DLL)' && !in_array($extension, ['exe', 'dll', 'sys', 'drv'])) {
            $isMismatch = true;
            $step3['status'] = 'fail';
            $step3['details'][] = "⚠ CRITICAL: File has executable binary signature but is disguised as .{$extension}";
            $results['overall_verdict'] = 'malicious';
            $results['threats_found'][] = "Disguised executable: PE binary masquerading as .{$extension}";
        } elseif ($extension === 'pdf' && $detectedType !== 'PDF Document' && $detectedType !== 'Unknown') {
            $isMismatch = true;
            $step3['status'] = 'warning';
            $step3['details'][] = "⚠ WARNING: File claims to be PDF but signature indicates {$detectedType}";
        }

        if (!$isMismatch) {
            $step3['details'][] = "✓ File signature matches expected type";
        }

        $results['steps'][] = $step3;

        // ═══════════════════════════════════════════════════════════════
        // STEP 4: Malicious Content Pattern Scan
        // ═══════════════════════════════════════════════════════════════
        $step4 = [
            'name' => 'Malicious Content Pattern Scan',
            'description' => 'Search file contents for known malware signatures, exploit patterns, and dangerous payloads',
            'status' => 'pass',
            'details' => [],
            'algorithm' => 'Scan binary content for: EICAR test string, PHP shells (eval/exec/system/passthru), JavaScript injection (document.write, eval), macro exploits (AutoOpen, Document_Open), embedded executables.',
        ];

        $contents = file_get_contents($fullPath);
        $contentLower = strtolower($contents);
        $patternsChecked = 0;
        $patternsMatched = [];

        // Pattern library
        $signatures = [
            // EICAR Standard Anti-Malware Test File
            /*
            [
                'name' => 'EICAR Test Virus',
                'pattern' => 'X5O!P%@AP[4\\PZX54(P^)7CC)7}$EICAR-STANDARD-ANTIVIRUS-TEST-FILE!$H+H*',
                'type' => 'exact',
                'severity' => 'malicious',
                'description' => 'EICAR standard test file — industry-standard malware detection test',
            ],
            */
            // PHP Remote Shells
            [
                'name' => 'PHP Shell (eval+base64)',
                'pattern' => 'eval(base64_decode(',
                'type' => 'contains_ci',
                'severity' => 'malicious',
                'description' => 'Obfuscated PHP code execution — common in webshells',
            ],
            [
                'name' => 'PHP Shell (system)',
                'pattern' => '<?php system(',
                'type' => 'contains_ci',
                'severity' => 'malicious',
                'description' => 'PHP system() call — allows arbitrary command execution',
            ],
            [
                'name' => 'PHP Shell (exec)',
                'pattern' => '<?php exec(',
                'type' => 'contains_ci',
                'severity' => 'malicious',
                'description' => 'PHP exec() call — allows arbitrary command execution',
            ],
            [
                'name' => 'PHP Shell (passthru)',
                'pattern' => 'passthru(',
                'type' => 'contains_ci',
                'severity' => 'malicious',
                'description' => 'PHP passthru() — direct command execution with output',
            ],
            [
                'name' => 'PHP Shell (shell_exec)',
                'pattern' => 'shell_exec(',
                'type' => 'contains_ci',
                'severity' => 'warning',
                'description' => 'PHP shell_exec() — may allow command injection',
            ],
            // JavaScript Injection
            [
                'name' => 'JavaScript eval()',
                'pattern' => '<script>eval(',
                'type' => 'contains_ci',
                'severity' => 'malicious',
                'description' => 'Embedded JavaScript with eval() — XSS/injection vector',
            ],
            [
                'name' => 'JavaScript document.write',
                'pattern' => '<script>document.write(',
                'type' => 'contains_ci',
                'severity' => 'warning',
                'description' => 'Embedded JavaScript document.write — potential XSS',
            ],
            // Office Macro Exploits
            [
                'name' => 'VBA Macro AutoOpen',
                'pattern' => 'Sub AutoOpen()',
                'type' => 'contains_ci',
                'severity' => 'warning',
                'description' => 'VBA macro auto-execute on document open',
            ],
            [
                'name' => 'VBA Macro Document_Open',
                'pattern' => 'Sub Document_Open()',
                'type' => 'contains_ci',
                'severity' => 'warning',
                'description' => 'VBA macro auto-execute on document open',
            ],
            [
                'name' => 'PowerShell Download Cradle',
                'pattern' => 'powershell',
                'type' => 'regex',
                'regex' => '/powershell[^;]*\-[eE].*downloadstring/i',
                'severity' => 'malicious',
                'description' => 'PowerShell download cradle — typically used for malware delivery',
            ],
            // SQL Injection (in uploaded text files)
            [
                'name' => 'SQL Injection Pattern',
                'pattern' => "'; DROP TABLE",
                'type' => 'contains_ci',
                'severity' => 'warning',
                'description' => 'SQL injection attempt pattern detected',
            ],
            // Embedded PE binary
            [
                'name' => 'Embedded Windows Executable',
                'pattern' => 'This program cannot be run in DOS mode',
                'type' => 'contains',
                'severity' => 'warning',
                'description' => 'Contains embedded Windows PE executable (potential dropper)',
            ],
        ];

        foreach ($signatures as $sig) {
            $patternsChecked++;
            $matched = false;

            switch ($sig['type']) {
                case 'exact':
                    $matched = (strpos($contents, $sig['pattern']) !== false);
                    break;
                case 'contains':
                    $matched = (strpos($contents, $sig['pattern']) !== false);
                    break;
                case 'contains_ci':
                    $matched = (stripos($contents, $sig['pattern']) !== false);
                    break;
                case 'regex':
                    $matched = (bool) preg_match($sig['regex'], $contents);
                    break;
            }

            if ($matched) {
                $patternsMatched[] = $sig;
                $step4['details'][] = "⚠ DETECTED [{$sig['severity']}]: {$sig['name']} — {$sig['description']}";

                if ($sig['severity'] === 'malicious') {
                    $step4['status'] = 'fail';
                    $results['overall_verdict'] = 'malicious';
                    $results['threats_found'][] = $sig['name'];
                } elseif ($sig['severity'] === 'warning' && $step4['status'] !== 'fail') {
                    $step4['status'] = 'warning';
                    if ($results['overall_verdict'] !== 'malicious') {
                        $results['overall_verdict'] = 'suspicious';
                    }
                    $results['threats_found'][] = $sig['name'] . ' (suspicious)';
                }
            }
        }

        $step4['details'][] = "Patterns checked: {$patternsChecked}";
        $step4['details'][] = "Patterns matched: " . count($patternsMatched);

        if (count($patternsMatched) === 0) {
            $step4['details'][] = "✓ No known malicious patterns detected";
        }

        $results['steps'][] = $step4;

        // ═══════════════════════════════════════════════════════════════
        // STEP 5: ClamAV Scan (if available)
        // ═══════════════════════════════════════════════════════════════
        $step5 = [
            'name' => 'ClamAV Antivirus Engine',
            'description' => 'Run file through ClamAV antivirus engine for deep signature and heuristic analysis',
            'status' => 'skip',
            'details' => [],
            'algorithm' => 'Execute clamscan binary with the uploaded file. ClamAV checks against its database of 8M+ malware signatures, performs heuristic analysis and sandboxed emulation.',
        ];

        $scanner = app(\App\Services\FileScanner::class);
        $clamEnabled = config('repository.filescan_enabled', false);

        if (!$clamEnabled) {
            $step5['details'][] = "ℹ ClamAV integration is currently disabled in configuration";
            $step5['details'][] = "Config: FILESCAN_ENABLED = false";
            $step5['details'][] = "To enable: Set FILESCAN_ENABLED=true in .env and install ClamAV";
        } else {
            $clamResult = $scanner->scan($fullPath);
            if ($clamResult['ok']) {
                $step5['status'] = 'pass';
                $step5['details'][] = "✓ ClamAV: {$clamResult['notes']}";
            } else {
                $step5['status'] = 'fail';
                $step5['details'][] = "⚠ ClamAV ALERT: {$clamResult['notes']}";
                $results['overall_verdict'] = 'malicious';
                $results['threats_found'][] = "ClamAV: {$clamResult['notes']}";
            }
        }

        $results['steps'][] = $step5;

        // ═══════════════════════════════════════════════════════════════
        // ═══════════════════════════════════════════════════════════════
        // STEP 6: PDF-Specific Validation (if PDF)
        // ═══════════════════════════════════════════════════════════════
        $step6 = [
            'name' => 'PDF Structure Validation',
            'description' => 'Verify PDF structure, check for embedded JavaScript, encrypted content, and extract text',
            'status' => 'pass',
            'details' => [],
            'algorithm' => 'Use pdfinfo to check page count and encryption. Use pdftotext to extract content. Scan for required approval/signature keywords. Check for embedded JavaScript or action triggers in PDF.',
        ];

        if ($extension === 'pdf' || $mimeType === 'application/pdf') {
            // Check for dangerous PDF features
            if (stripos($contents, '/JavaScript') !== false || stripos($contents, '/JS ') !== false) {
                $step6['status'] = 'warning';
                $step6['details'][] = "⚠ PDF contains embedded JavaScript (potential exploit)";
                if ($results['overall_verdict'] !== 'malicious') {
                    $results['overall_verdict'] = 'suspicious';
                }
                $results['threats_found'][] = 'Embedded JavaScript in PDF';
            }

            if (stripos($contents, '/OpenAction') !== false || stripos($contents, '/AA ') !== false) {
                $step6['status'] = 'warning';
                $step6['details'][] = "⚠ PDF contains automatic actions (auto-execute on open)";
            }

            if (stripos($contents, '/Launch') !== false) {
                $step6['status'] = 'fail';
                $step6['details'][] = "⚠ CRITICAL: PDF contains /Launch action — can execute programs when opened";
                $results['overall_verdict'] = 'malicious';
                $results['threats_found'][] = 'PDF with /Launch action (can execute programs)';
            }

            // Run the PDFValidator
            $pdfValidator = app(\App\Services\PDFValidator::class);
            $validation = $pdfValidator->validate($fullPath);

            $step6['details'][] = "PDF Validation: " . ($validation['valid'] ? 'Passed' : 'Failed');
            foreach ($validation['notes'] as $note) {
                $step6['details'][] = $note;
            }

            if (!$validation['valid'] && $step6['status'] !== 'fail') {
                $step6['status'] = 'warning';
            }
        } else {
            $step6['status'] = 'skip';
            $step6['details'][] = "ℹ Skipped: File is not a PDF Document.";
        }

        $results['steps'][] = $step6;

        // Cleanup temp file
        Storage::disk('public')->delete($tempPath);

        return response()->json($results);
    }

    /**
     * Generate an EICAR test file for download.
     * The EICAR file is a harmless, industry-standard test for antivirus software.
     */
    public function downloadTestFile(Request $request, string $type)
    {
        switch ($type) {
            case 'eicar':
                // EICAR Standard Anti-Malware Test File
                $content = 'X5O!P%@AP[4\\PZX54(P^)7CC)7}$EICAR-STANDARD-ANTIVIRUS-TEST-FILE!$H+H*';
                $filename = 'eicar_test.txt';
                break;

            case 'php-shell':
                // Simulated PHP webshell (harmless text — just contains the pattern)
                $content = "<?php\n// This is a SIMULATED malicious PHP webshell for testing purposes only.\n// It does NOT execute anything.\neval(base64_decode('dGVzdA==')); // decoded: 'test'\nsystem('whoami');\npassthru('ls -la');\n?>\n";
                $filename = 'test_webshell.php.txt';
                break;

            case 'js-injection':
                // Simulated JavaScript injection file
                $content = "<html>\n<body>\n<script>eval('alert(1)');</script>\n<script>document.write('<img src=x onerror=alert(1)>');</script>\n</body>\n</html>\n";
                $filename = 'test_xss.html.txt';
                break;

            case 'clean-pdf':
                // Minimal valid PDF with approval text
                $content = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Contents 4 0 R>>endobj\n4 0 obj<</Length 44>>stream\nBT /F1 12 Tf 100 700 Td (Clean PDF - Approval Page) Tj ET\nendstream endobj\nxref\n0 5\ntrailer<</Size 5/Root 1 0 R>>\nstartxref\n0\n%%EOF";
                $filename = 'clean_test.pdf';
                break;

            case 'clean-text':
                // Clean harmless text file
                $content = "This is a clean, harmless text file.\nIt contains no malicious content.\nGenerated for security scan testing purposes.\nDate: " . now()->toDateTimeString() . "\n";
                $filename = 'clean_test.txt';
                break;

            default:
                abort(404, 'Unknown test file type');
        }

        return response($content)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Detect file type based on magic bytes.
     */
    private function detectFileType(string $header): string
    {
        $hex = bin2hex(substr($header, 0, 8));

        $signatures = [
            '25504446' => 'PDF Document',
            '504b0304' => 'ZIP Archive',
            '504b0506' => 'ZIP Archive (empty)',
            '504b0708' => 'ZIP Archive (spanned)',
            '4d5a' => 'PE Executable (EXE/DLL)',
            'ffd8ff' => 'JPEG Image',
            '89504e47' => 'PNG Image',
            '47494638' => 'GIF Image',
            '52617221' => 'RAR Archive',
            '377abcaf' => '7-Zip Archive',
            '1f8b' => 'GZIP Archive',
            'd0cf11e0' => 'Microsoft Office (OLE2)',
        ];

        foreach ($signatures as $magic => $type) {
            if (str_starts_with($hex, $magic)) {
                return $type;
            }
        }

        // Check for text-based formats
        $text = substr($header, 0, 5);
        if (str_starts_with($text, '<?php')) {
            return 'PHP Script';
        }
        if (str_starts_with($text, '<html') || str_starts_with($text, '<!DOC')) {
            return 'HTML Document';
        }
        if (str_starts_with($text, '<?xml')) {
            return 'XML Document';
        }

        return 'Unknown / Plain Text';
    }

    private function humanFileSize(int $bytes): string
    {
        if ($bytes < 1024)
            return $bytes . ' B';
        if ($bytes < 1048576)
            return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 2) . ' MB';
    }
}
