<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $days = collect(range(6, 0))->map(fn ($days) => now()->subDays($days)->format('M d'));
        $chartData = [
            'labels' => $days->values()->all(),
            'earnings' => [12500, 18200, 15300, 22400, 27500, 19800, 24300],
            'orders' => [45, 62, 53, 84, 102, 75, 91],
        ];

        return view('admin.dashboard', [
            'stats' => [
                'products' => Product::query()->count(),
                'orders' => Order::query()->count(),
                'awaiting_payment' => Order::query()->where('status', OrderStatus::AwaitingPayment)->count(),
                'paid_payments' => Payment::query()->where('status', PaymentStatus::Paid)->count(),
                'users' => User::query()->count(),
            ],
            'orders' => Order::query()->with('payment')->latest()->limit(8)->get(),
            'chartData' => $chartData,
        ]);
    }
}
