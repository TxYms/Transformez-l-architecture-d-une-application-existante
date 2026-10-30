<?php

namespace App\Livewire;

use App\Http\Requests\Tag\StoreTagRequest;
use App\Services\TagService;
use Livewire\Component;

class TagForm extends Component
{
    public $name = '';

    protected function rules(): array
    {
        return StoreTagRequest::tagRules();
    }

    public function save(TagService $tagService)
    {
        $validated = $this->validate();

        $tagService->create($validated);

        $this->reset('name');

        $this->dispatch('tagCreated');

        session()->flash('message', 'Tag added!');
    }

    public function render()
    {
        return view('livewire.tag-form');
    }
}
