@extends('layouts.app')

@section('title', 'Responses · '.$form->title)

@section('content')
    <section class="responses-head">
        <div>
            <div class="breadcrumbs"><a href="{{ route('forms.index') }}">My forms</a><span>/</span><a href="{{ route('forms.edit', $form) }}">{{ $form->title }}</a><span>/</span><span>Responses</span></div>
            <span class="eyebrow"><span class="eyebrow-dot"></span> RESPONSE INBOX</span>
            <h1>{{ $form->title }}</h1>
            <p>{{ $responses->total() }} {{ Str::plural('response', $responses->total()) }} collected</p>
        </div>
        <div class="response-header-actions">
            <a class="button button-green" href="{{ route('forms.responses.export', $form) }}">Download for Excel (.csv) <span aria-hidden="true">↓</span></a>
            <a class="button button-outline" href="{{ route('forms.edit', $form) }}">Back to form <span aria-hidden="true">↗</span></a>
        </div>
    </section>

    @if ($responses->isEmpty())
        <section class="empty-state response-empty">
            <div class="empty-mark response-empty-mark" aria-hidden="true">↙</div>
            <h3>It's quiet here. For now.</h3>
            <p>Share your form link to start collecting answers.</p>
            @if ($form->is_published)
                <button class="button button-green" type="button" data-copy-link="{{ route('forms.fill', $form) }}">Copy share link <span aria-hidden="true">↗</span></button>
            @else
                <a class="button button-green" href="{{ route('forms.edit', $form) }}">Publish your form <span aria-hidden="true">→</span></a>
            @endif
        </section>
    @else
        <section class="response-list">
            <div class="response-list-head"><h2>All responses</h2><span>Newest first</span></div>
            <div class="responses-sheet-wrap" tabindex="0" aria-label="Scrollable responses sheet">
                <table class="responses-sheet">
                    <thead>
                        <tr>
                            <th class="sheet-row-number" scope="col">#</th>
                            <th class="sheet-submitted" scope="col">Submitted at</th>
                            @foreach ($form->questions as $question)
                                <th scope="col">{{ $question['label'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($responses as $response)
                            <tr>
                                <td class="sheet-row-number">{{ $responses->firstItem() + $loop->index }}</td>
                                <td class="sheet-submitted"><time>{{ $response->created_at->format('M j, Y · g:i a') }}</time></td>
                                @foreach ($form->questions as $question)
                                    @php
                                        $answer = $response->answers[$question['id']] ?? null;
                                        if (is_array($answer) && $question['type'] === 'address') {
                                            $displayAnswer = implode(', ', array_filter([($answer['barangay'] ?? ''), ($answer['city_municipality'] ?? ''), ($answer['province'] ?? '')]));
                                        } elseif (is_array($answer)) {
                                            $displayAnswer = implode(', ', $answer);
                                        } else {
                                            $displayAnswer = $answer;
                                        }
                                    @endphp
                                    <td>{{ $displayAnswer }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $responses->links() }}
        </section>
    @endif
@endsection