<?php

namespace App\Jobs;

use App\Models\University;
use App\Services\Universities\UniversityResearcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ResearchUniversity implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $universityId, public bool $applyEmpty = false) {}

    public function handle(UniversityResearcher $researcher): void
    {
        $university = University::query()->findOrFail($this->universityId);
        $researcher->research($university, $this->applyEmpty);
    }
}
