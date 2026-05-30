<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\SeedsApiFixtures;
use Tests\TestCase;

class ExpensesApiTest extends TestCase
{
    use RefreshDatabase;
    use SeedsApiFixtures;

    public function test_manager_cannot_use_expense_crud_or_report_endpoints(): void
    {
        $user = $this->seedUser(['role' => 'manager', 'username' => 'manager.exp']);
        Sanctum::actingAs($user);

        $this->getJson('/api/admin/expenses')->assertForbidden();
        $this->postJson('/api/admin/create_expense', [
            'comment' => 'Blocked',
            'price' => 10,
            'date' => '2026-05-01',
        ])->assertForbidden();
        $this->getJson('/api/admin/expenses_report?start_date=2026-05-01&end_date=2026-05-31')
            ->assertForbidden();
    }

    public function test_report_sums_only_expenses_inside_requested_period(): void
    {
        $admin = $this->seedUser(['role' => 'admin', 'username' => 'admin.report']);
        $this->seedExpense($admin->company_id, ['price' => 100, 'date' => '2026-05-10']);
        $this->seedExpense($admin->company_id, ['price' => 50.25, 'date' => '2026-05-12']);
        $this->seedExpense($admin->company_id, ['price' => 999, 'date' => '2026-01-01']);
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/expenses_report?start_date=2026-05-01&end_date=2026-05-31');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'total' => 150.25,
            ])
            ->assertJsonCount(2, 'data');
    }

    public function test_report_rejects_missing_date_parameters(): void
    {
        $admin = $this->seedUser(['role' => 'admin', 'username' => 'admin.report.bad']);
        Sanctum::actingAs($admin);

        $this->getJson('/api/admin/expenses_report')
            ->assertUnprocessable()
            ->assertJson([
                'success' => false,
                'message' => 'Please select valid dates.',
            ]);
    }

    public function test_admin_cannot_register_expense_with_non_positive_price(): void
    {
        $admin = $this->seedUser(['role' => 'admin', 'username' => 'admin.price']);
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/admin/create_expense', [
            'comment' => 'Invalid amount',
            'price' => 0,
            'date' => '2026-05-20',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['price']);
    }
}
