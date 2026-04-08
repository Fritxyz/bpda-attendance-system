<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse border border-slate-100 shadow-sm rounded-lg overflow-hidden">
        <thead class="bg-slate-50">
            <tr>
                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase border-r border-slate-200">Violation Category</th>
                <th colspan="2" class="px-4 py-2 text-[10px] font-black text-amber-600 uppercase text-center border-b border-slate-200 bg-amber-50/30">Tardiness (Late)</th>
                <th colspan="2" class="px-4 py-2 text-[10px] font-black text-orange-600 uppercase text-center border-b border-slate-200 border-x bg-orange-50/30">Undertime (UT)</th>
                <th colspan="2" class="px-4 py-2 text-[10px] font-black text-red-600 uppercase text-center border-b border-slate-200 bg-red-50/30">Absences / LWOP</th>
                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-slate-700 uppercase text-right bg-slate-100 border-l border-slate-200">Total Deduction</th>
            </tr>
            <tr class="bg-slate-50/50">
                <th class="px-2 py-2 text-[9px] font-bold text-slate-400 uppercase text-center">Mins</th>
                <th class="px-2 py-2 text-[9px] font-bold text-slate-400 uppercase text-center">Amount</th>
                <th class="px-2 py-2 text-[9px] font-bold text-slate-400 uppercase text-center border-l border-slate-100">Mins</th>
                <th class="px-2 py-2 text-[9px] font-bold text-slate-400 uppercase text-center border-r border-slate-100">Amount</th>
                <th class="px-2 py-2 text-[9px] font-bold text-slate-400 uppercase text-center">Days</th>
                <th class="px-2 py-2 text-[9px] font-bold text-slate-400 uppercase text-center">Amount</th>
            </tr>
        </thead>
        
        <tbody class="divide-y divide-slate-100 text-[12px]">
            {{-- Example Row 1 --}}
            <tr class="hover:bg-slate-50/80 transition-all">
                <td class="px-4 py-4">
                    <span class="font-bold text-slate-700">Attendance Penalty</span>
                    <div class="text-[9px] text-slate-400 italic">Based on DTR Logs</div>
                </td>
                {{-- Late --}}
                <td class="px-2 py-4 text-center font-medium text-slate-600">45m</td>
                <td class="px-2 py-4 text-center text-red-500 font-bold">₱ 120.00</td>
                {{-- Undertime --}}
                <td class="px-2 py-4 text-center font-medium text-slate-600 border-l border-slate-50">30m</td>
                <td class="px-2 py-4 text-center text-red-500 font-bold border-r border-slate-50">₱ 80.50</td>
                {{-- Absence --}}
                <td class="px-2 py-4 text-center font-medium text-slate-600">2.0</td>
                <td class="px-2 py-4 text-center text-red-500 font-bold">₱ 1,445.00</td>
                {{-- Row Total --}}
                <td class="px-4 py-4 text-right bg-slate-50/50 font-black text-slate-800 text-sm">
                    ₱ 1,645.50
                </td>
            </tr>
        </tbody>

        {{-- Grand Total Footer --}}
        <tfoot>
            <tr class="border-t-2 border-slate-200">
                <td colspan="7" class="px-4 py-4 text-right font-black text-slate-500 uppercase tracking-widest text-[10px]">
                    Total Amount to be Deducted:
                </td>
                <td class="px-4 py-4 text-right bg-red-600 text-white font-black text-lg">
                    ₱ 1,645.50
                </td>
            </tr>
        </tfoot>
    </table>
</div>