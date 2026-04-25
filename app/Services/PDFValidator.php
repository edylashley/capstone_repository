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
        $keywordsMissing = false;
        $hasImageOnlyPage = false;
        // Try pdfinfo to get encryption status
        $isEncrypted = false;
        $pdfinfo = $this->which('pdfinfo');
        if ($pdfinfo) {
            $output = [];
            @exec(escapeshellcmd("$pdfinfo " . escapeshellarg($path)) . " 2>&1", $output, $exit);
            if ($exit === 0) {
                foreach ($output as $line) {
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
                if ($exit === 0 && !empty($output)) {
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

        $requiredKeywords = config('repository.manuscript_required_keywords', ['approval', 'approved', 'approval page', 'signature', 'approved by']);
        $found = [];
        $hasImageOnlyPage = false;

        if ($isBinaryFallback) {
            foreach ($requiredKeywords as $kw) {
                if (stripos($text, $kw) !== false) {
                    $found[] = $kw;
                }
            }
        } else {
            // Split by form feed char \f (ASCII 12) to identify pages
            $pagesContent = array_filter(explode("\f", trim($text)), function ($val) {
                return $val !== "";
            });

            foreach ($pagesContent as $index => $pageText) {
                if (strlen(trim($pageText)) < 20) {
                    $hasImageOnlyPage = true;
                    // Log specific image pages only if helpful
                    $notes[] = "(i) Page " . ($index + 1) . " might an image-only page.";
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
                    $notes[] = "[OK] Detected: $kw (Page " . implode(', ', $kwLocations) . ")";
                }
            }
        }

        // Total pages (we can count \f chars + 1)
        $pageCount = substr_count($text, "\f") + 1;

        $minPages = config('repository.min_manuscript_pages', 5);

        if (count($found) === 0) {
            // HYBRID LOGIC: Allow multi-page scanned documents but with a warning
            if ($hasImageOnlyPage && $pageCount >= $minPages) {
                $valid = true;
                $notes[] = "[i] Manual Verification Needed: Signatures/Keywords not found in text, but a possible scanned document was detected. Please check this page manually if it is the signed approval sheet.";
            } else {
                $valid = false;
                $keywordsMissing = true;

                if ($pageCount < $minPages) {
                    $notes[] = "[ERROR] Document too short ({$pageCount} pg). Manuscripts must be complete research works. (Min: {$minPages} pg)";
                } elseif (!$hasImageOnlyPage) {
                    $notes[] = "[ERROR] Required sections (Approval Sheet, signatures, etc.) not detected. Check manually for the Approval Sheet page.";
                } else {
                    $notes[] = "[ERROR] Content verification failed. No required keywords found even in scanned pages.";
                }
            }
        }

        return [
            'valid' => $valid,
            'notes' => $notes,
            'keywords_missing' => $keywordsMissing,
            'text' => trim($text),
            'page_count' => $pageCount
        ];
    }

    protected function which(string $cmd): ?string
    {
        $checker = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';
        $output = shell_exec($checker . ' ' . escapeshellarg($cmd));
        if (!$output)
            return null;

        // 'where' on Windows can return multiple lines; take the first one
        $path = trim(explode("\n", $output)[0]);
        return $path !== '' ? $path : null;
    }
}
