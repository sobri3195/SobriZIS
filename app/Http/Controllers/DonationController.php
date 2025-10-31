<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Program;
use App\Services\Payment\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DonationController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService
    ) {}

    public function create(Program $program)
    {
        return Inertia::render('Donations/Create', [
            'program' => $program->load('tenant'),
            'paymentMethods' => $this->getPaymentMethods(),
        ]);
    }

    public function store(Request $request, Program $program)
    {
        $validated = $request->validate([
            'donor_name' => 'required_without:user_id|string|max:255',
            'donor_email' => 'required_without:user_id|email',
            'donor_phone' => 'required_without:user_id|string|max:20',
            'amount' => 'required|numeric|min:10000',
            'is_anonymous' => 'boolean',
            'message' => 'nullable|string|max:500',
            'payment_method' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $uniqueCode = rand(1, 999);
            $totalAmount = $validated['amount'] + $uniqueCode;

            $donation = Donation::create([
                'donor_id' => auth()->id() ? auth()->user()->donor?->id : null,
                'program_id' => $program->id,
                'tenant_id' => $program->tenant_id,
                'donor_name' => $validated['donor_name'] ?? auth()->user()?->name,
                'donor_email' => $validated['donor_email'] ?? auth()->user()?->email,
                'donor_phone' => $validated['donor_phone'] ?? auth()->user()?->phone,
                'amount' => $validated['amount'],
                'unique_code' => $uniqueCode,
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'payment_gateway' => 'midtrans',
                'is_anonymous' => $validated['is_anonymous'] ?? false,
                'message' => $validated['message'] ?? null,
                'status' => 'pending',
                'expired_at' => now()->addHours(24),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $paymentResult = $this->midtransService->createTransaction($donation);

            if (!$paymentResult['success']) {
                throw new \Exception($paymentResult['message']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'donation' => $donation->fresh(),
                'payment' => $paymentResult,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(Donation $donation)
    {
        $this->authorize('view', $donation);

        return Inertia::render('Donations/Show', [
            'donation' => $donation->load(['program', 'donor.user']),
        ]);
    }

    public function myDonations(Request $request)
    {
        $donations = Donation::with('program')
            ->where('donor_id', auth()->user()->donor->id)
            ->latest()
            ->paginate(15);

        return Inertia::render('Donations/MyDonations', [
            'donations' => $donations,
            'stats' => [
                'total_amount' => Donation::where('donor_id', auth()->user()->donor->id)
                    ->where('status', 'success')
                    ->sum('amount'),
                'total_count' => Donation::where('donor_id', auth()->user()->donor->id)
                    ->where('status', 'success')
                    ->count(),
            ],
        ]);
    }

    public function downloadReceipt(Donation $donation)
    {
        $this->authorize('view', $donation);

        if (!$donation->receipt_pdf) {
            abort(404, 'Receipt not available');
        }

        return response()->download(storage_path("app/{$donation->receipt_pdf}"));
    }

    protected function getPaymentMethods(): array
    {
        return [
            [
                'group' => 'E-Wallet',
                'methods' => [
                    ['code' => 'gopay', 'name' => 'GoPay', 'fee' => 0],
                    ['code' => 'shopeepay', 'name' => 'ShopeePay', 'fee' => 0],
                    ['code' => 'dana', 'name' => 'DANA', 'fee' => 0],
                    ['code' => 'ovo', 'name' => 'OVO', 'fee' => 0],
                ],
            ],
            [
                'group' => 'Bank Transfer',
                'methods' => [
                    ['code' => 'bca_va', 'name' => 'BCA Virtual Account', 'fee' => 4000],
                    ['code' => 'bni_va', 'name' => 'BNI Virtual Account', 'fee' => 4000],
                    ['code' => 'bri_va', 'name' => 'BRI Virtual Account', 'fee' => 4000],
                    ['code' => 'mandiri_va', 'name' => 'Mandiri Virtual Account', 'fee' => 4000],
                ],
            ],
            [
                'group' => 'QRIS',
                'methods' => [
                    ['code' => 'qris', 'name' => 'QRIS (All E-Wallet & Bank)', 'fee' => 0],
                ],
            ],
        ];
    }
}
