<?php

namespace App\Http\Resources;

use App\Models\ServiceOrder;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

/**
 * @mixin ServiceOrder
 */
class ServiceOrderResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var ServiceOrder $order */
        $order = $this->resource;

        return [
            'id'           => $order->id,
            'order_value'  => $order->order_value,
            'time_average' => $order->time_average,
            'status'       => (bool) $order->status,
            'created_at'   => $order->create_date,
            'updated_at'   => $order->modified_date,
            'user'         => new UserResource($this->whenLoaded('user')),
            'vehicule'     => new VehiculeResource($this->whenLoaded('vehicule')),
            'products'     => $this->loadProducts($order->id),
            'services'     => $this->loadServices($order->id),
            'status_history' => $this->loadStatusHistory($order->id),
        ];
    }

    /** Carrega produtos associados via pivot sem model dedicado para a tabela composta. */
    private function loadProducts(int $orderId): array
    {
        return DB::table('service_order_has_products as sop')
            ->join('products as p', 'p.id', '=', 'sop.products_id')
            ->where('sop.service_order_id', $orderId)
            ->select('p.id', 'p.name', 'p.type', 'sop.charged_value')
            ->get()
            ->toArray();
    }

    /** Carrega serviços associados via pivot. */
    private function loadServices(int $orderId): array
    {
        return DB::table('service_order_has_services as sos')
            ->join('services as s', 's.id', '=', 'sos.services_id')
            ->where('sos.service_order_id', $orderId)
            ->select('s.id', 's.name', 'sos.charged_value')
            ->get()
            ->toArray();
    }

    /** Carrega histórico de status da ordem. */
    private function loadStatusHistory(int $orderId): array
    {
        return DB::table('service_order_has_service_order_status as sh')
            ->join('service_order_status as ss', 'ss.id', '=', 'sh.service_order_status_id')
            ->where('sh.service_order_id', $orderId)
            ->select('ss.id', 'ss.name', 'sh.create_date')
            ->orderBy('sh.create_date')
            ->get()
            ->toArray();
    }
}
