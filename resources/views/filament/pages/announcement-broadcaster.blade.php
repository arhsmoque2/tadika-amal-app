<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Announcement Composer Form --}}
            <div class="lg:col-span-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <x-heroicon-o-pencil-square class="w-5 h-5 text-primary-500" />
                    Cipta Pengumuman Baru
                </h3>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Tajuk Pengumuman</label>
                    <input
                        type="text"
                        wire:model="title_input"
                        placeholder="Contoh: Cuti Peristiwa Sambutan Maulidur Rasul"
                        class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"
                    />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                        <select wire:model="category_input" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                            <option value="general">Umum</option>
                            <option value="urgent">Penting / Cemas</option>
                            <option value="event">Acara / Majlis</option>
                            <option value="holiday">Cuti Peristiwa</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Sasaran</label>
                        <select wire:model.live="target_audience_input" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                            <option value="all">Semua Ibu Bapa</option>
                            <option value="parents">Ibu Bapa Sahaja</option>
                            <option value="teachers">Guru Sahaja</option>
                            <option value="cohort">Kelas Tertentu</option>
                        </select>
                    </div>
                </div>

                @if($target_audience_input === 'cohort')
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Pilih Kelas</label>
                        <select wire:model="cohort_id_input" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                            @foreach($cohortOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Kandungan Pengumuman</label>
                    <textarea
                        wire:model="content_input"
                        rows="6"
                        placeholder="Tuliskan butiran pengumuman, tarikh, masa, dan tindakan yang perlu diambil oleh ibu bapa..."
                        class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"
                    ></textarea>
                </div>

                <x-filament::button
                    type="button"
                    wire:click="createAnnouncement"
                    color="primary"
                    icon="heroicon-o-paper-airplane"
                    class="w-full"
                >
                    Terbit & Jana Format WhatsApp
                </x-filament::button>
            </div>

            {{-- Published Announcements Feed with 1-Click WhatsApp --}}
            <div class="lg:col-span-2 space-y-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center justify-between">
                    <span>Senarai Hebahan & Siaran</span>
                    <span class="text-xs font-normal text-gray-500">{{ count($announcements) }} Pengumuman</span>
                </h3>

                @forelse($announcements as $ann)
                    @php
                        $model = \App\Models\Announcement::find($ann['id']);
                        $waUrl = $model ? $model->whats_app_url : '#';
                        $categoryBadge = match($ann['category']) {
                            'urgent' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200',
                            'event' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-200',
                            'holiday' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
                            default => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
                        };
                    @endphp
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5 shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider {{ $categoryBadge }}">
                                    {{ $ann['category'] }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($ann['published_at'])->format('d M Y, h:i A') }}
                                </span>
                            </div>

                            <a
                                href="{{ $waUrl }}"
                                target="_blank"
                                rel="noopener"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition"
                            >
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                                </svg>
                                Hantar ke WhatsApp
                            </a>
                        </div>

                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                            {{ $ann['title'] }}
                        </h4>

                        <p class="text-xs text-gray-600 dark:text-gray-300 whitespace-pre-line leading-relaxed">
                            {{ $ann['content'] }}
                        </p>

                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 text-[10px] text-gray-400 flex items-center justify-between">
                            <span>Sasaran: {{ strtoupper($ann['target_audience']) }}</span>
                            <span>Diterbitkan oleh: {{ $ann['author']['name'] ?? 'Pihak Pengurusan' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-xs text-gray-500">
                        Belum ada sebarang pengumuman diterbitkan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
