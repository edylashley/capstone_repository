<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('records automated pdf validation results', function () {
    Storage::fake('public');

    $student = User::factory()->create(['role' => User::ROLE_STUDENT, 'is_active' => true]);
    $adviser = User::factory()->create(['role' => User::ROLE_ADVISER, 'is_active' => true]);

    // Fake validator that marks valid
    $fakeValidator = new class {
        public function validate($path) {
            return ['valid' => true, 'notes' => ['found approval page']];
        }
    };

    $this->instance(App\Services\PDFValidator::class, $fakeValidator);

    // Stub FileScanner to return OK for this test environment (avoid failing when clamscan is not installed)
    $fakeScannerOk = new class {
        public function scan($path) { return ['ok' => true, 'notes' => 'No threats found']; }
    };
    $this->instance(App\Services\FileScanner::class, $fakeScannerOk);

    $pdf = UploadedFile::fake()->create('manuscript.pdf', 100, 'application/pdf');

    // ensure student can access create page
    $this->actingAs($student)->get('/projects/create')->assertStatus(200);

    $response = $this->actingAs($student)->post('/projects', [
        'title' => 'Validation test',
        'abstract' => 'abstract',
        'year' => date('Y'),
        'adviser_id' => $adviser->id,
        'authors' => [$student->id],
        'manuscript' => $pdf,
        'acknowledge_policy' => '1',
    ]);

    \Log::debug('Submission response', ['status' => $response->status(), 'content' => $response->getContent()]);

    $response->assertStatus(201);

    $projectId = $response->json('project_id');
    $this->assertDatabaseHas('projects', ['id' => $projectId, 'manuscript_validated' => 1]);
});

it('records file scan failures', function () {
    Storage::fake('public');

    $student = User::factory()->create(['role' => User::ROLE_STUDENT, 'is_active' => true]);
    $adviser = User::factory()->create(['role' => User::ROLE_ADVISER, 'is_active' => true]);

    // Fake scanner that returns not ok
    $fakeScanner = new class {
        public function scan($path) {
            return ['ok' => false, 'notes' => 'infected'];
        }
    };

    $this->instance(App\Services\FileScanner::class, $fakeScanner);

    // Make validator benign
    $fakeValidator = new class {
        public function validate($path) { return ['valid' => true, 'notes' => ['ok']]; }
    };
    $this->instance(App\Services\PDFValidator::class, $fakeValidator);

    $pdf = UploadedFile::fake()->create('manuscript.pdf', 100, 'application/pdf');

    // ensure student can access create page
    $this->actingAs($student)->get('/projects/create')->assertStatus(200);

    // Simulate API/JSON client (assert JSON response)
    $response = $this->actingAs($student)->postJson('/projects', [
        'title' => 'Scan test',
        'abstract' => 'abstract',
        'year' => date('Y'),
        'adviser_id' => $adviser->id,
        'authors' => [$student->id],
        'manuscript' => $pdf,
        'acknowledge_policy' => '1',
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment(['message' => 'File scan failed: upload blocked']);

    // Ensure no project was created for the failed upload
    $this->assertDatabaseMissing('projects', ['title' => 'Scan test']);

    // Also check web-form behavior: non-JSON requests should redirect back with an error
    $pdf2 = UploadedFile::fake()->create('manuscript.pdf', 100, 'application/pdf');
    $response = $this->actingAs($student)->post('/projects', [
        'title' => 'Scan test 2',
        'abstract' => 'abstract',
        'year' => date('Y'),
        'adviser_id' => $adviser->id,
        'authors' => [$student->id],
        'manuscript' => $pdf2,
        'acknowledge_policy' => '1',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('manuscript');
    $this->assertDatabaseMissing('projects', ['title' => 'Scan test 2']);
});