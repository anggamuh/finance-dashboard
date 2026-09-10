<?php

namespace App\Livewire\Transactions;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionAttachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public Transaction $transaction;

    public $transaction_date;

    public $transaction_number;

    public $type;

    public $payment_method;

    public $account_code;

    public $amount;

    public $description;

    /** @var TemporaryUploadedFile[] */
    public $proofs = [];

    public function mount(Transaction $transaction)
    {
        $this->transaction = $transaction;
        $this->transaction_date = $transaction->transaction_date->format('Y-m-d');
        $this->transaction_number = $transaction->transaction_number;

        // Normalisasi type dari debit/credit, bukan dari transaction_type mentah
        $this->type = $transaction->credit > 0 ? 'income' : 'expense';

        $this->payment_method = $transaction->payment_method;
        $this->account_code = $transaction->account_code;
        $this->description = $transaction->description;
        $this->amount = $transaction->debit > 0
            ? $transaction->debit
            : $transaction->credit;
    }

    protected function rules()
    {
        return [
            'transaction_date' => 'required|date',
            'account_code' => 'required|exists:accounts,code',
            'type' => 'required|in:expense,income',
            'payment_method' => 'required|in:Bank BCA,Petty Cash,Transfer,QRIS,Tunai',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable',
            'proofs.*' => 'nullable|image|max:4096',
        ];
    }

    public function removeProof($index)
    {
        unset($this->proofs[$index]);
        $this->proofs = array_values($this->proofs);
    }

    public function deleteAttachment($attachmentId)
    {
        $attachment = TransactionAttachment::where('transaction_id', $this->transaction->id)
            ->findOrFail($attachmentId);

        Storage::disk('public')->delete($attachment->file_path);

        $attachment->delete();

        $this->transaction->refresh();
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $account = Account::where('code', $this->account_code)->first();

            $this->transaction->update([
                'transaction_date' => $this->transaction_date,
                'transaction_type' => $this->type,
                'payment_method' => $this->payment_method,
                'account_code' => $account->code,
                'account_name' => $account->name,
                'description' => $this->description,
                'debit' => $this->type == 'expense'
                    ? $this->amount
                    : 0,
                'credit' => $this->type == 'income'
                    ? $this->amount
                    : 0,
            ]);

            foreach ($this->proofs as $proof) {
                $path = $proof->store('proofs', 'public');

                $this->transaction->attachments()->create([
                    'file_path' => $path,
                    'original_name' => $proof->getClientOriginalName(),
                ]);
            }
        });

        session()->flash('success', 'Transaksi berhasil diperbarui.');

        return redirect()->route('transactions');
    }

    public function render()
    {
        return view('livewire.transactions.edit', [
            'accounts' => Account::orderBy('code')->get(),
        ])->layout('layouts.app');
    }
}
