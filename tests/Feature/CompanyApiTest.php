<?php

namespace Tests\Feature;

use App\Enums\CompanyStatus;
use App\Models\Company;
use App\Models\Version;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_company()
    {
        $payload = [
            'name' => 'Test Company',
            'edrpou' => '12345678',
            'address' => 'Kyiv, Ukraine'
        ];

        $response = $this->postJson('/api/company', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => CompanyStatus::CREATED->value,
                'version' => 1
            ]);

        $this->assertDatabaseHas('companies', ['edrpou' => '12345678']);
        $this->assertDatabaseHas('versions', ['version_number' => 1]);
    }

    public function test_can_update_company_and_create_new_version()
    {
        $company = Company::factory()->create(['edrpou' => '12345678']);

        $payload = [
            'name' => 'Updated Name',
            'edrpou' => '12345678',
            'address' => $company->address
        ];

        $response = $this->postJson('/api/company', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => CompanyStatus::UPDATED->value,
                'version' => 2
            ]);

        $this->assertEquals(2, $company->versions()->count());
    }

    public function test_returns_duplicate_if_data_is_same()
    {
        $company = Company::factory()->create([
            'name' => 'Same Name',
            'edrpou' => '12345678',
            'address' => 'Same Address'
        ]);

        $payload = [
            'name' => 'Same Name',
            'edrpou' => '12345678',
            'address' => 'Same Address'
        ];

        $response = $this->postJson('/api/company', $payload);

        $response->assertStatus(200)
            ->assertJson(['status' => CompanyStatus::DUPLICATE->value]);

        $this->assertEquals(1, $company->versions()->count());
    }

    public function test_version_contains_correct_polymorphic_data()
    {
        $payload = [
            'name' => 'Test Corp',
            'edrpou' => '99999999',
            'address' => 'Test Address'
        ];

        $this->postJson('/api/company', $payload);

        $this->assertDatabaseHas('versions', [
            'versionable_type' => Company::class,
            'version_number' => 1,
        ]);

        $version = Version::first();
        $this->assertEquals('Test Corp', $version->data['name']);
    }

    public function test_can_get_company_versions_history()
    {
        $company = Company::factory()->create(['edrpou' => '11223344']);
        $company->update(['name' => 'New Name']);

        $response = $this->getJson("/api/company/11223344/versions");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'versions')
            ->assertJsonPath('versions.0.version_number', 2);
    }

}
