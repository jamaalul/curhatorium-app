@extends('layouts.dashboard')

@section('title', $chapter->title)

@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('dashboard-content')
    <main class="mx-auto px-4 py-10 max-w-3xl">
        <a href="{{ route('e-class.library') }}" class="text-primary-600">← Library E-Class</a>
        <p class="mt-6 font-medium text-zinc-500">{{ $module->title }}</p>
        <h1 class="mt-1 font-bricolage font-semibold text-zinc-900 text-4xl">{{ $chapter->title }}</h1>

        @if (! $attempt)
            <form action="{{ route('e-class.quiz-attempts.start', [$module, $chapter]) }}" method="POST" class="bg-white mt-8 p-6 border border-zinc-200 rounded-2xl">
                @csrf
                <p class="text-zinc-600">Quiz ini terdiri dari {{ $questions->count() }} pertanyaan.</p>
                <button class="bg-primary-500 mt-5 px-5 py-3 rounded-xl font-medium text-white">Mulai attempt</button>
            </form>
        @else
            <form action="{{ route('e-class.quiz-attempts.submit', $attempt) }}" method="POST" class="space-y-5 mt-8">
                @csrf
                @foreach ($questions as $question)
                    <fieldset class="bg-white p-6 border border-zinc-200 rounded-2xl">
                        <input type="hidden" name="answers[{{ $loop->index }}][question_id]" value="{{ $question->id }}">
                        <legend class="font-semibold text-zinc-900">{{ $loop->iteration }}. {{ $question->question }}</legend>
                        @if ($question->type === \App\QuizQuestionType::MultipleChoice)
                            <div class="space-y-3 mt-4">
                                @foreach ($question->options as $option)
                                    <label class="flex items-start gap-3">
                                        <input type="radio" name="answers[{{ $loop->parent->index }}][selected_option_id]" value="{{ $option->id }}" required>
                                        <span>{{ $option->option_text }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <input type="text" name="answers[{{ $loop->index }}][answer_text]" required maxlength="5000" class="mt-4 px-4 py-3 border border-zinc-300 rounded-xl w-full">
                        @endif
                    </fieldset>
                @endforeach
                <button class="bg-primary-500 px-5 py-3 rounded-xl font-medium text-white">Kirim jawaban</button>
            </form>
        @endif
    </main>
@endsection
