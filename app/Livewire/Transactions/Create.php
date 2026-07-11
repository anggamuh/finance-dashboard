<?php

namespace App\Livewire\Transactions;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
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

    public $proof;

    protected function rules()
    {
        return [
            'transaction_date' => ['required', 'date'],
            'account_code'     => ['required'],
            'amount'           => ['required', 'numeric', 'min:1'],
            'description'      => ['nullable', 'string'],
            'payment_method'   => ['required'],
            'proof'            => ['nullable', 'image', 'max:4096'],
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

        $this->transaction_number = 'TRX-'.$today.'-'.str_pad($last,4,'0',STR_PAD_LEFT);
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {

            $account = Account::where('code',$this->account_code)->first();

            $proofPath = null;
            $originalName = null;

            if($this->proof){

                $proofPath = $this->proof->store('proofs','public');

                $originalName = $this->proof->getClientOriginalName();

            }

            Transaction::create([

                'account_code' => $account->code,

                'account_name' => $account->name,

                'transaction_number' => $this->transaction_number,

                'transaction_date' => $this->transaction_date,

                'transaction_type' => $this->type,

                'description' => $this->description,

                'payment_method' => $this->payment_method,

                'proof_file' => $proofPath,

                'proof_original_name' => $originalName,

                'created_by' => Auth::id(),

                'debit' => $this->type == 'expense'
                    ? $this->amount
                    : 0,

                'credit' => $this->type == 'income'
                    ? $this->amount
                    : 0,

            ]);

        });

        session()->flash(
            'success',
            'Transaksi berhasil disimpan.'
        );

        return redirect()->route('transactions');
    }

    public function render()
    {
        return view('livewire.transactions.create',[

            'accounts' => Account::orderBy('code')->get()

        ])->layout('layouts.app');
    }
}