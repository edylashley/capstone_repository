<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    protected ?string $apiKey;
    protected string $model = 'gemini-embedding-2';
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key') ?? '';
    }

    /**
     * Generate an embedding vector for the given text.
     *
     * @param string $text The text to generate an embedding for.
     * @return array|null The embedding vector (768 floats) or null on failure.
     */
    public function generate(string $text): ?array
    {
        if (empty($this->apiKey) || $this->apiKey === 'your_api_key_here') {
            Log::warning('EmbeddingService: Gemini API key is not configured.');
            return null;
        }

        // Trim text to avoid exceeding token limits (~2048 tokens ≈ 8000 chars is safe)
        $text = mb_substr(trim($text), 0, 8000);

        if (empty($text)) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->post("{$this->baseUrl}/{$this->model}:embedContent?key={$this->apiKey}", [
                    'model' => "models/{$this->model}",
                    'content' => [
                        'parts' => [
                            ['text' => $text],
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $embedding = $response->json('embedding.values');

                if (is_array($embedding) && count($embedding) > 0) {
                    return $embedding;
                }

                Log::warning('EmbeddingService: Unexpected response structure.', [
                    'response' => $response->json(),
                ]);
                return null;
            }

            Log::error('EmbeddingService: API request failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('EmbeddingService: Exception during API call.', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Build the searchable text for a project from its abstract and keywords.
     */
    public function buildProjectText(\App\Models\Project $project): string
    {
        $parts = [];

        $parts[] = "Project Title: " . $project->title;

        if (!empty($project->authors_list)) {
            $parts[] = "Authors: " . $project->authors_list;
        }

        if (!empty($project->adviser_name)) {
            $parts[] = "Research Adviser: " . $project->adviser_name;
        }

        if (!empty($project->program)) {
            $parts[] = "Academic Program: " . $project->program;
        }

        // Add categories to context
        $categories = $project->categories->pluck('name')->toArray();
        if (!empty($project->custom_category)) {
            $categories[] = $project->custom_category;
        }
        if (!empty($categories)) {
            $parts[] = "Research Categories: " . implode(', ', $categories);
        }

        if (!empty($project->keywords)) {
            $parts[] = "Core Keywords: " . implode(', ', $project->keywords);
        }

        if (!empty($project->abstract)) {
            $parts[] = "Project Abstract/Summary: " . $project->abstract;
        }

        // Join with newlines to simulate a structured document
        return implode("\n\n", $parts);
    }

    /**
     * Calculate cosine similarity between two embedding vectors.
     *
     * @param array $a First embedding vector.
     * @param array $b Second embedding vector.
     * @return float Similarity score between -1 and 1 (higher = more similar).
     */
    public static function cosineSimilarity(array $a, array $b): float
    {
        if (count($a) !== count($b) || count($a) === 0) {
            return 0.0;
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] ** 2;
            $normB += $b[$i] ** 2;
        }

        $denominator = sqrt($normA) * sqrt($normB);

        return $denominator > 0 ? $dot / $denominator : 0.0;
    }
}
