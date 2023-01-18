<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    /** @test */
    public function test_index()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function test_index_ru()
    {
        $response = $this->get('ru');

        $response->assertStatus(200);
    }
    public function test_index_he()
    {
        $response = $this->get('he');

        $response->assertStatus(200);
    }
    public function test_getIcreditUrl()
    {
        $response = $this->post('orders/icredit');

        $response->assertStatus(200);
    }
    public function test_amoWebhok()
    {
        $response = $this->post('api/amocrm/amowebhok');

        $response->assertStatus(200);
    }
    public function test_ruInstock()
    {
        $response = $this->get('/ru/in_stock');

        $response->assertStatus(200);
    }
    public function test_crm()
    {
        $response = $this->post('crm');

        $response->assertStatus(200);
    }
}
