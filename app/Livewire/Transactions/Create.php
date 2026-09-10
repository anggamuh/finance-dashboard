<?php

namespace App\Livewire\Transactions;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $transaction_date;

    public $transaction_number;

    public $type = 'expense';

    public $payment_method = 'Bank BCA';

    public $account_code = '';

    public $amount;

    public $description;

    /** @var TemporaryUploadedFile[] */
    public $proofs = [];

    protected function rules()
    {
        return [
            'transaction_date' => ['required', 'date'],
            'account_code' => ['required', 'exists:accounts,code'],
            'type' => ['required', 'in:expense,income'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string'],
            'payment_method' => ['required', 'in:Bank BCA,Petty Cash,Transfer,QRIS,Tunai'],
            'proofs.*' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function mount()
    {
        $this->transaction_date = now()->format('Y-m-d');
        $this->generateNumber();
    }

    public function generateNumber()
    {
        $today = now()->format('Ymd');
        $last = Transaction::whereDate('created_at', today())->count() + 1;
        $this->transaction_number = 'TRX-'.$today.'-'.str_pad($last, 4, '0', STR_PAD_LEFT);
    }

    public function removeProof($index)
    {
        unset($this->proofs[$index]);
        $this->proofs = array_values($this->proofs);
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $account = Account::where('code', $this->account_code)->first();

            $transaction = Transaction::create([
                'account_code' => $account->code,
                'account_name' => $account->name,
                'transaction_number' => $this->transaction_number,
                'transaction_date' => $this->transaction_date,
                'transaction_type' => $this->type,
                'description' => $this->description,
                'payment_method' => $this->payment_method,
                'created_by' => Auth::id(),
                'debit' => $this->type == 'expense'
                    ? $this->amount
                    : 0,
                'credit' => $this->type == 'income'
                    ? $this->amount
                    : 0,
            ]);

            foreach ($this->proofs as $proof) {
                $path = $proof->store('proofs', 'public');

                $transaction->attachments()->create([
                    'file_path' => $path,
                    'original_name' => $proof->getClientOriginalName(),
                ]);
            }
        });

        session()->flash(
            'success',
            'Transaksi berhasil disimpan.'
        );

        return redirect()->route('transactions');
    }

    public function render()
    {
        return view('livewire.transactions.create', [
            'accounts' => Account::orderBy('code')->get(),
        ])->layout('layouts.app');
    }
}
