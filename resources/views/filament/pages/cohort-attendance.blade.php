<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter & Action Bar --}}
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
                        Tarikh Kehadiran
                    </label>
                    <input
                        type="date"
                        wire:model.live="selectedDate"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:ring-primary-500"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <x-filament::button
                        type="button"
                        wire:click="markAllPresent"
                        color="gray"
                        icon="heroicon-o-check-badge"
                        class="w-full"
                    >
                        Tanda Semua Hadir
                    </x-filament::button>

                    <x-filament::button
                        type="button"
                        wire:click="exportCsv"
                        color="gray"
                        icon="heroicon-o-arrow-down-tray"
                    >
                        Eksport CSV
                    </x-filament::button>
                </div>
            </div>
        </div>

        {{-- Attendance Matrix Table --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                    Senarai Murid ({{ count($students) }} orang)
                </h3>
                <span class="text-xs text-gray-500">
                    Klik butang status untuk menukar rekod murid
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-800/50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3 w-12 text-center">Bil</th>
                            <th class="px-4 py-3">Nama Murid</th>
                            <th class="px-4 py-3">MyKid</th>
                            <th class="px-4 py-3 text-center">Status Kehadiran</th>
                            <th class="px-4 py-3">Catatan / Sebab Tidak Hadir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($students as $index => $student)
                            @php
                                $status = $attendanceData[$student['id']]['status'] ?? 'hadir';
                            @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                                <td class="px-4 py-3 text-center text-xs text-gray-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ $student['name'] }}
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 font-mono">
                                    {{ $student['mykid'] ?: '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="inline-flex rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-0.5 bg-gray-100 dark:bg-gray-800">
                                        <button
                                            type="button"
                                            wire:click="setStatus({{ $student['id'] }}, 'hadir')"
                                            class="px-3 py-1 text-xs font-semibold rounded-md transition {{ $status === 'hadir' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}"
                                        >
                                            Hadir
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="setStatus({{ $student['id'] }}, 'tidak_hadir')"
                                            class="px-3 py-1 text-xs font-semibold rounded-md transition {{ $status === 'tidak_hadir' ? 'bg-rose-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}"
                                        >
                                            Tidak Hadir
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="setStatus({{ $student['id'] }}, 'sakit')"
                                            class="px-3 py-1 text-xs font-semibold rounded-md transition {{ $status === 'sakit' ? 'bg-amber-500 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}"
                                        >
                                            Sakit
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="setStatus({{ $student['id'] }}, 'cuti')"
                                            class="px-3 py-1 text-xs font-semibold rounded-md transition {{ $status === 'cuti' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}"
                                        >
                                            Cuti
                                        </button>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <input
                                        type="text"
                                        wire:model="attendanceData.{{ $student['id'] }}.reason"
                                        placeholder="{{ $status === 'hadir' ? '-' : 'Contoh: Demam / Hal keluarga' }}"
                                        class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-primary-500"
                                    />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-xs text-gray-500">
                                    Tiada murid berdaftar dalam kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-800 flex justify-end">
                <x-filament::button
                    type="button"
                    wire:click="saveAttendance"
                    color="primary"
                    icon="heroicon-o-check"
                >
                    Simpan Rekod Kehadiran
                </x-filament::button>
            </div>
        </div>
    </div>
</x-filament-panels::page>
