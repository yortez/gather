const builder = document.querySelector('[data-form-builder]');

if (builder) {
	const list = builder.querySelector('[data-question-list]');
	const questionTypes = [
		['short', 'Short answer'],
		['paragraph', 'Paragraph'],
		['email', 'Email'],
		['date', 'Date'],
		['address', 'Address'],
		['choice', 'Multiple choice'],
		['checkbox', 'Checkboxes'],
	];

	function addOption(container, value = '') {
		const row = document.createElement('div');
		row.className = 'option-row';

		const input = document.createElement('input');
		input.type = 'text';
		input.value = value;
		input.placeholder = 'Option';
		input.dataset.optionInput = '';
		input.maxLength = 120;
		row.append(input);

		const remove = document.createElement('button');
		remove.type = 'button';
		remove.className = 'icon-button';
		remove.setAttribute('aria-label', 'Remove option');
		remove.textContent = '×';
		remove.addEventListener('click', () => row.remove());
		row.append(remove);
		container.append(row);
	}

	function reindexQuestions() {
		list.querySelectorAll('.question-card').forEach((card, index) => {
			card.querySelector('[data-question-id]').name = `questions[${index}][id]`;
			card.querySelector('[data-question-label]').name = `questions[${index}][label]`;
			card.querySelector('[data-question-type]').name = `questions[${index}][type]`;
			card.querySelector('[data-question-required]').name = `questions[${index}][required]`;
			card.querySelectorAll('[data-option-input]').forEach((input) => {
				input.name = `questions[${index}][options][]`;
				input.disabled = !['choice', 'checkbox'].includes(card.querySelector('[data-question-type]').value);
			});
			card.querySelector('[data-question-number]').textContent = String(index + 1).padStart(2, '0');
		});
	}

	function addQuestion(question = {}) {
		const card = document.createElement('article');
		card.className = 'question-card';

		const header = document.createElement('div');
		header.className = 'question-card-header';
		header.innerHTML = '<span class="question-index"><span data-question-number></span></span><span class="question-grip" aria-hidden="true">•••</span>';

		const remove = document.createElement('button');
		remove.type = 'button';
		remove.className = 'icon-button question-remove';
		remove.setAttribute('aria-label', 'Remove question');
		remove.textContent = '×';
		remove.addEventListener('click', () => {
			if (list.children.length > 1) {
				card.remove();
				reindexQuestions();
			}
		});
		header.append(remove);
		card.append(header);

		const id = document.createElement('input');
		id.type = 'hidden';
		id.value = question.id || crypto.randomUUID();
		id.dataset.questionId = '';
		card.append(id);

		const fields = document.createElement('div');
		fields.className = 'question-fields';
		const labelWrap = document.createElement('label');
		labelWrap.className = 'field-label';
		labelWrap.append('Question');
		const label = document.createElement('input');
		label.type = 'text';
		label.value = question.label || '';
		label.placeholder = 'e.g. What should we know?';
		label.required = true;
		label.maxLength = 255;
		label.dataset.questionLabel = '';
		labelWrap.append(label);
		fields.append(labelWrap);

		const typeWrap = document.createElement('label');
		typeWrap.className = 'field-label type-field';
		typeWrap.append('Answer type');
		const select = document.createElement('select');
		select.dataset.questionType = '';
		questionTypes.forEach(([value, text]) => {
			const option = document.createElement('option');
			option.value = value;
			option.textContent = text;
			option.selected = (question.type || 'short') === value;
			select.append(option);
		});
		typeWrap.append(select);
		fields.append(typeWrap);

		const options = document.createElement('div');
		options.className = 'question-options';
		const optionsTitle = document.createElement('span');
		optionsTitle.className = 'field-caption';
		optionsTitle.textContent = 'Answer choices';
		options.append(optionsTitle);
		const optionList = document.createElement('div');
		optionList.className = 'option-list';
		(question.options?.length ? question.options : ['', '']).forEach((value) => addOption(optionList, value));
		options.append(optionList);
		const addChoice = document.createElement('button');
		addChoice.type = 'button';
		addChoice.className = 'text-button add-option';
		addChoice.textContent = '+ Add option';
		addChoice.addEventListener('click', () => addOption(optionList));
		options.append(addChoice);
		fields.append(options);

		const footer = document.createElement('div');
		footer.className = 'question-card-footer';
		const requiredLabel = document.createElement('label');
		requiredLabel.className = 'switch-label';
		const required = document.createElement('input');
		required.type = 'checkbox';
		required.value = '1';
		required.checked = Boolean(question.required);
		required.dataset.questionRequired = '';
		const switchMark = document.createElement('span');
		switchMark.className = 'switch-mark';
		requiredLabel.append(required, switchMark, 'Required');
		footer.append(requiredLabel);
		card.append(fields, footer);

		select.addEventListener('change', () => {
			options.hidden = !['choice', 'checkbox'].includes(select.value);
			reindexQuestions();
		});
		options.hidden = !['choice', 'checkbox'].includes(select.value);
		list.append(card);
		reindexQuestions();
	}

	(window.formBuilderQuestions?.length ? window.formBuilderQuestions : [{}]).forEach(addQuestion);
	builder.querySelector('[data-add-question]').addEventListener('click', () => addQuestion());
}

document.querySelectorAll('[data-copy-link]').forEach((button) => {
	button.addEventListener('click', async () => {
		await navigator.clipboard.writeText(button.dataset.copyLink);
		const originalText = button.textContent;
		button.textContent = 'Copied';
		window.setTimeout(() => { button.textContent = originalText; }, 1600);
	});
});
