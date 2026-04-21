<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveCreditLedger;
use Carbon\Carbon;

class LeaveCreditService
{
    /**
     * Kunin ang running balance ng employee sa katapusan ng isang period.
     * Halimbawa: getBalanceAsOf($emp, '2026-03') = balance after March 2026
     */
    public function getBalanceAsOf(Employee $employee, string $period): float
    {
        return (float) LeaveCreditLedger::forEmployee($employee->id)
            ->upToPeriod($period)
            ->sum('amount');
    }

    /**
     * Kunin ang balance bago magsimula ang isang period (starting balance).
     * Para ma-display: "Balance at start of March: 9.072"
     */
    public function getStartingBalance(Employee $employee, string $period): float
    {
        return (float) LeaveCreditLedger::forEmployee($employee->id)
            ->beforePeriod($period)
            ->sum('amount');
    }

    /**
     * I-accrual ang 1.25 days para sa isang buwan.
     * Dapat tawagin sa katapusan ng bawat buwan (via scheduler).
     * May built-in protection para hindi mag-duplicate.
     */
    public function accrueMonthly(Employee $employee, string $period): ?LeaveCreditLedger
    {
        // Check muna kung naka-accrual na para sa period na ito
        $alreadyAccrued = LeaveCreditLedger::forEmployee($employee->id)
            ->forPeriod($period)
            ->where('type', 'ACCRUAL')
            ->exists();

        if ($alreadyAccrued) {
            return null; // Skip, naka-accrual na
        }

        return LeaveCreditLedger::create([
            'employee_id'      => $employee->id,
            'transaction_date' => Carbon::parse($period)->endOfMonth()->toDateString(),
            'period'           => $period,
            'type'             => 'ACCRUAL',
            'amount'           => 1.25,
            'description'      => 'Monthly leave credit accrual',
        ]);
    }

    /**
     * I-process ang lahat ng deductions para sa isang buwan
     * base sa attendance data.
     * 
     * Tinatawag ito pag-view ng attendance para laging updated.
     * May protection din para hindi mag-duplicate.
     */
    public function processMonthlyDeductions(
        Employee $employee,
        string $period,
        array $attendanceData,   // Yung $attendance array mo sa controller
        $attendanceRecords       // Yung collection mo na keyed by date
    ): void {
        // I-delete muna ang existing deductions para sa period na ito
        // para fresh ang computation (re-computable)
        LeaveCreditLedger::forEmployee($employee->id)
            ->forPeriod($period)
            ->whereIn('type', ['LATE', 'UNDERTIME', 'ABSENT'])
            ->delete();

        $parsedMonth = Carbon::parse($period);

        foreach ($attendanceData as $dayNumber => $data) {
            $record    = $data['record'];
            $isWeekend = in_array($data['day_name'], ['Sat', 'Sun']);
            $isHoliday = $data['is_holiday'] ?? false;
            $isLeave   = $data['is_leave'] ?? false;
            $isFuture  = $data['is_future'] ?? false;
            $isBeforeHire = $data['is_before_hire'] ?? false;
            $isTravel  = $data['is_travel'] ?? false;

            $dateStr = $data['date_str'];
            $transactionDate = Carbon::parse($dateStr)->toDateString();

            if ($isFuture || $isBeforeHire || $isWeekend || $isHoliday || $isLeave || $isTravel) {
                continue; // Wag na i-process ito
            }

            if (!$record) {
                // ABSENT — full 1 day deduction
                LeaveCreditLedger::create([
                    'employee_id'      => $employee->id,
                    'transaction_date' => $transactionDate,
                    'period'           => $period,
                    'type'             => 'ABSENT',
                    'amount'           => -1.0000,
                    'description'      => "Absent - {$dateStr}",
                    'reference_id'     => null,
                ]);
                continue;
            }

            // LATE deduction
            if ($record->computed_late) {
                preg_match('/(\d+)/', $record->computed_late, $lateMatches);
                $lateMinutes = (int) ($lateMatches[0] ?? 0);
                if ($lateMinutes > 0) {
                    $lateDays = round($lateMinutes * 0.00208333, 4); // 1 min = 1/480 day
                    LeaveCreditLedger::create([
                        'employee_id'      => $employee->id,
                        'transaction_date' => $transactionDate,
                        'period'           => $period,
                        'type'             => 'LATE',
                        'amount'           => -$lateDays,
                        'description'      => "Late {$lateMinutes}min - {$dateStr}",
                        'reference_id'     => $record->id,
                    ]);
                }
            }

            // UNDERTIME deduction
            if ($record->computed_undertime) {
                preg_match('/(\d+)/', $record->computed_undertime, $utMatches);
                $utMinutes = (int) ($utMatches[0] ?? 0);
                if ($utMinutes > 0) {
                    $utDays = round($utMinutes * 0.00208333, 4);
                    LeaveCreditLedger::create([
                        'employee_id'      => $employee->id,
                        'transaction_date' => $transactionDate,
                        'period'           => $period,
                        'type'             => 'UNDERTIME',
                        'amount'           => -$utDays,
                        'description'      => "Undertime {$utMinutes}min - {$dateStr}",
                        'reference_id'     => $record->id,
                    ]);
                }
            }
        }
    }

    /**
     * I-set ang initial balance ng employee (yung manual na pinasok ng HR).
     * Tinatawag lang isang beses pag ma-migrate ang system.
     */
    public function setInitialBalance(Employee $employee, float $balance, string $asOfPeriod): LeaveCreditLedger
    {
        // I-delete ang existing initial entry kung meron
        LeaveCreditLedger::forEmployee($employee->id)
            ->where('type', 'INITIAL')
            ->delete();

        return LeaveCreditLedger::create([
            'employee_id'      => $employee->id,
            'transaction_date' => Carbon::parse($asOfPeriod)->startOfMonth()->toDateString(),
            'period'           => $asOfPeriod,
            'type'             => 'INITIAL',
            'amount'           => $balance,
            'description'      => 'Initial leave credit balance (migrated)',
        ]);
    }

    /**
     * Kunin ang breakdown ng transactions para sa isang buwan
     * para sa display sa UI.
     */
    public function getMonthSummary(Employee $employee, string $period): array
    {
        $transactions = LeaveCreditLedger::forEmployee($employee->id)
            ->forPeriod($period)
            ->orderBy('transaction_date')
            ->get();

        $startingBalance = $this->getStartingBalance($employee, $period);
        $monthlyChanges  = $transactions->sum('amount');
        $endingBalance   = $startingBalance + $monthlyChanges;

        return [
            'starting_balance' => round($startingBalance, 4),
            'accrual'          => round($transactions->where('type', 'ACCRUAL')->sum('amount'), 4),
            'late_deduction'   => round(abs($transactions->where('type', 'LATE')->sum('amount')), 4),
            'ut_deduction'     => round(abs($transactions->where('type', 'UNDERTIME')->sum('amount')), 4),
            'absent_deduction' => round(abs($transactions->where('type', 'ABSENT')->sum('amount')), 4),
            'net_change'       => round($monthlyChanges, 4),
            'ending_balance'   => round($endingBalance, 4),
        ];
    }
}