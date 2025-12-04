<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateRekapJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $idBulan;
    public $tipe;
    public $tahun;

    /**
     * Create a new job instance.
     */
    public function __construct($idBulan, $tipe = 'sr', $tahun = null)
    {
        $this->idBulan = $idBulan;
        $this->tipe = $tipe;
        $this->tahun = $tahun;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            \App\Http\Controllers\RekapController::generateRekapByTipe($this->idBulan, $this->tipe, $this->tahun);
        } catch (\Exception $e) {
            // Log inside controller or queue worker
            \Illuminate\Support\Facades\Log::error('GenerateRekapJob failed: ' . $e->getMessage(), ['exception' => $e]);
        }
    }
}
