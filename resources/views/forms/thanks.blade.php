@extends('layouts.app')

@section('title', 'Response received')

@section('content')
    <section class="thanks-wrap">
        <div class="thanks-mark" aria-hidden="true">✓</div>
        <span class="eyebrow"><span class="eyebrow-dot"></span> RESPONSE RECEIVED</span>
        <h1>Thanks for<br><em>sharing that.</em></h1>
        <p>Your response to <strong>{{ $form->title }}</strong> has been recorded.</p>
        <a class="text-link" href="{{ route('forms.fill', $form) }}">Send another response <span aria-hidden="true">→</span></a>
    </section>
@endsection