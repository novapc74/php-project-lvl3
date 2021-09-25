<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class UrlTest extends TestCase
{
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

    public function testUrlsRoot(): void
    {
        $response = $this->get(route('root'));
        $response->assertOk();
    }

    public function testUrlsIndex(): void
    {
        $response = $this->get(route('urls.index'));
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
