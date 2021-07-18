<?php

namespace Tests\Feature;

use App\Services\EcwidService;
use Tests\TestCase;

class EcwiServiseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
    public function test_Class()
    {
        $class = new EcwidService();
        $this->assertIsObject($class);
    }

    public function test_ClassAttribute()
    {
        $this->assertClassHasAttribute('secret_key', EcwidService::class);
        $this->assertClassHasAttribute('shop_id', EcwidService::class);
        $this->assertClassHasAttribute('secret_token', EcwidService::class);
    }


}
