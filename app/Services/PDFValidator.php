<?php

namespace App\Services;

class PDFValidator
{
    /**
     * Validate a PDF file with simple heuristics:
     * - Check page count (if pdfinfo available)
     * - Extract text (pdftotext) and search for required keywords (approval, signature)
     * Returns array: ['valid' => bool, 'notes' => array<string>]
     */
    public function validate(string $path): array
    {
        $notes = [];
        $valid = true;
        $pageCountFailed = false;
        $keywordsMissing = false;

        // Try pdfinfo to get page count and encryption status
        $pages = null;
        $isEncrypted = false;
        $pdfinfo = $this->which('pdfinfo');
        if ($pdfinfo) {
            $output = [];
            @exec(escapeshellcmd("$pdfinfo " . escapeshellarg($path)) . " 2>&1", $output, $exit);
            if ($exit === 0) {
                foreach ($output as $line) {
                    if (stripos($line, 'Pages:') !== false) {
                        $parts = preg_split('/\s+/', trim($line));
                        $pages = (int) end($parts);
                    }
                    if (stripos($line, 'Encrypted:') !== false && stripos($line, 'yes') !== false) {
                        $isEncrypted = true;
                    }
                }
            }
        }

        if ($isEncrypted) {
            return [
                'valid' => false,
                'notes' => ['Critical Error: This PDF is password-protected. Please export a version without a password or editing restrictions and try again.'],
                'page_count_failed' => false,
                'keywords_missing' => true
            ];
        }

        // Minimum page check removed per user request: any page count is now acceptable.
        // We still keep $pageCountFailed = false for backwards compatibility with the return array.

        // Try to extract text using pdftotext
        $pdftotext = $this->which('pdftotext');
        $text = '';
        $isBinaryFallback = false;

        if ($pdftotext) {
            // Build command properly for Windows/Unix compatibility
            $escapedPath = escapeshellarg($path);
            
            // Use shell_exec to preserve form-feed characters (\f) which mark page breaks
            // standard pdftotext outputs \f between pages in main output
            $cmd = "\"$pdftotext\" -q -layout $escapedPath -";
            
            // Use shell_exec instead of exec to get raw output string with control chars
            $extracted = shell_exec($cmd);
            
            if ($extracted !== null && strlen($extracted) > 0) {
                $text = $extracted;
            } else {
                // Fallback to exec if shell_exec fails or returns null
                $output = [];
                @exec($cmd . " 2>&1", $output, $exit);
                if ($exit === 0 && ! empty($output)) {
                    $text = implode("\n", $output);
                } else {
                    $notes[] = "⚠ Text extraction failed (exit code: $exit). Using fallback method.";
                }
            }
        }

        // If no pdftotext or extraction failed, try a simple fallback: check file for keywords in binary blob (less reliable)
        if ($text === '') {
            $isBinaryFallback = true;
            $contents = @file_get_contents($path);
            if ($contents !== false) {
                // convert some binary parts to text-like representation
                $text = substr($contents, 0, 50000);
                if (!isset($notes[0]) || strpos($notes[0], 'Text extraction failed') === false) {
                    $notes[] = "ℹ Using binary fallback (pdftotext not available).";
                }
            }
        }

        // Scanned Image Check: If the system successfully ran pdftotext but found almost no actual words.
        if (!$isBinaryFallback) {
            // Remove extra whitespace just to measure actual characters
            $cleanText = trim(preg_replace('/\s+/', ' ', $text));
            // A typical 5+ page manuscript should have thousands of characters. If it has less than 150, it's likely a scan.
            if (strlen($cleanText) < 150) {
                $valid = false;
                $notes[] = 'Warning: No readable text detected. This appears to be a scanned image of a printed document. Please export your manuscript directly from Microsoft Word / Google Docs so the text is searchable.';
            }
        }

        $requiredKeywords = config('repository.manuscript_required_keywords', ['approval','approved','approval page','signature','approved by']);

        $found = [];
        $foundLocations = [];

        if ($isBinaryFallback) {
             foreach ($requiredKeywords as $kw) {
                if (stripos($text, $kw) !== false) {
                    $found[] = $kw;
                    $foundLocations[] = "$kw (Location unknown)";
                }
            }
        } else {
            // Split by form feed char \f (ASCII 12) to identify pages
            // If \f is missing (single page or layout mode weirdness), array will have 1 element
            $pagesContent = explode("\f", $text);
            $hasImageOnlyPage = false;

            foreach ($pagesContent as $index => $pageText) {
                // If a page has almost no text (less than 20 chars), it's likely a scan or image-only
                if (strlen(trim($pageText)) < 20) {
                    $hasImageOnlyPage = true;
                    $notes[] = "ℹ️ Page " . ($index + 1) . " detected as image-only/scanned. (Likely the Signed Approval Sheet)";
                }
            }

            foreach ($requiredKeywords as $kw) {
                $kwLocations = [];
                foreach ($pagesContent as $index => $pageText) {
                    if (stripos($pageText, $kw) !== false) {
                        $found[] = $kw;
                        $kwLocations[] = $index + 1;
                    }
                }
                
                if (!empty($kwLocations)) {
                    $pageList = implode(', ', $kwLocations);
                    $notes[] = "✅ Detected: $kw (Pages $pageList)";
                }
            }

            if ($hasImageOnlyPage && count($found) === 0) {
                $notes[] = "💡 Manual Audit Advised: Keywords not found in text, but an image-only page was detected. Please verify signatures in the HD Viewer.";
            }
        }

        if (count($found) === 0 && !$hasImageOnlyPage) {
            $valid = false;
            $keywordsMissing = true;
            $notes[] = '❌ Approval page or signatures not detected (no required keywords found).';
        } elseif (count($found) === 0 && $hasImageOnlyPage) {
            // If we have an image page, we mark it as "Valid but needs eyes"
            $valid = true; 
            $notes[] = '⚠️ Automated scan inconclusive due to scanned page. Human verification required.';
        }

        return [
            'valid' => $valid,
            'notes' => $notes,
            'page_count_failed' => $pageCountFailed,
            'keywords_missing' => $keywordsMissing,
            'text' => trim($text),
        ];
    }

    protected function which(string $cmd): ?string
    {
        // Use 'where' on Windows, 'which' on Unix
        $checker = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';
        $path = trim((string) shell_exec($checker . ' ' . escapeshellarg($cmd)));
        return $path !== '' ? $path : null;
    }
}
