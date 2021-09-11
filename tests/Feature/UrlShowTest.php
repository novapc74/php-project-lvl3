<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UrlShowTest extends TestCase
{
    use RefreshDatabase;

    private int $id;

    public function setUp(): void
    {
        parent::setUp();
        $created = now();
        $updated = $created;
        $urlData = [
            'name' => 'https://www.test.com',
            'created_at' => $created,
            'updated_at' => $updated,
        ];
        $this->id = DB::table('urls')->insertGetId($urlData);
    }

    public function testUrlsIndex(): void
    {
        $response = $this->get(route('urls.index'));
        $response->assertOk();
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
