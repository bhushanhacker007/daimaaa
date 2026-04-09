<?php

namespace App\Livewire\Admin;

use App\Models\Faq;
use Livewire\Component;
use Livewire\WithPagination;

class ManageFaqs extends Component
{
    use WithPagination;

    public bool $showForm = false;
    public ?int $editingId = null;
    public string $question = '';
    public string $answer = '';
    public string $category = '';
    public int $sortOrder = 0;
    public bool $isActive = true;

    public function save()
    {
        $this->validate(['question' => 'required|string', 'answer' => 'required|string']);
        $data = ['question' => $this->question, 'answer' => $this->answer, 'category' => $this->category, 'sort_order' => $this->sortOrder, 'is_active' => $this->isActive];
        $this->editingId ? Faq::findOrFail($this->editingId)->update($data) : Faq::create($data);
        $this->resetForm();
    }

    public function edit(int $id)
    {
        $f = Faq::findOrFail($id);
        $this->editingId = $id;
        $this->question = $f->question;
        $this->answer = $f->answer;
        $this->category = $f->category ?? '';
        $this->sortOrder = $f->sort_order;
        $this->isActive = $f->is_active;
        $this->showForm = true;
    }

    public function delete(int $id) { Faq::findOrFail($id)->delete(); }

    public function resetForm()
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->reset(['question', 'answer', 'category', 'sortOrder']);
        $this->isActive = true;
    }

    public function render()
    {
        return view('livewire.admin.manage-faqs', ['faqs' => Faq::orderBy('sort_order')->paginate(20)]);
    }
}
