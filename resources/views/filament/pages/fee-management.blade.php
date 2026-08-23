<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Control Filter Header --}}
        <div class="p-5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Pilih Kelas / Cohort</label>
                    <select wire:model.live="selectedCohortId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm">
                        @foreach($cohortOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Bulan Yuran</label>
                    <select wire:model.live="selectedMonth" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm">
                        @foreach($monthOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Invoices & Receipt Table --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Senarai Invois Yuran - Bulan {{ $selectedMonth }} ({{ count($invoices) }} Murid)
                    </h3>
                    <p class="text-xs text-gray-500">
                        Sahkan penerimaan bayaran untuk menjana Resit Rasmi Pelepasan Cukai LHDN.
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 uppercase">
                        <tr>
                            <th class="px-4 py-3 w-10 text-center">Bil</th>
                            <th class="px-4 py-3">Nama Murid</th>
                            <th class="px-4 py-3">No. Invois</th>
                            <th class="px-4 py-3 text-right">Jumlah (RM)</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">No. Resit</th>
                            <th class="px-4 py-3 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($invoices as $index => $inv)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                                <td class="px-4 py-3 text-center text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                    {{ $inv['student']['name'] ?? '-' }}
                                    <div class="text-[10px] text-gray-400 font-mono">{{ $inv['student']['mykid'] ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 font-mono text-gray-600 dark:text-gray-300">
                                    {{ $inv['invoice_number'] }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">
                                    RM {{ number_format($inv['amount'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($inv['status'] === 'paid')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">
                                            ✓ LUNAS
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">
                                            MENUNGGU BAYARAN
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center font-mono text-[11px] text-gray-500">
                                    {{ $inv['receipt_number'] ?: '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($inv['status'] === 'paid')
                                        <button
                                            type="button"
                                            wire:click="downloadReceiptPdf({{ $inv['id'] }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 transition"
                                        >
                                            <x-heroicon-o-document-arrow-down class="w-3.5 h-3.5" />
                                            Cetak Resit LHDN
                                        </button>
                                    @else
                                        <div class="inline-flex gap-1">
                                            <button
                                                type="button"
                                                wire:click="markAsPaid({{ $inv['id'] }}, 'fpx_online')"
                                                class="px-2 py-1 text-[11px] font-bold rounded bg-primary-600 hover:bg-primary-700 text-white transition"
                                            >
                                                Sahkan Bayaran (FPX/Online)
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="markAsPaid({{ $inv['id'] }}, 'cash')"
                                                class="px-2 py-1 text-[11px] font-bold rounded bg-gray-200 hover:bg-gray-300 text-gray-800 transition"
                                            >
                                                Tunai
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    Tiada rekod invois untuk bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
