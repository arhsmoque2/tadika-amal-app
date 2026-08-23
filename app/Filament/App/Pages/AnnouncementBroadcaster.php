<?php

namespace App\Filament\App\Pages;

use App\Models\Announcement;
use App\Models\Cohort;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class AnnouncementBroadcaster extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Hebahan & WhatsApp';
    protected static ?string $title = 'Hebahan Pengumuman & Penjana Mesej WhatsApp';
    protected static ?int $navigationSort = 5;
    protected string $view = 'filament.pages.announcement-broadcaster';

    public string $title_input = '';
    public string $content_input = '';
    public string $category_input = 'general';
    public string $target_audience_input = 'all';
    public ?int $cohort_id_input = null;

    public array $announcements = [];
    public array $cohortOptions = [];

    public function mount(): void
    {
        $this->cohortOptions = Cohort::where('is_active', true)->pluck('name', 'id')->toArray();
        $this->loadAnnouncements();
    }

    public function loadAnnouncements(): void
    {
        $this->announcements = Announcement::with(['cohort', 'author'])
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get()
            ->toArray();
    }

    public function createAnnouncement(): void
    {
        if (empty($this->title_input) || empty($this->content_input)) {
            Notification::make()
                ->title('Sila isi tajuk dan kandungan pengumuman')
                ->danger()
                ->send();
            return;
        }

        $user = Auth::user();
        $schoolId = 1; // Primary tenant

        Announcement::create([
            'school_id' => $schoolId,
            'cohort_id' => $this->target_audience_input === 'cohort' ? $this->cohort_id_input : null,
            'title' => $this->title_input,
            'content' => $this->content_input,
            'category' => $this->category_input,
            'target_audience' => $this->target_audience_input,
            'published_at' => now(),
            'created_by' => $user?->id,
            'is_active' => true,
        ]);

        $this->reset(['title_input', 'content_input']);
        $this->loadAnnouncements();

        Notification::make()
            ->title('Pengumuman berjaya diterbitkan!')
            ->success()
            ->send();
    }
}
