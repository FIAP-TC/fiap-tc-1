<?php

namespace App\Interfaces\Http\Controllers\ServiceOrder;

use App\Application\ServiceOrder\UseCases\RejectServiceOrderUseCase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class RejectServiceOrderController
{
    public function __invoke(
        Request $request,
        RejectServiceOrderUseCase $useCase,
    ): View {
        $useCase->execute($request->query('token'));
        return view('approval.rejected');
    }
}
