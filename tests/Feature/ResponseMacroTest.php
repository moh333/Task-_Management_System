<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Response;
use Tests\TestCase;

class ResponseMacroTest extends TestCase
{
    public function test_success_macro_returns_data_as_object(): void
    {
        $response = Response::success(['key' => 'value'], 'Operation successful', 200);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'success' => true,
            'message' => 'Operation successful',
            'data' => ['key' => 'value'],
        ], $response->getData(true));
    }

    public function test_success_macro_returns_data_as_empty_array(): void
    {
        $response = Response::success([], 'Logged out successfully', 200);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'success' => true,
            'message' => 'Logged out successfully',
            'data' => [],
        ], $response->getData(true));
    }

    public function test_response_omits_data_key_when_data_is_null(): void
    {
        $response = Response::success(null, 'Action completed');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'success' => true,
            'message' => 'Action completed',
        ], $response->getData(true));
        $this->assertArrayNotHasKey('data', $response->getData(true));
    }

    public function test_error_macro_omits_data_key_when_null(): void
    {
        $response = Response::error('Operation failed', 400);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals([
            'success' => false,
            'message' => 'Operation failed',
        ], $response->getData(true));
        $this->assertArrayNotHasKey('data', $response->getData(true));
    }

    public function test_unauthorized_macro_returns_401_status(): void
    {
        $response = Response::unauthorized('Custom unauthenticated message');

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals([
            'success' => false,
            'message' => 'Custom unauthenticated message',
        ], $response->getData(true));
        $this->assertArrayNotHasKey('data', $response->getData(true));
    }

    public function test_forbidden_macro_returns_403_status(): void
    {
        $response = Response::forbidden('Access denied');

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals([
            'success' => false,
            'message' => 'Access denied',
        ], $response->getData(true));
        $this->assertArrayNotHasKey('data', $response->getData(true));
    }

    public function test_not_found_macro_returns_404_status(): void
    {
        $response = Response::notFound('User not found');

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals([
            'success' => false,
            'message' => 'User not found',
        ], $response->getData(true));
        $this->assertArrayNotHasKey('data', $response->getData(true));
    }

    public function test_server_error_macro_returns_500_status(): void
    {
        $response = Response::serverError('Something went wrong');

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals([
            'success' => false,
            'message' => 'Something went wrong',
        ], $response->getData(true));
        $this->assertArrayNotHasKey('data', $response->getData(true));
    }
}
