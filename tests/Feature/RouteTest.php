<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class RouteTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $created_at = now();
        $updated_at = $created_at;
        $this->urlData = [
            'name' => 'https://www.test.com',
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        ];
        $this->id = DB::table('urls')->insertGetId($this->urlData);
    }

    public function testUrlsIndex(): void
    {
        $response = $this->get(route('urls.index'));
        $response->assertOk()->assertViewIs('index')->assertStatus(200);
    }

    public function testUrlsStore(): void
    {
        $response = $this->post('/urls', ['url' => $this->urlData]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('urls', $urlData);
    }

    public function testUrlsShow(): void
    {
        $response = $this->get(route('urls.show'));
        $response->assertOk()->assertViewIs('urls')->assertStatus(200);
    }

    public function testUrlShow(): void
    {
        $response = $this->get(route('url.show', $this->id));
        $response->assertOk()->assertViewIs('url')->assertStatus(200);
    }

    public function testUrlsChecksStore(): void
    {
        $body = file_get_contents(realpath(__DIR__ . '/fixtures/htmlTest.html'));
        Http::fake(function ($request) use ($body) {
            Http::response($body, 200);
        });
        $response = $this->post('/urls/{id}/checks', ['id' => $this->id]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('url_checks', ['url_id' => $this->id]);
    }
}
