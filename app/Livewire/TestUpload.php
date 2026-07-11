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
        dd($this->file);
    }

    public function render()
    {
        return view('livewire.test-upload')
            ->layout('layouts.app');
    }
}