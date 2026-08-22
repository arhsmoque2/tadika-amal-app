<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Control Header --}}
        <div class="p-5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Pilih Kelas / Cohort
                    </label>
                    <select
                        wire:model.live="selectedCohortId"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:ring-primary-500"
                    >
                        @foreach($cohortOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Tempoh Penilaian
                    </label>
                    <select
                        wire:model.live="selectedPeriod"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:ring-primary-500"
                    >
                        @foreach($periodOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end">
                    <x-filament::button
                        type="button"
                        wire:click="saveEvaluations"
                        color="primary"
                        icon="heroicon-o-check"
                        class="w-full"
                    >
                        Simpan Semua Penilaian
                    </x-filament::button>
                </div>
            </div>
        </div>

        {{-- Evaluation Matrix Table --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Matriks Penilaian Murid ({{ count($students) }} Murid × {{ count($skills) }} Indikator)
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Skala:
                        <span class="inline-flex items-center gap-1 font-semibold text-emerald-600 mr-2"><span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> TM (Menguasai)</span>
                        <span class="inline-flex items-center gap-1 font-semibold text-amber-600 mr-2"><span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span> SM (Sedang Menguasai)</span>
                        <span class="inline-flex items-center gap-1 font-semibold text-rose-600"><span class="w-2 h-2 rounded-full bg-rose-500 inline-block"></span> BM (Belum Menguasai)</span>
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto max-h-[600px]">
                <table class="w-full text-left text-xs divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 uppercase sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 w-10 text-center">Bil</th>
                            <th class="px-4 py-3 min-w-[180px] sticky left-0 bg-gray-100 dark:bg-gray-800 z-20">Nama Murid</th>
                            @foreach($skills as $skill)
                                <th class="px-3 py-2 text-center min-w-[110px]" title="{{ $skill['name'] }}">
                                    <div class="font-bold text-[11px] text-gray-900 dark:text-white">{{ $skill['code'] ?? 'KSPK' }}</div>
                                    <div class="text-[9px] font-normal text-gray-500 truncate max-w-[100px]">{{ $skill['name'] }}</div>
                                </th>
                            @endforeach
                            <th class="px-4 py-3 text-center min-w-[100px]">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($students as $index => $student)
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40">
                                <td class="px-4 py-3 text-center text-gray-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-gray-900 z-10 border-r border-gray-100 dark:border-gray-800">
                                    {{ $student['name'] }}
                                </td>
                                @foreach($skills as $skill)
                                    @php
                                        $key = $student['id'] . '_' . $skill['id'];
                                        $currentScale = $evaluations[$key] ?? null;
                                    @endphp
                                    <td class="px-2 py-2 text-center">
                                        <select
                                            wire:change="setEvaluation({{ $student['id'] }}, {{ $skill['id'] }}, $event.target.value)"
                                            class="text-[11px] rounded p-1 font-semibold border-gray-300 dark:border-gray-700 dark:bg-gray-800 {{ $currentScale == 3 ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : ($currentScale == 2 ? 'bg-amber-50 text-amber-700 border-amber-300' : ($currentScale == 1 ? 'bg-rose-50 text-rose-700 border-rose-300' : 'text-gray-400')) }}"
                                        >
                                            <option value="">-</option>
                                            @foreach($scales as $scale)
                                                <option value="{{ $scale['id'] }}" {{ $currentScale == $scale['id'] ? 'selected' : '' }}>
                                                    {{ $scale['shortname'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endforeach
                                <td class="px-4 py-2 text-center">
                                    <button
                                        type="button"
                                        wire:click="generateStudentPdf({{ $student['id'] }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-medium rounded-md bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 transition"
                                        title="Jana Fail PDF"
                                    >
                                        <x-heroicon-o-document-arrow-down class="w-3.5 h-3.5" />
                                        PDF
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($skills) + 3 }}" class="px-4 py-8 text-center text-gray-500">
                                    Tiada murid berdaftar dalam kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
