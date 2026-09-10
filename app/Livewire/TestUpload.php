<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class TestUpload extends Component
{
    use WithFileUploads;

    public $file;

    public function save()
    {
        $this->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        session()->flash('success', 'File berhasil divalidasi.');
        $this->reset('file');
    }

    public function render()
    {
        return view('livewire.test-upload')
            ->layout('layouts.app');
    }
}
