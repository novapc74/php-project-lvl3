<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class UrlCheckTest extends TestCase
{
    use RefreshDatabase;

    private int $id;

    public function setUp(): void
    {
        parent::setUp();
        $created = now();
        $updated = $created;
        $urlData = [
            'name' => 'http://www.dinamovki.ru',
            'created_at' => $created,
            'updated_at' => $updated,
        ];
        $this->id = DB::table('urls')->insertGetId($urlData);
    }

    public function testUrlCheck(): void
    {
        $body = (string)(file_get_contents(__DIR__ . '/../fixtures/htmlTest.html'));

        Http::fake(fn ($request) => Http::response($body, 200));

        $response = $this->post(route('url.check', [$this->id]));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect()->assertStatus(302);
        $this->assertDatabaseHas('url_checks', ['url_id' => $this->id]);
    }
}
