<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Question;
use App\Answer;

class AnswersController extends Controller
{
    public function store(Request $request)
    {
        $question = Question::findOrFail($request->input('question_id'));
        $this->authorize('view-course', $question->course);

        $this->validate($request, [
            'body' => 'required',
        ]);

        $answer = new Answer($request->only(['body']));
        $answer->question()->associate($question);
        $answer->user()->associate(auth()->user());
        $answer->save();

        session()->flash('success', 'La respuesta ha sido enviada.');
        return redirect()->back();
    }

    public function edit(Request $request, int $id)
    {
        $answer = Answer::findOrFail($id);
        $this->authorize('edit-answer', $answer);

        return view('questions.show', [
            'question' => $answer->question,
            'answerToEdit' => $answer,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $answer = Answer::findOrFail($id);
        $this->authorize('edit-answer', $answer);

        $this->validate($request, [
            'body' => 'required',
        ]);

        $answer->body = $request->get('body');
        $answer->save();

        session()->flash('success', 'La respuesta ha sido editada.');
        return redirect()->route('question.show', $answer->question->id);
    }
}
