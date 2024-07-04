<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentsTest extends TestCase
{
    use RefreshDatabase;

    public function testItCanListExpiringDocuments()
    {
        $user = User::factory()
            ->has(Document::factory()->withExpiringSoon()->count(10))
            ->create();

        $this->actingAs($user);

        $this->getJson('/api/documents')
            ->assertJsonCount(10)
            ->assertSuccessful();
    }

    public function testItCanListADocument()
    {
        $user = User::factory()
            ->create();

        $document = Document::factory()
            ->for($user, 'owner')
            ->create();

        $this->actingAs($user);

        $this->getJson("/api/documents/{$document->id}")
            ->assertSuccessful();
    }

    public function testOnlyDocumentOwnersCanViewDocument()
    {
        $user = User::factory()
            ->create();

        $user2 = User::factory()
            ->create();

        $document = Document::factory()
            ->for($user2, 'owner')
            ->create();

        $this->actingAs($user);

        $this->get("/api/documents/{$document->id}")
            ->assertForbidden();
    }

    public function testItCanStoreADocument()
    {
        Storage::fake('local');
        $pdf = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $user = User::factory()
            ->create();

        $this->actingAs($user);

        $this->postJson('/api/documents', [
            'name' => 'Contract',
            'document' => $pdf,
        ])->assertSuccessful();
    }

    //    public function testItCanNotStoreADocumentWithExpiryInThePast()
    //    {
    //        $user = User::factory()
    //            ->create();
    //
    //        $this->actingAs($user);
    //
    //        $this->postJson('/api/documents', [
    //            'name' => 'Contract',
    //            'expires_at' => now()->subWeek()
    //        ])->assertInvalid();
    //    }
}
