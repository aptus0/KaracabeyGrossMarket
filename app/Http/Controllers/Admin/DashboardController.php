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
        return view('admin.dashboard', [
            'stats' => [
                'products' => Product::query()->count(),
                'orders' => Order::query()->count(),
                'awaiting_payment' => Order::query()->where('status', OrderStatus::AwaitingPayment)->count(),
                'paid_payments' => Payment::query()->where('status', PaymentStatus::Paid)->count(),
                'users' => User::query()->count(),
            ],
            'orders' => Order::query()->with('payment')->latest()->limit(8)->get(),
        ]);
    }
}
