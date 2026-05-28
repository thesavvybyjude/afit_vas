<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;

class AuthIntegrationTest extends TestCase
{
    private string $baseUrl;

    protected function setUp(): void
    {
        // For local integration testing, we expect the PHP built-in server to be running on 8000
        $this->baseUrl = 'http://localhost:8000/api';
    }

    public function testHealthEndpointReturnsConnected(): void
    {
        $response = @file_get_contents($this->baseUrl . '/health');
        if ($response === false) {
            $this->markTestSkipped('Local API server is not running on localhost:8000.');
        }

        $data = json_decode($response, true);
        $this->assertEquals('ok', $data['status']);
        $this->assertEquals('connected', $data['db']);
    }

    public function testProgrammesEndpointReturnsData(): void
    {
        $response = @file_get_contents($this->baseUrl . '/programmes');
        if ($response === false) {
            $this->markTestSkipped('Local API server is not running on localhost:8000.');
        }

        $data = json_decode($response, true);
        $this->assertTrue($data['success']);
        $this->assertIsArray($data['data']);
        $this->assertGreaterThanOrEqual(6, count($data['data']));
    }

    public function testRegistrationFailsWithDuplicateEmail(): void
    {
        $payload = json_encode([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'superadmin@afit.edu.ng', // Existing super admin
            'password' => 'TestPass123!',
            'phone' => '08000000000'
        ]);

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => $payload,
                'ignore_errors' => true // Don't throw Exception on 4xx
            ]
        ];

        $context = stream_context_create($options);
        $response = @file_get_contents($this->baseUrl . '/auth/register', false, $context);
        
        if ($response === false) {
            $this->markTestSkipped('Local API server is not running.');
        }

        // We expect HTTP 422 Unprocessable Entity
        $this->assertStringContainsString('422', $http_response_header[0]);
        
        $data = json_decode($response, true);
        $this->assertFalse($data['success']);
        $this->assertArrayHasKey('email', $data['errors']);
    }
}
