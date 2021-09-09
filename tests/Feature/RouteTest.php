<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RouteTest extends TestCase
{
    use RefreshDatabase;

    private int $id;

    public function setUp(): void
    {
        parent::setUp();
        $created_at = now();
        $updated_at = $created_at;
        $urlData = [
            'name' => 'https://www.test.com',
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        ];
        $this->id = DB::table('urls')->insertGetId($urlData);
    }

    public function testUrlsIndex(): void
    {
        $response = $this->get(route('urls.index'));
        $response->assertOk();
    }

    public function testUrlsStore(): void
    {
        $urlData = [
            'name' => 'https://www.test.com',
        ];
        $response = $this->post(route('urls.store', ['url' => $urlData]));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect()->assertStatus(302);
        $this->assertDatabaseHas('urls', $urlData);
    }

    public function testInvalidUrlsStore(): void
    {
        $newData = [
            'id' => 100,
            'name' => 'https://www.fake.com',
        ];
        $response = $this->post(route('urls.store', ['url' => $newData]));
        $this->assertDatabaseMissing('urls', $newData);
    }

    public function testUrlsShow(): void
    {
        $response = $this->get(route('urls.show'));
        $response->assertOk();
    }

    public function testUrlShow(): void
    {
        $response = $this->get(route('url.show', $this->id));
        $response->assertOk();
    }

    public function testInvalidUrlShow(): void
    {
        $invalidId = [1000];
        $response = $this->get(route('url.show', $invalidId));
        $response->assertNotFound();
    }
}
