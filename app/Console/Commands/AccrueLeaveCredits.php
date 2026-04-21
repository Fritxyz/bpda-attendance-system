<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Services\LeaveCreditService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AccrueLeaveCredits extends Command
{
    protected $signature   = 'leave:accrue {--period= : Y-m format, default is last month}';
    protected $description = 'Accrue monthly leave credits (1.25 days) for all permanent employees';

    public function handle(LeaveCreditService $service): void
    {
        // Default: last month (para i-run sa simula ng bagong buwan)
        $period = $this->option('period') ?? Carbon::now()->subMonth()->format('Y-m');

        $employees = Employee::where('employment_type', 'Permanent')
            ->where('is_active', true)
            ->get();

        $this->info("Accruing leave credits for period: {$period}");
        $bar = $this->output->createProgressBar($employees->count());

        foreach ($employees as $employee) {
            $hireDate = Carbon::parse($employee->hire_date);
            $periodDate = Carbon::parse($period);

            // Skip kung hindi pa siya employed nung period na yun
            if ($hireDate->gt($periodDate->endOfMonth())) {
                $this->line(" – {$employee->name} (not yet employed in {$period}, skipped)");
                $bar->advance();
                continue;
            }

            $result = $service->accrueMonthly($employee, $period);
            if ($result) {
                $this->line(" ✓ {$employee->name} (+1.25 days)");
            } else {
                $this->line(" – {$employee->name} (already accrued, skipped)");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done!');
    }
}