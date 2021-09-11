<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlsStoreTest extends TestCase
{
    use RefreshDatabase;

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
}
