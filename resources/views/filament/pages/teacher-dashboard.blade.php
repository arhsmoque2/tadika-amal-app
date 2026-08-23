<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Attendance Warning Banner if pending --}}
        @if(!empty($pendingAttendanceCohorts))
            <div class="p-4 border-l-4 border-amber-500 bg-amber-50 dark:bg-amber-950/40 rounded-r-lg shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                        <div>
                            <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-200">
                                Peringatan Kehadiran Hari Ini
                            </h3>
                            <p class="text-xs text-amber-700 dark:text-amber-300">
                                Anda belum merekod kehadiran untuk kelas:
                                <span class="font-bold">
                                    {{ implode(', ', array_column($pendingAttendanceCohorts, 'name')) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <x-filament::button
                        tag="a"
                        href="{{ route('filament.app.pages.cohort-attendance') }}"
                        color="amber"
                        size="sm"
                    >
                        Ambil Kehadiran Sekarang
                    </x-filament::button>
                </div>
            </div>
        @else
            <div class="p-4 border-l-4 border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 rounded-r-lg shadow-sm flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <x-heroicon-o-check-circle class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    <div>
                        <h3 class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">
                            Kehadiran Hari Ini Selesai
                        </h3>
                        <p class="text-xs text-emerald-700 dark:text-emerald-300">
                            Semua rekod kehadiran kelas anda telah dikemaskini.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Metrics & Cohort Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Kelas Dibimbing</p>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ count($cohorts) }}</h2>
                    </div>
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-xl">
                        <x-heroicon-o-user-group class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <div class="p-5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Murid</p>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalStudents }}</h2>
                    </div>
                    <div class="p-3 bg-purple-50 dark:bg-purple-900/30 rounded-xl">
                        <x-heroicon-o-academic-cap class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
            </div>

            <div class="p-5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sesi Hari Ini</p>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ count($todaySlots) }} Slot</h2>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl">
                        <x-heroicon-o-clock class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Today's Schedule & Quick Action Blocks --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Today's Classes --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-heroicon-o-calendar class="w-5 h-5 text-primary-500" />
                        Jadual Mengajar Hari Ini
                    </h3>
                    <x-filament::button tag="a" href="{{ route('filament.app.pages.class-timetable') }}" size="xs" color="gray">
                        Lihat Penuh
                    </x-filament::button>
                </div>

                @if(!empty($todaySlots))
                    <div class="space-y-3">
                        @foreach($todaySlots as $slot)
                            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800">
                                <div>
                                    <div class="font-medium text-sm text-gray-900 dark:text-white">
                                        {{ $slot['subject_name'] }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        Kelas: {{ $slot['cohort']['name'] ?? '-' }} | Bilik: {{ $slot['room']['name'] ?? 'Bilik Darjah' }}
                                    </div>
                                </div>
                                <div class="text-xs font-semibold px-2.5 py-1 rounded bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200">
                                    {{ substr($slot['start_time'], 0, 5) }} - {{ substr($slot['end_time'], 0, 5) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-500 py-6 text-center">Tiada slot mengajar dijadualkan untuk hari ini.</p>
                @endif
            </div>

            {{-- Quick Links & Operations --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-800 pb-3 flex items-center gap-2">
                    <x-heroicon-o-bolt class="w-5 h-5 text-amber-500" />
                    Tindakan Pantas Operasi
                </h3>

                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('filament.app.pages.cohort-attendance') }}" class="p-4 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-primary-500 hover:bg-primary-50/20 transition flex flex-col items-center text-center">
                        <x-heroicon-o-clipboard-document-check class="w-8 h-8 text-primary-600 mb-2" />
                        <span class="text-xs font-semibold text-gray-900 dark:text-white">Rekod Kehadiran</span>
                        <span class="text-[10px] text-gray-500 mt-0.5">Tanda roll-call kelas</span>
                    </a>

                    <a href="{{ route('filament.app.pages.skill-evaluation-page') }}" class="p-4 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-purple-500 hover:bg-purple-50/20 transition flex flex-col items-center text-center">
                        <x-heroicon-o-chart-bar-square class="w-8 h-8 text-purple-600 mb-2" />
                        <span class="text-xs font-semibold text-gray-900 dark:text-white">Penilaian KSPK</span>
                        <span class="text-[10px] text-gray-500 mt-0.5">Matriks perkembangan murid</span>
                    </a>

                    <a href="{{ route('filament.app.pages.class-timetable') }}" class="p-4 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-emerald-500 hover:bg-emerald-50/20 transition flex flex-col items-center text-center">
                        <x-heroicon-o-calendar-days class="w-8 h-8 text-emerald-600 mb-2" />
                        <span class="text-xs font-semibold text-gray-900 dark:text-white">Jadual Waktu</span>
                        <span class="text-[10px] text-gray-500 mt-0.5">Mingguan mengikut kelas</span>
                    </a>

                    <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-blue-500 hover:bg-blue-50/20 transition flex flex-col items-center text-center opacity-80">
                        <x-heroicon-o-document-text class="w-8 h-8 text-blue-600 mb-2" />
                        <span class="text-xs font-semibold text-gray-900 dark:text-white">Borang & Surat</span>
                        <span class="text-[10px] text-gray-500 mt-0.5">Jana fail DOCX / PDF</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
