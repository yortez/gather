<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FormController extends Controller
{
    public function index(Request $request): View
    {
        $forms = $request->user()->forms()->withCount('responses')->latest()->get();

        return view('forms.index', ['forms' => $forms]);
    }

    public function create(): View
    {
        return view('forms.builder', ['form' => new Form]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedForm($request);
        $form = $request->user()->forms()->create([
            ...$data,
            'slug' => Str::slug($data['title']).'-'.Str::lower(Str::random(8)),
        ]);

        return redirect()->route('forms.edit', $form)->with('status', 'Your form is ready to share.');
    }

    public function edit(Request $request, Form $form): View
    {
        $form = $this->ownedForm($request->user(), $form);

        return view('forms.builder', ['form' => $form]);
    }

    public function update(Request $request, Form $form): RedirectResponse
    {
        $form = $this->ownedForm($request->user(), $form);
        $form->update($this->validatedForm($request));

        return back()->with('status', 'Changes saved.');
    }

    public function togglePublished(Request $request, Form $form): RedirectResponse
    {
        $form = $this->ownedForm($request->user(), $form);
        $form->update(['is_published' => ! $form->is_published]);

        return back()->with('status', $form->is_published ? 'Form published.' : 'Form unpublished.');
    }

    public function destroy(Request $request, Form $form): RedirectResponse
    {
        $this->ownedForm($request->user(), $form)->delete();

        return redirect()->route('forms.index')->with('status', 'Form deleted.');
    }

    private function ownedForm(User $user, Form $form): Form
    {
        return $user->forms()->findOrFail($form->id);
    }

    private function validatedForm(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'questions' => ['required', 'array', 'min:1', 'max:50'],
            'questions.*.id' => ['required', 'uuid', 'distinct'],
            'questions.*.label' => ['required', 'string', 'max:255'],
            'questions.*.type' => ['required', 'in:short,paragraph,email,date,address,choice,checkbox'],
            'questions.*.required' => ['sometimes', 'boolean'],
            'questions.*.options' => ['nullable', 'array', 'max:20'],
            'questions.*.options.*' => ['required', 'string', 'max:120'],
        ]);

        foreach ($data['questions'] as $index => $question) {
            $options = array_values(array_filter(array_map('trim', $question['options'] ?? [])));

            if (in_array($question['type'], ['choice', 'checkbox'], true) && count($options) < 2) {
                throw ValidationException::withMessages([
                    "questions.{$index}.options" => 'Add at least two options to this question.',
                ]);
            }

            $data['questions'][$index]['options'] = $options;
            $data['questions'][$index]['required'] = (bool) ($question['required'] ?? false);
        }

        return $data;
    }
}
