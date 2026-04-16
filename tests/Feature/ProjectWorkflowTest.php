<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('runs the project submission -> verification -> publish workflow', function () {
    Storage::fake('public');

    $student = User::factory()->create(['role' => User::ROLE_STUDENT, 'is_active' => true]);
    $adviser = User::factory()->create(['role' => User::ROLE_ADVISER, 'is_active' => true]);
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'is_active' => true]);

    // Student submits project
    $pdf = UploadedFile::fake()->create('manuscript.pdf', 100, 'application/pdf');

    $response = $this->actingAs($student)->post('/projects', [
        'title' => 'Test Project',
        'abstract' => 'Abstract content',
        'year' => date('Y'),
        'adviser_id' => $adviser->id,
        'authors' => [$student->id],
        'manuscript' => $pdf,
        'acknowledge_policy' => '1',
    ]);

    $response->assertStatus(201);

    $projectId = $response->json('project_id');
    $this->assertDatabaseHas('projects', ['id' => $projectId, 'status' => 'pending']);

    // Adviser verifies and recommends
    $response = $this->actingAs($adviser)->post("/projects/{$projectId}/verify", [
        'recommended' => true,
        'notes' => 'Looks good',
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('projects', ['id' => $projectId, 'status' => 'verified']);

    // Admin publishes
    $response = $this->actingAs($admin)->post("/admin/projects/{$projectId}/publish");
    $response->assertStatus(302);
    $this->assertDatabaseHas('projects', ['id' => $projectId, 'status' => 'published', 'is_published' => 1]);
});
