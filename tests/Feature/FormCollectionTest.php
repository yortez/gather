<?php

use App\Models\Form;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('stores submitted answers for a published form', function () {
    $owner = User::factory()->create();
    $questionId = (string) Str::uuid();
    $form = Form::create([
        'user_id' => $owner->id,
        'title' => 'Workshop feedback',
        'slug' => 'workshop-feedback',
        'questions' => [
            ['id' => $questionId, 'label' => 'What did you think?', 'type' => 'short', 'required' => true],
        ],
        'is_published' => true,
    ]);

    $response = $this->post(route('forms.responses.store', $form), [
        'answers' => [$questionId => 'A thoughtful session.'],
    ]);

    $response->assertRedirect(route('forms.thanks', $form));
    $this->assertDatabaseHas('form_responses', [
        'form_id' => $form->id,
        'answers' => json_encode([$questionId => 'A thoughtful session.']),
    ]);
});

it('does not expose a form response dashboard to another owner', function () {
    $owner = User::factory()->create();
    $form = Form::create([
        'user_id' => $owner->id,
        'title' => 'Private feedback',
        'slug' => 'private-feedback',
        'questions' => [],
        'is_published' => true,
    ]);

    $response = $this->actingAs(User::factory()->create())
        ->get(route('forms.responses.index', $form));

    $response->assertNotFound();
});

it('downloads an Excel-compatible response export only for the form owner', function () {
    $owner = User::factory()->create();
    $nameQuestionId = (string) Str::uuid();
    $addressQuestionId = (string) Str::uuid();
    $form = Form::create([
        'user_id' => $owner->id,
        'title' => 'Community Visit',
        'slug' => 'community-visit',
        'questions' => [
            ['id' => $nameQuestionId, 'label' => 'Visitor name', 'type' => 'short', 'required' => false],
            ['id' => $addressQuestionId, 'label' => 'Home address', 'type' => 'address', 'required' => false],
        ],
        'is_published' => true,
    ]);
    $form->responses()->create([
        'answers' => [
            $nameQuestionId => '=2+2',
            $addressQuestionId => [
                'barangay' => 'Poblacion',
                'city_municipality' => 'Quezon City',
                'province' => 'Metro Manila',
            ],
        ],
    ]);

    $response = $this->actingAs($owner)->get(route('forms.responses.export', $form));

    $response->assertDownload('community-visit-responses.csv');
    expect($response->streamedContent())
        ->toContain('"Submitted At","Visitor name","Home address"')
        ->toContain("'=2+2")
        ->toContain('Poblacion, Quezon City, Metro Manila');

    $this->actingAs(User::factory()->create())
        ->get(route('forms.responses.export', $form))
        ->assertNotFound();
});

it('lets an owner create publish and review a form', function () {
    $owner = User::factory()->create();
    $questionId = (string) Str::uuid();
    $this->actingAs($owner);

    $this->get(route('forms.index'))->assertSee('Good to see you');
    $this->get(route('forms.create'))->assertSee('Start with a question.');

    $this->post(route('forms.store'), [
        'title' => 'Studio visit',
        'description' => 'Tell us what you thought.',
        'questions' => [[
            'id' => $questionId,
            'label' => 'How was your visit?',
            'type' => 'choice',
            'required' => '1',
            'options' => ['Good', 'Great'],
        ]],
    ])->assertRedirect();

    $form = $owner->forms()->firstOrFail();
    $this->assertDatabaseHas('forms', ['id' => $form->id, 'title' => 'Studio visit']);
    $this->get(route('forms.edit', $form))->assertSee('How was your visit?');

    $this->post(route('forms.publish', $form))->assertRedirect();
    $this->get(route('forms.fill', $form))->assertSee('How was your visit?')->assertSee('Great');
    $this->post(route('forms.responses.store', $form), [
        'answers' => [$questionId => 'Great'],
    ])->assertRedirect(route('forms.thanks', $form));

    $this->get(route('forms.responses.index', $form))
        ->assertSee('<table class="responses-sheet">', false)
        ->assertSee('<th scope="col">How was your visit?</th>', false)
        ->assertSee('Great');
});

it('collects dates and structured address answers', function () {
    $owner = User::factory()->create();
    $dateQuestionId = (string) Str::uuid();
    $addressQuestionId = (string) Str::uuid();

    $this->actingAs($owner)->post(route('forms.store'), [
        'title' => 'Community visit',
        'questions' => [
            [
                'id' => $dateQuestionId,
                'label' => 'Visit date',
                'type' => 'date',
                'required' => '1',
            ],
            [
                'id' => $addressQuestionId,
                'label' => 'Your address',
                'type' => 'address',
                'required' => '1',
            ],
        ],
    ])->assertRedirect();

    $form = $owner->forms()->firstOrFail();
    $this->post(route('forms.publish', $form))->assertRedirect();

    $this->get(route('forms.fill', $form))
        ->assertSee('type="date"', false)
        ->assertSee('Barangay')
        ->assertSee('City/Municipality')
        ->assertSee('Province');

    $answers = [
        $dateQuestionId => '2026-10-01',
        $addressQuestionId => [
            'barangay' => 'Poblacion',
            'city_municipality' => 'Quezon City',
            'province' => 'Metro Manila',
        ],
    ];

    $invalidDateAnswers = $answers;
    $invalidDateAnswers[$dateQuestionId] = 'next month';
    $this->from(route('forms.fill', $form))
        ->post(route('forms.responses.store', $form), ['answers' => $invalidDateAnswers])
        ->assertSessionHasErrors("answers.{$dateQuestionId}");
    $this->assertDatabaseCount('form_responses', 0);

    $incompleteAddressAnswers = $answers;
    unset($incompleteAddressAnswers[$addressQuestionId]['province']);
    $this->from(route('forms.fill', $form))
        ->post(route('forms.responses.store', $form), ['answers' => $incompleteAddressAnswers])
        ->assertSessionHasErrors("answers.{$addressQuestionId}.province");
    $this->assertDatabaseCount('form_responses', 0);

    $this->post(route('forms.responses.store', $form), ['answers' => $answers])
        ->assertRedirect(route('forms.thanks', $form));

    $this->assertDatabaseHas('form_responses', [
        'form_id' => $form->id,
        'answers' => json_encode($answers),
    ]);
    $this->get(route('forms.responses.index', $form))
        ->assertSee('Barangay: Poblacion, City/Municipality: Quezon City, Province: Metro Manila');
});

it('rejects an answer that is not one of the published choices', function () {
    $owner = User::factory()->create();
    $questionId = (string) Str::uuid();
    $form = Form::create([
        'user_id' => $owner->id,
        'title' => 'Workshop',
        'slug' => 'workshop',
        'questions' => [[
            'id' => $questionId,
            'label' => 'How was it?',
            'type' => 'choice',
            'required' => true,
            'options' => ['Good', 'Great'],
        ]],
        'is_published' => true,
    ]);

    $response = $this->from(route('forms.fill', $form))->post(route('forms.responses.store', $form), [
        'answers' => [$questionId => 'Unexpected'],
    ]);

    $response->assertSessionHasErrors("answers.{$questionId}");
    $this->assertDatabaseCount('form_responses', 0);
});

it('registers an owner and signs them in', function () {
    $response = $this->post(route('register'), [
        'name' => 'Alex Morgan',
        'email' => 'alex@example.com',
        'password' => 'a-secure-password',
        'password_confirmation' => 'a-secure-password',
    ]);

    $response->assertRedirect(route('forms.index'));
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', ['email' => 'alex@example.com']);
});
