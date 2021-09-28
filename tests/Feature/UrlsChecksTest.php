<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\WithFaker;

class UrlsChecksTest extends TestCase
{
    private int $id;

    public function setUp(): void
    {
        parent::setUp();
        $created = now();
        $urlData = [
            'name' => 'http://www.dinamovki.ru',
            'created_at' => $created,
        ];
        $this->id = DB::table('urls')->insertGetId($urlData);
    }

    public function getFixtureFullPath(string $fixtureName): string
    {
        $parts = [__DIR__, '../fixtures', $fixtureName];
        return $path = realpath(implode('/', $parts));
    }

    public function testUrlChecks(): void
    {
        $pathToFixtures = (string)($this->getFixtureFullPath('htmlTest.html'));
        $body = (string)(file_get_contents($pathToFixtures));
        $checkData = [
            'url_id' => $this->id,
            'status_code' => '200',
            'keywords' => "Динамо, Новосибирск, Баскетбольный клуб",
            'description' => "День рождения отмечает менеджер БК 'Динамо'",
            'h1' => "С Днем рождения, Сергей Анатольевич!"
        ];

        Http::fake(fn ($request) => Http::response($body, 200));

        $response = $this->post(route('url.checks', [$this->id]));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect()->assertStatus(302);
        $this->assertDatabaseHas('url_checks', $checkData);
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
        $this->post(route('urls.store', ['url' => $newData]));
        $this->assertDatabaseMissing('urls', $newData);
    }
}
