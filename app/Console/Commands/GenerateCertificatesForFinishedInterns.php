<?php

namespace App\Console\Commands;

use App\Models\Intern;
use App\Services\CertificateService;
use Illuminate\Console\Command;

class GenerateCertificatesForFinishedInterns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interns:generate-certificates {--force : Regenerate PDF even if it already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate PDF certificates for interns whose internship has ended';

    /**
     * Execute the console command.
     */
    public function handle(CertificateService $service)
    {
        $interns = Intern::with(['user', 'group', 'request'])
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<=', now()->toDateString())
            ->get();

        $count = 0;
        foreach ($interns as $intern) {
            $certificate = $service->generateForIntern($intern, $this->option('force'));
            if ($certificate) {
                $count++;
                $this->line("• Generated for {$intern->user->name} ({$intern->group?->filiere})");
            }
        }

        $this->info("Certificates generated/updated: {$count}");

        return Command::SUCCESS;
    }
}
