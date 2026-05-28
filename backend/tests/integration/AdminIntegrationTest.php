<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;

class AdminIntegrationTest extends TestCase
{
    private string $baseUrl;
    private ?string $adminToken = null;

    protected function setUp(): void
    {
        $this->baseUrl = 'http://localhost:8000/api';
        
        // Attempt to login as super admin to get a token for testing Phase 5 endpoints
        $payload = json_encode([
            'email' => 'admin@afit.edu.ng', // Using the default seed email
            'password' => 'Admin@2024'
        ]);

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => $payload,
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($options);
        $response = @file_get_contents($this->baseUrl . '/auth/login', false, $context);

        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['data']['token'])) {
                $this->adminToken = $data['data']['token'];
            }
        }
    }

    public function testAdminCanFetchDashboardStats(): void
    {
        if (!$this->adminToken) {
            $this->markTestSkipped('Could not authenticate as admin. Is the local server running and seeded?');
        }

        $options = [
            'http' => [
                'method' => 'GET',
                'header' => "Authorization: Bearer {$this->adminToken}\r\n" .
                            "Accept: application/json\r\n",
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($options);
        $response = @file_get_contents($this->baseUrl . '/admin/stats', false, $context);
        
        $this->assertNotFalse($response, 'Failed to connect to API server.');
        $this->assertStringContainsString('200', $http_response_header[0]);
        
        $data = json_decode($response, true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('total', $data['data']);
        $this->assertArrayHasKey('submitted', $data['data']);
        $this->assertArrayHasKey('admitted', $data['data']);
    }

    public function testAdminCanListApplications(): void
    {
        if (!$this->adminToken) {
            $this->markTestSkipped('Could not authenticate as admin.');
        }

        $options = [
            'http' => [
                'method' => 'GET',
                'header' => "Authorization: Bearer {$this->adminToken}\r\n" .
                            "Accept: application/json\r\n",
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($options);
        $response = @file_get_contents($this->baseUrl . '/admin/applications', false, $context);
        
        $this->assertNotFalse($response);
        $this->assertStringContainsString('200', $http_response_header[0]);
        
        $data = json_decode($response, true);
        $this->assertTrue($data['success']);
        $this->assertIsArray($data['data']['data']); // Check pagination structure
    }

    public function testSuperAdminCanAccessAuditLogs(): void
    {
        if (!$this->adminToken) {
            $this->markTestSkipped('Could not authenticate as admin.');
        }

        $options = [
            'http' => [
                'method' => 'GET',
                'header' => "Authorization: Bearer {$this->adminToken}\r\n" .
                            "Accept: application/json\r\n",
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($options);
        $response = @file_get_contents($this->baseUrl . '/admin/audit-logs', false, $context);
        
        $this->assertNotFalse($response);
        
        // Since we logged in with admin@afit.edu.ng (super_admin), this should return 200
        $this->assertStringContainsString('200', $http_response_header[0]);
        
        $data = json_decode($response, true);
        $this->assertTrue($data['success']);
    }

    public function testRegularUsersCannotAccessAdminEndpoints(): void
    {
        $options = [
            'http' => [
                'method' => 'GET',
                'header' => "Accept: application/json\r\n", // No auth token
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($options);
        $response = @file_get_contents($this->baseUrl . '/admin/stats', false, $context);
        
        if ($response === false) {
            $this->markTestSkipped('Failed to connect to API server.');
        }

        $this->assertStringContainsString('401', $http_response_header[0]);
    }
}
