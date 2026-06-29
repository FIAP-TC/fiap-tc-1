<?php

namespace App\Http\Controllers;

use App\Services\OrderApprovalTokenService;
use App\Services\ServiceOrderService;
use Illuminate\Http\Request;

class ServiceOrderApprovalController extends Controller
{
    public function __construct(
        private readonly OrderApprovalTokenService $tokenService,
        private readonly ServiceOrderService $serviceOrderService,
    ) {}

    public function approve(Request $request)
    {
        $payload = $this->tokenService->validate($request->get('token'));

        $this->serviceOrderService->approve($payload['service_order_id']);
        return view('approval.success');
    }

    public function reject(Request $request)
    {
        $payload = $this->tokenService->validate($request->get('token'));

        $this->serviceOrderService->reject($payload['service_order_id']);
        return view('approval.rejected');
    }
}