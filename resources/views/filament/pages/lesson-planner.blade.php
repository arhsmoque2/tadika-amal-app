<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- RPH Creator Form --}}
            <div class="lg:col-span-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <x-heroicon-o-pencil class="w-5 h-5 text-primary-500" />
                    Penyediaan RPH Harian
                </h3>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Pilih Kelas</label>
                        <select wire:model.live="selectedCohortId" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                            @foreach($cohortOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Minggu Ke-</label>
                        <input type="number" min="1" max="42" wire:model="selectedWeek" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Tarikh Pengajaran</label>
                        <input type="date" wire:model="plan_date_input" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Tema Mingguan</label>
                        <input type="text" wire:model="theme_input" placeholder="Contoh: Diri Saya" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Tunjang Pembelajaran KSPK</label>
                    <select wire:model="tunjang_input" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                        @foreach($kspkTunjangOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Objektif Pembelajaran</label>
                    <textarea wire:model="objectives_input" rows="2" placeholder="Pada akhir sesi, murid dapat mengenal huruf A-Z..." class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Aktiviti PdP</label>
                    <textarea wire:model="activities_input" rows="3" placeholder="1. Set Induksi: Nyanyian lagu ABC&#10;2. Aktiviti: Memadankan kad huruf..." class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Bahan Bantu Belajar (BBB)</label>
                    <input type="text" wire:model="materials_input" placeholder="Kad imbasan, plastisin, kertas lukisan" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                </div>

                <x-filament::button
                    type="button"
                    wire:click="saveLessonPlan"
                    color="primary"
                    icon="heroicon-o-check"
                    class="w-full"
                >
                    Simpan RPH
                </x-filament::button>
            </div>

            {{-- Lesson Plans Archive / List --}}
            <div class="lg:col-span-2 space-y-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center justify-between">
                    <span>Arkib RPH Prasekolah</span>
                    <span class="text-xs font-normal text-gray-500">{{ count($lessonPlans) }} Rekod RPH</span>
                </h3>

                @forelse($lessonPlans as $plan)
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5 shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-primary-100 text-primary-800 dark:bg-primary-900/40 dark:text-primary-200">
                                    Minggu {{ $plan['week_number'] }}
                                </span>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                    Tema: {{ $plan['theme'] }}
                                </span>
                            </div>
                            <span class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($plan['plan_date'])->format('d/m/Y') }}
                            </span>
                        </div>

                        <div class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                            {{ $plan['tunjang_kspk'] }}
                        </div>

                        <div class="text-xs text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/40 p-3 rounded-lg space-y-1.5">
                            <div><strong>🎯 Objektif:</strong> {{ $plan['learning_objectives'] }}</div>
                            <div><strong>📝 Aktiviti:</strong> {{ $plan['activities'] }}</div>
                            @if($plan['materials_needed'])
                                <div><strong>🎨 Bahan:</strong> {{ $plan['materials_needed'] }}</div>
                            @endif
                        </div>

                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 text-[10px] text-gray-400 flex items-center justify-between">
                            <span>Disediakan oleh: {{ $plan['teacher']['full_name'] ?? 'Guru Kelas' }}</span>
                            <span class="text-emerald-600 font-semibold">✓ Disahkan</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-xs text-gray-500">
                        Belum ada rekod RPH disimpan untuk kelas ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
