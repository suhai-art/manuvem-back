<?php
namespace Tests\Feature\Roles;
use App\Models\User;
use Tests\TestCase;

class ZzDebugTest extends TestCase
{
    protected bool $tenancy = true;

    public function test_debug_forbidden(): void
    {
        $viewer = $this->createUserWithoutRole();
        \Laravel\Sanctum\Sanctum::actingAs($viewer);
        $response = $this->getJson($this->baseUrl.'/api/roles');
        fwrite(STDERR, "\nDEBUG status=".$response->getStatusCode()." body=".$response->getContent()."\n");
    }
}
