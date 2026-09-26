@extends('layouts.app')

@section('title', $form->title)

@section('content')
    <section class="respond-wrap">
        <div class="respond-brand"><span class="brand-mark" aria-hidden="true"><span></span><span></span><span></span></span> gather<span class="brand-period">.</span></div>
        <form class="respond-form" method="POST" action="{{ route('forms.responses.store', $form) }}">
            @csrf
            <header class="respond-heading"><span class="respond-kicker">SHARED WITH YOU</span><h1>{{ $form->title }}</h1>
                @if ($form->description)<p>{{ $form->description }}</p>@endif
                <div class="respond-required"><span>*</span> Required</div>
            </header>
            @if ($errors->any())
                <div class="form-errors" role="alert">{{ $errors->first() }}</div>
            @endif
            @foreach ($form->questions as $question)
                <fieldset class="respond-question">
                    <legend>{{ $question['label'] }} @if ($question['required'] ?? false)<span class="required-star" aria-label="required">*</span>@endif</legend>
                    @if ($question['type'] === 'paragraph')
                        <textarea name="answers[{{ $question['id'] }}]" rows="4" @required($question['required'] ?? false) placeholder="Your answer">{{ old('answers.'.$question['id']) }}</textarea>
                    @elseif ($question['type'] === 'date')
                        <input type="date" name="answers[{{ $question['id'] }}]" value="{{ old('answers.'.$question['id']) }}" @required($question['required'] ?? false)>
                    @elseif ($question['type'] === 'address')
                        @php($addressAnswer = old('answers.'.$question['id'], []))
                        <div class="address-fields">
                            @foreach (['barangay' => 'Barangay', 'city_municipality' => 'City/Municipality', 'province' => 'Province'] as $part => $label)
                                <label class="field-label">{{ $label }}<input type="text" name="answers[{{ $question['id'] }}][{{ $part }}]" value="{{ data_get($addressAnswer, $part) }}" @required($question['required'] ?? false) maxlength="255" placeholder="{{ $label }}"></label>
                            @endforeach
                        </div>
                    @elseif ($question['type'] === 'choice')
                        <div class="respond-options">
                            @foreach ($question['options'] ?? [] as $option)
                                <label class="respond-option"><input type="radio" name="answers[{{ $question['id'] }}]" value="{{ $option }}" @checked(old('answers.'.$question['id']) === $option) @required(($question['required'] ?? false) && $loop->first)><span class="radio-dot"></span>{{ $option }}</label>
                            @endforeach
                        </div>
                    @elseif ($question['type'] === 'checkbox')
                        <div class="respond-options">
                            @foreach ($question['options'] ?? [] as $option)
                                <label class="respond-option"><input type="checkbox" name="answers[{{ $question['id'] }}][]" value="{{ $option }}" @checked(in_array($option, old('answers.'.$question['id'], [])) )><span class="checkbox-mark"></span>{{ $option }}</label>
                            @endforeach
                        </div>
                    @else
                        <input type="{{ $question['type'] === 'email' ? 'email' : 'text' }}" name="answers[{{ $question['id'] }}]" value="{{ old('answers.'.$question['id']) }}" @required($question['required'] ?? false) maxlength="5000" placeholder="Your answer">
                    @endif
                </fieldset>
            @endforeach
            <div class="respond-submit"><button class="button button-dark" type="submit">Send response <span aria-hidden="true">→</span></button><span>Your response will be shared with the form owner.</span></div>
        </form>
        <p class="respond-footer">Built with <a href="{{ route('home') }}">Gather</a></p>
    </section>
@endsection