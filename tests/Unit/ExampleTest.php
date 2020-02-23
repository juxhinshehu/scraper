<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $this->assertTrue(true);
    }

    public function testProfile()
    {
        $profileId = 'realmadrid';

        $response = $this->get("/tiktok-profile-scraper/$profileId/");
        $response->assertStatus(200);
        $response->assertJson(['data' => "profile $profileId scrapped succesfully"]);
        $this->assertDatabaseHas('profiles', [
	        'user_id' => $profileId
	    ]);
    }

    public function testVideo()
    {
        $profileId = 'realmadrid';
        $videoId = '6721977173101579526';

        $response = $this->get("/tiktok-video-scraper/$profileId/$videoId");
        $response->assertStatus(200);
        $response->assertJson(['data' => "video $videoId scraped succesfully"]);
    }
}
