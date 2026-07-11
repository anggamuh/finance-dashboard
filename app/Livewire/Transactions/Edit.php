<?php

namespace App\Livewire\Transactions;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
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

    public $proof;

    public function mount(Transaction $transaction)
    {
        $this->transaction = $transaction;

        $this->transaction_date = $transaction->transaction_date->format('Y-m-d');
        $this->transaction_number = $transaction->transaction_number;
        $this->type = $transaction->transaction_type;
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
            'account_code' => 'required',
            'payment_method' => 'required',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable',
            'proof' => 'nullable|image|max:4096',
        ];
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {

            $account = Account::where('code', $this->account_code)->first();

            $data = [

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

            ];

            if ($this->proof) {

                if ($this->transaction->proof_file) {
                    Storage::disk('public')->delete(
                        $this->transaction->proof_file
                    );
                }

                $path = $this->proof->store('proofs', 'public');

                $data['proof_file'] = $path;
                $data['proof_original_name'] = $this->proof->getClientOriginalName();
            }

            $this->transaction->update($data);

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