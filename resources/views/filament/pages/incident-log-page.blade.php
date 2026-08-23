<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Incident Entry Form --}}
            <div class="lg:col-span-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <x-heroicon-o-plus-circle class="w-5 h-5 text-rose-500" />
                    Daftar Insiden / Kemalangan Kecil
                </h3>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Kelas / Cohort</label>
                        <select wire:model.live="selectedCohortId" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                            @foreach($cohortOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Murid</label>
                        <select wire:model="selectedStudentId" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                            @foreach($studentOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Tarikh</label>
                        <input type="date" wire:model="incident_date" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Masa Kejadian</label>
                        <input type="time" wire:model="incident_time" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Lokasi Kejadian</label>
                        <input type="text" wire:model="location" placeholder="Taman Permainan / Bilik Darjah" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Tahap Kecederaan</label>
                        <select wire:model="severity" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                            <option value="ringan">Ringan (Calar/Lebam kecil)</option>
                            <option value="sederhana">Sederhana</option>
                            <option value="perlu_hospital">Perlu Rawatan Hospital</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Penerangan Kejadian</label>
                    <textarea wire:model="incident_description" rows="2" placeholder="Murid tersadung ketika bermain jongkang-jongkit..." class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Kecederaan & Rawatan Awal Diberikan</label>
                    <textarea wire:model="injury_details" rows="2" placeholder="Luka kecil pada lutut kanan. Dicuci dengan antiseptik dan dipasang plaster." class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="parentNotifiedCheck" wire:model="parent_notified" class="rounded text-primary-600 border-gray-300 dark:border-gray-700" />
                    <label for="parentNotifiedCheck" class="text-xs text-gray-700 dark:text-gray-300">Ibu bapa telah dimaklumkan melalui WhatsApp/Panggilan</label>
                </div>

                <x-filament::button
                    type="button"
                    wire:click="saveIncident"
                    color="danger"
                    icon="heroicon-o-shield-exclamation"
                    class="w-full"
                >
                    Simpan ke Buku Log JKM
                </x-filament::button>
            </div>

            {{-- Incident Log History --}}
            <div class="lg:col-span-2 space-y-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center justify-between">
                    <span>Buku Rekod Kemalangan (Arkib JKM)</span>
                    <span class="text-xs font-normal text-gray-500">{{ count($incidentLogs) }} Rekod Kemalangan</span>
                </h3>

                @forelse($incidentLogs as $inc)
                    @php
                        $badge = match($inc['severity']) {
                            'perlu_hospital' => 'bg-rose-600 text-white',
                            'sederhana' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
                        };
                    @endphp
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5 shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase {{ $badge }}">
                                    {{ $inc['severity'] }}
                                </span>
                                <span class="text-xs font-bold text-gray-900 dark:text-white">
                                    {{ $inc['student']['name'] ?? '-' }} ({{ $inc['cohort']['name'] ?? '-' }})
                                </span>
                            </div>
                            <span class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($inc['incident_date'])->format('d/m/Y') }} ({{ substr($inc['incident_time'], 0, 5) }})
                            </span>
                        </div>

                        <div class="text-xs text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/40 p-3 rounded-lg space-y-1">
                            <div><strong>📍 Lokasi:</strong> {{ $inc['location'] }}</div>
                            <div><strong>⚠️ Kejadian:</strong> {{ $inc['incident_description'] }}</div>
                            <div><strong>🩹 Rawatan:</strong> {{ $inc['injury_details'] }}</div>
                        </div>

                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 text-[10px] text-gray-400 flex items-center justify-between">
                            <span>Saksi Guru: {{ $inc['witness_teacher']['full_name'] ?? 'Guru Bertugas' }}</span>
                            <span class="text-emerald-600 font-semibold">
                                {{ $inc['parent_notified'] ? '✓ Ibu bapa telah dimaklumkan' : 'Ibu bapa belum dimaklumkan' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-xs text-gray-500">
                        Alhamdulillah, tiada sebarang rekod kemalangan didaftarkan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
