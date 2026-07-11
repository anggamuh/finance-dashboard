<?php

namespace App\Livewire\Accurate;

use App\Models\Import;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $title;
    public $period;
    public $file;

    protected function rules()
    {
        return [

            'title' => 'required',

            'file' => 'required|file|max:10240',

        ];
    }

    public function save()
    {
        $this->validate();

        $path = $this->file->store('accurate', 'public');

        Import::create([

            'file_name' => $this->file->getClientOriginalName(),

            'file_path' => $path,

            'total_rows' => 0,

            'status' => 'completed',

        ]);

        $this->reset();

        session()->flash('success','File berhasil diupload.');
    }

 public function delete($id)
{
    $file = Import::findOrFail($id);

    // Hapus semua transaksi yang terkait dengan import ini
    $file->transactions()->delete();

    // Hapus file dari storage
    if (! empty($file->file_path)) {
        Storage::disk('public')->delete($file->file_path);
    }

    // Hapus record import
    $file->delete();

    session()->flash('success', 'File dan semua transaksi terkait berhasil dihapus.');
}

    public function render()
    {
        return view('livewire.accurate.index',[
            'files'=>Import::latest()->paginate(10)
        ])->layout('layouts.app');
    }
}