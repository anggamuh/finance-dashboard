<?php

namespace App\Services;

use App\Models\ReconciliationBatch;
use App\Models\ReconciliationEntry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReconciliationMatcher
{
    // Only a unique candidate can be auto-matched. Ambiguous candidates are suggestions.
    public function run(ReconciliationBatch $batch): void
    {
        DB::transaction(function () use ($batch) {
            $entries = $batch->entries()->where('status', '!=', 'matched')->orderBy('id')->get();
            $bank = $entries->where('source', 'bank');
            $ledger = $entries->where('source', 'ledger');
            // Clear previous suggestions before calculating a new proposal.
            $batch->entries()->where('status', 'suggested')->update(['status'=>'unmatched','matched_entry_id'=>null,'confidence'=>null]);
            $used = [];
            foreach ($bank as $item) {
                $candidates = $ledger->filter(function ($other) use ($item, $used) {
                    return !isset($used[$other->id])
                        && $other->status !== 'matched'
                        && $other->direction === $item->direction
                        && (string) $other->amount === (string) $item->amount
                        && abs(Carbon::parse($other->posted_at)->diffInDays(Carbon::parse($item->posted_at), false)) <= 3;
                });
                if ($candidates->count() !== 1) {
                    continue;
                }
                $candidate = $candidates->first();
                // Require candidate to be unique in the other direction too.
                $reverse = $bank->filter(fn ($other) =>
                    $other->status !== 'matched'
                    && $other->direction === $candidate->direction
                    && (string) $other->amount === (string) $candidate->amount
                    && abs(Carbon::parse($other->posted_at)->diffInDays(Carbon::parse($candidate->posted_at), false)) <= 3
                );
                if ($reverse->count() !== 1) {
                    continue;
                }
                $used[$candidate->id] = true;
                $days = abs(Carbon::parse($candidate->posted_at)->diffInDays(Carbon::parse($item->posted_at), false));
                $confidence = $days === 0 ? 95 : (int) (90 - 10 * $days);
                $item->update(['status'=>'suggested','matched_entry_id'=>$candidate->id,'confidence'=>$confidence]);
                $candidate->update(['status'=>'suggested','matched_entry_id'=>$item->id,'confidence'=>$confidence]);
            }
        });
    }

    public function decide(ReconciliationEntry $entry, bool $approve): void
    {
        DB::transaction(function () use ($entry, $approve) {
            $entry = ReconciliationEntry::query()->lockForUpdate()->findOrFail($entry->id);
            if ($entry->status !== 'suggested' || !$entry->matched_entry_id) {
                throw new \DomainException('Pasangan transaksi ini sudah berubah. Muat ulang halaman.');
            }
            $other = ReconciliationEntry::query()->lockForUpdate()->findOrFail($entry->matched_entry_id);
            if ($other->matched_entry_id !== $entry->id || $other->batch_id !== $entry->batch_id || $other->status !== 'suggested') {
                throw new \DomainException('Pasangan transaksi tidak valid.');
            }
            foreach ([$entry, $other] as $row) {
                $row->update([
                    'status' => $approve ? 'matched' : 'unmatched',
                    'matched_entry_id' => $approve ? ($row->id === $entry->id ? $other->id : $entry->id) : null,
                    'confidence' => $approve ? $row->confidence : null,
                ]);
            }
        });
    }
}