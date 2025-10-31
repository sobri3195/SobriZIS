<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_create_donation(): void
    {
        $program = Program::factory()->create([
            'status' => 'active',
            'target_amount' => 10000000,
        ]);

        $response = $this->post(route('donate.store', $program), [
            'donor_name' => 'John Doe',
            'donor_email' => 'john@example.com',
            'donor_phone' => '081234567890',
            'amount' => 100000,
            'payment_method' => 'qris',
            'is_anonymous' => false,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('donations', [
            'program_id' => $program->id,
            'donor_email' => 'john@example.com',
            'amount' => 100000,
        ]);
    }

    public function test_authenticated_donor_can_view_donation_history(): void
    {
        $user = User::factory()->create(['role' => 'donor']);
        $donor = Donor::factory()->create(['user_id' => $user->id]);
        
        Donation::factory()->count(5)->create([
            'donor_id' => $donor->id,
            'status' => 'success',
        ]);

        $response = $this->actingAs($user)->get(route('donations.my'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) =>
            $page->component('Donations/MyDonations')
                ->has('donations.data', 5)
        );
    }

    public function test_donation_updates_program_collected_amount(): void
    {
        $program = Program::factory()->create([
            'collected_amount' => 0,
            'total_donors' => 0,
        ]);

        $donation = Donation::factory()->create([
            'program_id' => $program->id,
            'amount' => 100000,
            'status' => 'pending',
        ]);

        $donation->update(['status' => 'success']);

        $program->refresh();
        
        $this->assertEquals(100000, $program->collected_amount);
        $this->assertEquals(1, $program->total_donors);
    }

    public function test_donation_requires_minimum_amount(): void
    {
        $program = Program::factory()->create(['status' => 'active']);

        $response = $this->post(route('donate.store', $program), [
            'donor_name' => 'John Doe',
            'donor_email' => 'john@example.com',
            'donor_phone' => '081234567890',
            'amount' => 5000, // Below minimum
            'payment_method' => 'qris',
        ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_receipt_is_generated_after_successful_payment(): void
    {
        $user = User::factory()->create(['role' => 'donor']);
        $donor = Donor::factory()->create(['user_id' => $user->id]);
        
        $donation = Donation::factory()->create([
            'donor_id' => $donor->id,
            'status' => 'pending',
            'receipt_number' => null,
        ]);

        $donation->update(['status' => 'success']);
        $donation->generateReceipt();

        $this->assertNotNull($donation->receipt_number);
        $this->assertStringStartsWith('RCP-', $donation->receipt_number);
    }
}
