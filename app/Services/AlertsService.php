<?php

namespace App\Services;

use App\Models\ContractsModel;
use App\Models\MaintenancesModel;
use App\Models\MaterialsStockModel;
use App\Models\PlanificationModel;
use App\Models\VacationsModel;

class AlertsService
{
    public function build(int $companyId): array
    {
        $today  = now();
        $alerts = [];

        $expiringContracts = ContractsModel::where('company_id', $companyId)
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->where('end_date', '<=', $today->copy()->addDays(30)->toDateString())
            ->where('end_date', '>=', $today->toDateString())
            ->with('employee')
            ->get();

        foreach ($expiringContracts as $c) {
            $daysLeft = round($today->diffInDays($c->end_date));
            $alerts[] = [
                'type'     => 'warning',
                'category' => 'contracts',
                'message'  => "Contract of {$c->employee->name} {$c->employee->surname} expires in {$daysLeft} days ({$c->end_date})",
            ];
        }

        $pendingVacations = VacationsModel::where('company_id', $companyId)
            ->where('status', 'pending')
            ->with('staff')
            ->get();

        if ($pendingVacations->isNotEmpty()) {
            $names = $pendingVacations->map(fn ($v) => "{$v->staff->name} {$v->staff->surname}")->join(', ');
            $alerts[] = [
                'type'     => 'info',
                'category' => 'vacations',
                'message'  => "{$pendingVacations->count()} vacation request awaiting confirmation: {$names}",
            ];
        }

        $upcomingMaintenances = MaintenancesModel::where('company_id', $companyId)
            ->where('date', '>', $today->toDateString())
            ->where('date', '<=', $today->copy()->addDays(7)->toDateString())
            ->with('machine')
            ->orderBy('date')
            ->get();

        foreach ($upcomingMaintenances as $m) {
            $alerts[] = [
                'type'     => 'info',
                'category' => 'maintenance',
                'message'  => "Planned Maintenance: {$m->machine->machine} on {$m->date} — {$m->description}",
            ];
        }

        $lowStock = MaterialsStockModel::where('materials_stock.company_id', $companyId)
            ->join('materials', 'materials_stock.material_id', '=', 'materials.mid')
            ->selectRaw('materials.material,
                SUM(CASE WHEN materials_stock.type = "in" THEN materials_stock.qty ELSE -materials_stock.qty END) as current_stock')
            ->groupBy('materials_stock.material_id', 'materials.material')
            ->havingRaw('current_stock <= 0')
            ->get();

        foreach ($lowStock as $s) {
            $alerts[] = [
                'type'     => 'danger',
                'category' => 'stock',
                'message'  => "Stock \"{$s->material}\" is {$s->current_stock}, reorder immediately!",
            ];
        }

        $delayedPlans = PlanificationModel::join('products', 'planification.product_id', '=', 'products.pid')
            ->where('planification.company_id', $companyId)
            ->whereIn('planification.status', ['pending', 'in_progress'])
            ->where('planification.end_date', '<', $today->toDateString())
            ->get(['products.product', 'planification.planned_qty', 'planification.end_date', 'planification.status']);

        foreach ($delayedPlans as $p) {
            $alerts[] = [
                'type'     => 'danger',
                'category' => 'production',
                'message'  => "Delayed production plan: {$p->product} ({$p->planned_qty} units) — should have been completed by {$p->end_date}",
            ];
        }

        return [
            'alerts' => $alerts,
            'count'  => count($alerts),
        ];
    }
}
