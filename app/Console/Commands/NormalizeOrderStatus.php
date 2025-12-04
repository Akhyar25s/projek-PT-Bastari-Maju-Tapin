<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NormalizeOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:normalize-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalisasi nilai kolom status di tabel order (trim spasi, lowercase)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai normalisasi status order...');

        // Cek status non-standar sebelum normalisasi
        $nonStandard = DB::table('order')
            ->select(DB::raw('DISTINCT status, COUNT(*) as total'))
            ->whereRaw("LOWER(TRIM(status)) NOT IN ('pending','approved','final_approved','rejected')")
            ->groupBy('status')
            ->get();

        if ($nonStandard->isNotEmpty()) {
            $this->warn('Status non-standar ditemukan:');
            foreach ($nonStandard as $s) {
                $hex = bin2hex($s->status ?? '');
                $this->line("  - '{$s->status}' (HEX: {$hex}) - {$s->total} baris");
            }
        }

        // Normalisasi status yang mengandung kata kunci sah
        $mappings = [
            'pending' => ['pending', 'PENDING', 'Pending', ' pending', 'pending '],
            'approved' => ['approved', 'APPROVED', 'Approved', ' approved', 'approved '],
            'final_approved' => ['final_approved', 'FINAL_APPROVED', 'Final_Approved', 'final approved', 'FINAL APPROVED'],
            'rejected' => ['rejected', 'REJECTED', 'Rejected', ' rejected', 'rejected '],
        ];

        $totalUpdated = 0;

        DB::beginTransaction();
        try {
            foreach ($mappings as $correct => $variants) {
                foreach ($variants as $variant) {
                    $affected = DB::table('order')
                        ->where('status', $variant)
                        ->update(['status' => $correct]);
                    
                    if ($affected > 0) {
                        $this->line("  Normalisasi '{$variant}' -> '{$correct}': {$affected} baris");
                        $totalUpdated += $affected;
                    }
                }
            }

            // Normalisasi case-insensitive untuk yang masih belum ter-cover (spasi, mixed case)
            $additionalUpdated = DB::table('order')
                ->whereRaw("LOWER(TRIM(status)) IN ('pending','approved','final_approved','rejected')")
                ->whereRaw("status != LOWER(TRIM(status))")
                ->update([
                    'status' => DB::raw("LOWER(TRIM(status))")
                ]);

            if ($additionalUpdated > 0) {
                $this->line("  Normalisasi tambahan (trim+lowercase): {$additionalUpdated} baris");
                $totalUpdated += $additionalUpdated;
            }

            DB::commit();

            $this->info("Normalisasi selesai. Total {$totalUpdated} baris diperbarui.");

            // Cek apakah masih ada yang non-standar
            $remaining = DB::table('order')
                ->select(DB::raw('DISTINCT status, COUNT(*) as total'))
                ->whereRaw("status NOT IN ('pending','approved','final_approved','rejected')")
                ->groupBy('status')
                ->get();

            if ($remaining->isNotEmpty()) {
                $this->error('Status yang masih non-standar setelah normalisasi:');
                foreach ($remaining as $r) {
                    $hex = bin2hex($r->status ?? '');
                    $this->line("  - '{$r->status}' (HEX: {$hex}) - {$r->total} baris");
                }
                $this->warn('Periksa nilai status ini secara manual atau perbarui mapping.');
            } else {
                $this->info('✓ Semua status sudah standar.');
            }

            return 0;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Gagal normalisasi: ' . $e->getMessage());
            return 1;
        }
    }
}
