@extends('layouts.app')

@section('title', $form->exists ? 'Edit form' : 'Create a form')

@section('content')
    @php($isEditing = $form->exists)
    <section class="builder-topline">
        <div class="breadcrumbs"><a href="{{ route('forms.index') }}">My forms</a><span>/</span><span>{{ $isEditing ? 'Edit form' : 'New form' }}</span></div>
        <div class="builder-actions">
            @if ($isEditing)
                <form method="POST" action="{{ route('forms.publish', $form) }}">
                    @csrf
                    <button class="button {{ $form->is_published ? 'button-outline' : 'button-green' }}" type="submit">{{ $form->is_published ? 'Unpublish' : 'Publish form' }}</button>
                </form>
                @if ($form->is_published)
                    <a class="button button-dark" href="{{ route('forms.fill', $form) }}" target="_blank" rel="noreferrer">Open form <span aria-hidden="true">↗</span></a>
                @endif
            @endif
        </div>
    </section>

    <section class="builder-intro">
        <div><span class="eyebrow"><span class="eyebrow-dot"></span> FORM BUILDER</span><h1>{{ $isEditing ? 'Shape your questions.' : 'Start with a question.' }}</h1><p>Keep it simple. You can change everything later.</p></div>
        @if ($isEditing && $form->is_published)
            <button class="share-control" type="button" data-copy-link="{{ route('forms.fill', $form) }}"><span class="share-control-icon" aria-hidden="true">↗</span><span><strong>Copy share link</strong><small>{{ route('forms.fill', $form) }}</small></span></button>
        @endif
    </section>

    @if ($errors->any())
        <div class="form-errors builder-errors" role="alert">{{ $errors->first() }}</div>
    @endif

    <form data-form-builder class="builder-form" method="POST" action="{{ $isEditing ? route('forms.update', $form) : route('forms.store') }}">
        @csrf
        @if ($isEditing) @method('PUT') @endif
        <section class="form-details">
            <span class="detail-index">01</span>
            <div class="detail-fields">
                <label class="field-label">Form title<input type="text" name="title" value="{{ old('title', $form->title) }}" placeholder="e.g. Weekend workshop feedback" maxlength="255" required autofocus></label>
                <label class="field-label">Description <span class="optional-label">OPTIONAL</span><textarea name="description" rows="2" maxlength="2000" placeholder="Add a little context for the people filling this in.">{{ old('description', $form->description) }}</textarea></label>
            </div>
        </section>

        <div class="questions-heading"><div><span class="detail-index">02</span><div><h2>Your questions</h2><p>Choose a question and the kind of answer you need.</p></div></div><span class="question-limit">UP TO 50 QUESTIONS</span></div>
        <div class="question-list" data-question-list></div>
        <button class="add-question" type="button" data-add-question><span aria-hidden="true">+</span> Add a question</button>
        <div class="builder-footer"><a class="text-link" href="{{ route('forms.index') }}">Cancel</a><button class="button button-dark" type="submit">{{ $isEditing ? 'Save changes' : 'Save form' }} <span aria-hidden="true">→</span></button></div>
    </form>

    <script>
        window.formBuilderQuestions = {{ Illuminate\Support\Js::from(old('questions', $form->questions ?? [])) }};
    </script>
@endsection