<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Control Header --}}
        <div class="p-5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Mod Paparan Jadual
                    </label>
                    <select
                        wire:model.live="viewMode"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:ring-primary-500"
                    >
                        <option value="cohort">Mengikut Kelas / Cohort</option>
                        <option value="teacher">Mengikut Guru</option>
                    </select>
                </div>

                @if($viewMode === 'cohort')
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            Pilih Kelas
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
                @else
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            Pilih Guru
                        </label>
                        <select
                            wire:model.live="selectedTeacherId"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:ring-primary-500"
                        >
                            @foreach($teacherOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>

        {{-- Weekly Timetable Schedule Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @foreach($days as $dayNum => $dayName)
                @php
                    $slots = $timetableGrid[$dayNum] ?? [];
                @endphp
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden shadow-sm flex flex-col">
                    <div class="p-3 bg-gray-50 dark:bg-gray-800/60 border-b border-gray-200 dark:border-gray-800 text-center">
                        <h4 class="font-bold text-sm text-gray-900 dark:text-white uppercase tracking-wider">
                            {{ $dayName }}
                        </h4>
                        <span class="text-[11px] text-gray-500 font-medium">
                            {{ count($slots) }} sesi
                        </span>
                    </div>

                    <div class="p-3 space-y-3 flex-1 min-h-[300px]">
                        @forelse($slots as $slot)
                            <div class="p-3 rounded-lg border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/40 shadow-xs hover:border-primary-400 transition">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[11px] font-bold text-primary-600 dark:text-primary-400">
                                        {{ substr($slot['start_time'], 0, 5) }} - {{ substr($slot['end_time'], 0, 5) }}
                                    </span>
                                </div>
                                <div class="font-semibold text-xs text-gray-900 dark:text-white">
                                    {{ $slot['subject_name'] }}
                                </div>
                                <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-800/60 flex flex-col gap-0.5 text-[10px] text-gray-500">
                                    <span>Kelas: {{ $slot['cohort']['name'] ?? '-' }}</span>
                                    <span>Guru: {{ $slot['teacher']['full_name'] ?? '-' }}</span>
                                    <span>Bilik: {{ $slot['room']['name'] ?? 'Bilik Darjah' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex items-center justify-center text-center p-4">
                                <p class="text-xs text-gray-400">Tiada kelas dijadualkan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
