<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter & Control Header --}}
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
                        Tarikh Saringan
                    </label>
                    <input
                        type="date"
                        wire:model.live="selectedDate"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:ring-primary-500"
                    />
                </div>

                <div class="flex justify-end">
                    <x-filament::button
                        type="button"
                        wire:click="saveAllScreenings"
                        color="primary"
                        icon="heroicon-o-check"
                        class="w-full"
                    >
                        Simpan Rekod Saringan
                    </x-filament::button>
                </div>
            </div>
        </div>

        {{-- Screening Table --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Pemeriksaan Pintu Masuk (Gate Screening)
                    </h3>
                    <p class="text-xs text-gray-500">
                        Pantau suhu badan & gejala HFMD sebelum murid memasuki bilik darjah.
                    </p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="inline-flex items-center gap-1 font-semibold text-emerald-600"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Suhu Normal (< 37.5°C)</span>
                    <span class="inline-flex items-center gap-1 font-semibold text-rose-600"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Demam / Gejala (>= 37.5°C)</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 uppercase">
                        <tr>
                            <th class="px-4 py-3 w-10 text-center">Bil</th>
                            <th class="px-4 py-3">Nama Murid</th>
                            <th class="px-4 py-3 text-center">Suhu (°C)</th>
                            <th class="px-4 py-3">Pemeriksaan Gejala Fizikal</th>
                            <th class="px-4 py-3 text-center">Keputusan</th>
                            <th class="px-4 py-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($students as $index => $student)
                            @php
                                $sData = $screeningData[$student['id']] ?? ['temp' => 36.5, 'symptoms' => [], 'status' => 'lulus', 'remarks' => ''];
                                $isFever = ($sData['temp'] ?? 36.5) >= 37.5;
                                $symptoms = $sData['symptoms'] ?? [];
                            @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                                <td class="px-4 py-3 text-center text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                    {{ $student['name'] }}
                                    <div class="text-[10px] text-gray-400 font-mono">{{ $student['mykid'] }}</div>
                                </td>
                                <td class="px-4 py-3 text-center w-28">
                                    <input
                                        type="number"
                                        step="0.1"
                                        wire:model.live="screeningData.{{ $student['id'] }}.temp"
                                        class="w-20 text-center text-xs font-bold rounded-lg border {{ $isFever ? 'border-rose-400 bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300' : 'border-emerald-300 bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300' }}"
                                    />
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1.5">
                                        <button
                                            type="button"
                                            wire:click="toggleSymptom({{ $student['id'] }}, 'hfmd_rash')"
                                            class="px-2 py-0.5 rounded text-[10px] font-semibold border transition {{ in_array('hfmd_rash', $symptoms) ? 'bg-rose-600 text-white border-rose-600' : 'border-gray-300 text-gray-600 hover:bg-gray-100' }}"
                                        >
                                            Bintik/Ruam HFMD
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="toggleSymptom({{ $student['id'] }}, 'flu')"
                                            class="px-2 py-0.5 rounded text-[10px] font-semibold border transition {{ in_array('flu', $symptoms) ? 'bg-amber-500 text-white border-amber-500' : 'border-gray-300 text-gray-600 hover:bg-gray-100' }}"
                                        >
                                            Selsema / Batuk
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="toggleSymptom({{ $student['id'] }}, 'red_eyes')"
                                            class="px-2 py-0.5 rounded text-[10px] font-semibold border transition {{ in_array('red_eyes', $symptoms) ? 'bg-amber-500 text-white border-amber-500' : 'border-gray-300 text-gray-600 hover:bg-gray-100' }}"
                                        >
                                            Mata Merah
                                        </button>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <select
                                        wire:model="screeningData.{{ $student['id'] }}.status"
                                        class="text-xs rounded-lg font-semibold border-gray-300 dark:border-gray-700 dark:bg-gray-800 {{ ($sData['status'] ?? 'lulus') === 'lulus' ? 'text-emerald-600' : 'text-rose-600' }}"
                                    >
                                        <option value="lulus">Lulus Masuk</option>
                                        <option value="kuarantin">Kuarantin di Bilik Rehat</option>
                                        <option value="ibu_bapa_dihubungi">Ibu Bapa Dihubungi</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input
                                        type="text"
                                        wire:model="screeningData.{{ $student['id'] }}.remarks"
                                        placeholder="Catatan tambahan..."
                                        class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"
                                    />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
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
