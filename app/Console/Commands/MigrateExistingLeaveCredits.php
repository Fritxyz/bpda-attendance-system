<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Services\LeaveCreditService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MigrateExistingLeaveCredits extends Command
{
    protected $signature   = 'leave:migrate-existing {--period= : Y-m kung kailan base ang balance}';
    protected $description = 'One-time migration: i-import ang existing leave_credits column sa ledger';

    public function handle(LeaveCreditService $service): void
    {
        $period = $this->option('period') ?? Carbon::now()->format('Y-m');

        $employees = Employee::where('employment_type', 'Permanent')
            ->whereNotNull('leave_credits')
            ->get();

        $this->info("Migrating existing balances as of: {$period}");

        foreach ($employees as $employee) {
            $service->setInitialBalance($employee, (float) $employee->leave_credits, $period);
            $this->line(" ✓ {$employee->name}: {$employee->leave_credits} days");
        }

        $this->info('Migration complete! You can now use the ledger system.');
    }
}