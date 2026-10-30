<?php

namespace App\Livewire;

use App\Http\Requests\Note\StoreNoteRequest;
use App\Services\NoteService;
use App\Services\TagService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Notes extends Component
{
    public $notes;

    public $text = '';

    public $tag_id = '';

    public $tags;

    protected $listeners = ['tagCreated' => 'refreshTags'];

    protected function rules(): array
    {
        return StoreNoteRequest::noteRules();
    }

    public function mount(TagService $tagService, NoteService $noteService)
    {
        $this->tags = $tagService->list();
        $this->loadNotes($noteService);
    }

    public function loadNotes(NoteService $noteService)
    {
        $this->notes = $noteService->listFor(Auth::user());
    }

    public function refreshTags(TagService $tagService)
    {
        $this->tags = $tagService->list();
    }

    public function save(NoteService $noteService)
    {
        $validated = $this->validate();

        $noteService->createFor(Auth::user(), $validated);

        $this->text = '';
        $this->tag_id = '';

        $this->loadNotes($noteService);

        session()->flash('message', 'Note added.');
    }

    public function delete($noteId, NoteService $noteService)
    {
        $note = $noteService->find((int) $noteId);
        Gate::authorize('delete', $note);

        $noteService->delete($note);
        $this->loadNotes($noteService);
    }

    public function render()
    {
        return view('livewire.notes');
    }
}
