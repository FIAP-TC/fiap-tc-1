<?php

namespace App\Interfaces\Http\Controllers\ServiceOrder;

use App\Application\ServiceOrder\UseCases\ApproveServiceOrderUseCase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class ApproveServiceOrderController
{
    public function __invoke(
        Request $request,
        ApproveServiceOrderUseCase $useCase,
    ): View {
        $useCase->execute($request->query('token'));
        return view('approval.success');
    }
}
