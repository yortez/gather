<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FormResponseController extends Controller
{
    public function show(Form $form): View
    {
        abort_unless($form->is_published, 404);

        return view('forms.respond', ['form' => $form]);
    }

    public function store(Request $request, Form $form): RedirectResponse
    {
        abort_unless($form->is_published, 404);

        $questions = collect($form->questions);
        $questionIds = $questions->pluck('id')->all();
        $rules = [
            'answers' => ['required', 'array:'.implode(',', $questionIds)],
        ];

        foreach ($questions as $question) {
            $key = 'answers.'.$question['id'];
            $required = ($question['required'] ?? false) ? 'required' : 'nullable';

            if ($question['type'] === 'checkbox') {
                $rules[$key] = [$required, 'array', 'max:20'];
                $rules[$key.'.*'] = [Rule::in($question['options'] ?? [])];
            } elseif ($question['type'] === 'address') {
                $rules[$key] = [$required, 'array:barangay,city_municipality,province'];
                $addressPartRequired = ($question['required'] ?? false) ? 'required' : 'nullable';

                foreach (['barangay', 'city_municipality', 'province'] as $part) {
                    $rules["{$key}.{$part}"] = [$addressPartRequired, 'string', 'max:255'];
                }
            } else {
                $rules[$key] = [$required, 'string', 'max:5000'];

                if ($question['type'] === 'email') {
                    $rules[$key][] = 'email';
                }

                if ($question['type'] === 'date') {
                    $rules[$key][] = 'date_format:Y-m-d';
                }

                if ($question['type'] === 'choice') {
                    $rules[$key][] = Rule::in($question['options'] ?? []);
                }
            }
        }

        $validated = Validator::make($request->all(), $rules)->validate();
        FormResponse::create([
            'form_id' => $form->id,
            'answers' => $validated['answers'],
        ]);

        return redirect()->route('forms.thanks', $form);
    }

    public function thanks(Form $form): View
    {
        abort_unless($form->is_published, 404);

        return view('forms.thanks', ['form' => $form]);
    }

    public function index(Request $request, Form $form): View
    {
        $form = $request->user()->forms()->whereKey($form->id)->firstOrFail();
        $responses = $form->responses()->latest()->paginate(25);

        return view('forms.responses', [
            'form' => $form,
            'responses' => $responses,
        ]);
    }

    public function export(Request $request, Form $form): StreamedResponse
    {
        $form = $request->user()->forms()->whereKey($form->id)->firstOrFail();
        $questions = $form->questions;

        return response()->streamDownload(function () use ($form, $questions): void {
            $output = fopen('php://output', 'w');

            if ($output === false) {
                return;
            }

            fwrite($output, "\xEF\xBB\xBF");

            $headers = ['Submitted At', ...array_column($questions, 'label')];
            fputcsv($output, array_map($this->spreadsheetCell(...), $headers), ',', '"', '');

            foreach ($form->responses()->orderBy('id')->cursor() as $response) {
                $row = [$response->created_at->format('Y-m-d H:i:s')];

                foreach ($questions as $question) {
                    $row[] = $this->exportAnswer($question, $response->answers[$question['id']] ?? null);
                }

                fputcsv($output, array_map($this->spreadsheetCell(...), $row), ',', '"', '');
            }

            fclose($output);
        }, Str::slug($form->title).'-responses.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function exportAnswer(array $question, mixed $answer): string
    {
        if ($answer === null || $answer === '') {
            return '';
        }

        if (is_array($answer) && $question['type'] === 'address') {
            return implode(', ', array_filter([
                $answer['barangay'] ?? '',
                $answer['city_municipality'] ?? '',
                $answer['province'] ?? '',
            ], fn (string $part): bool => $part !== ''));
        }

        if (is_array($answer)) {
            return implode(', ', array_map('strval', $answer));
        }

        return (string) $answer;
    }

    private function spreadsheetCell(string $value): string
    {
        return preg_match('/^[\x00-\x20]*[=+\-@]/', $value) === 1 ? "'{$value}" : $value;
    }
}
