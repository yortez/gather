@extends('layouts.app')

@section('title', 'Collect answers, clearly')

@section('content')
    <section class="welcome-layout">
        <div class="welcome-copy">
            <span class="eyebrow"><span class="eyebrow-dot"></span> A calmer way to collect answers</span>
            <h1>Ask better.<br><em>Understand more.</em></h1>
            <p class="welcome-intro">Build a form, share one link, and bring every response together in one clear place.</p>
            <div class="welcome-actions">
                <a class="button button-dark" href="{{ route('register') }}">Create your first form <span aria-hidden="true">→</span></a>
                <a class="text-link" href="{{ route('login') }}">I already have an account</a>
            </div>
            <div class="welcome-proof"><span class="proof-spark">✳</span> Free to get started <span class="proof-divider"></span> No setup required</div>
        </div>
        <div class="welcome-art" aria-label="A preview of a form and its responses">
            <div class="art-note note-top"><span class="note-check">✓</span> One link, easy to share</div>
            <div class="art-paper">
                <div class="paper-kicker">QUICK CHECK-IN <span>04 QUESTIONS</span></div>
                <div class="paper-title">A few thoughts?</div>
                <div class="paper-rule"></div>
                <div class="paper-question">What are you working on this week?</div>
                <div class="paper-input">Your answer</div>
                <div class="paper-question">How's it going so far?</div>
                <div class="paper-choices"><i></i><span>On track</span><i></i><span>A little stuck</span></div>
                <div class="paper-submit">Send response <span>↗</span></div>
            </div>
            <div class="art-note note-bottom"><span class="note-bars"><i></i><i></i><i></i><i></i></span> 12 thoughtful responses</div>
            <span class="art-sun" aria-hidden="true">✳</span>
        </div>
    </section>
    <section class="welcome-bottom">
        <div><span class="bottom-number">01</span><strong>Make it yours</strong><span>Add the questions you need.</span></div>
        <div><span class="bottom-number">02</span><strong>Send it anywhere</strong><span>One simple link for everyone.</span></div>
        <div><span class="bottom-number">03</span><strong>See the whole picture</strong><span>Every answer, in one place.</span></div>
    </section>
@endsection