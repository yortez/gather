@extends('layouts.app')

@section('title', 'My forms')

@section('content')
    <section class="dashboard-head">
        <div>
            <span class="eyebrow"><span class="eyebrow-dot"></span> YOUR WORKSPACE</span>
            <h1>Good to see you, {{ Str::before(auth()->user()->name, ' ') }}.</h1>
            <p>Every form and every answer, all in one place.</p>
        </div>
        <a class="button button-dark" href="{{ route('forms.create') }}"><span class="button-plus" aria-hidden="true">+</span> New form</a>
    </section>

    <section class="stats-row" aria-label="Workspace overview">
        <div class="stat-block"><span class="stat-label">FORMS CREATED</span><strong>{{ $forms->count() }}</strong><span class="stat-detail">{{ $forms->where('is_published', true)->count() }} currently live</span></div>
        <div class="stat-block stat-accent"><span class="stat-label">ANSWERS COLLECTED</span><strong>{{ $forms->sum('responses_count') }}</strong><span class="stat-detail">Across all your forms</span></div>
        <div class="stat-block stat-quiet"><span class="stat-label">READY TO SHARE</span><strong>{{ $forms->where('is_published', true)->count() }}</strong><span class="stat-detail">Open to new responses</span></div>
    </section>

    <section class="forms-section">
        <div class="section-heading">
            <div><h2>Your forms</h2><span class="section-count">{{ $forms->count() }}</span></div>
            <span class="table-note">LATEST ACTIVITY</span>
        </div>
        @if ($forms->isEmpty())
            <div class="empty-state">
                <div class="empty-mark" aria-hidden="true"><span></span><span></span><span></span></div>
                <h3>Your first form starts here.</h3>
                <p>Add a few questions, then send your share link to start collecting answers.</p>
                <a class="button button-green" href="{{ route('forms.create') }}">Create a form <span aria-hidden="true">→</span></a>
            </div>
        @else
            <div class="form-list">
                <div class="form-list-header"><span>FORM</span><span>STATUS</span><span>RESPONSES</span><span>UPDATED</span><span></span></div>
                @foreach ($forms as $form)
                    <article class="form-row">
                        <div class="form-cell form-title-cell">
                            <span class="form-glyph" aria-hidden="true">{{ mb_strtoupper(mb_substr($form->title, 0, 1)) }}</span>
                            <div><a class="form-row-title" href="{{ route('forms.edit', $form) }}">{{ $form->title }}</a><span class="form-row-meta">{{ count($form->questions) }} {{ Str::plural('question', count($form->questions)) }}</span></div>
                        </div>
                        <div class="form-cell"><span @class(['status-pill', 'status-live' => $form->is_published, 'status-draft' => ! $form->is_published])><i></i>{{ $form->is_published ? 'Live' : 'Draft' }}</span></div>
                        <div class="form-cell response-count"><strong>{{ $form->responses_count }}</strong><span>{{ Str::plural('response', $form->responses_count) }}</span></div>
                        <div class="form-cell updated-cell">{{ $form->updated_at->diffForHumans() }}</div>
                        <div class="form-cell row-actions">
                            <a class="row-action" href="{{ route('forms.responses.index', $form) }}">View answers <span aria-hidden="true">↗</span></a>
                            <a class="row-action row-edit" href="{{ route('forms.edit', $form) }}" aria-label="Edit {{ $form->title }}">Edit</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection