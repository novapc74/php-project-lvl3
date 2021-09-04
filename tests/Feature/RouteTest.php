<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RouteTest extends TestCase
{
    use RefreshDatabase;

    private array $urlData = [];
    private int $id;

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
        $response = $this->post(route('urls.store', ['url' => $this->urlData]));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect()->assertStatus(302);
        $this->assertDatabaseHas('urls', $this->urlData);
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
        $body = (string)(file_get_contents((string)(realpath(__DIR__ . '/fixtures/htmlTest.html'))));
        Http::fake(fn ($request) => Http::response($body));
        $response = $this->post(route('urlChecks.store', [$this->id]));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect()->assertStatus(302);
        $this->assertDatabaseHas('url_checks', ['url_id' => $this->id]);
    }
}
