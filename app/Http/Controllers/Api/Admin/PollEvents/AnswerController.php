<?php

namespace App\Http\Controllers\Api\Admin\PollEvents;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AnswerResource;
use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnswerController extends Controller
{
    public function index()
    {
        $answers = Answer::latest()->paginate(15);
        return AnswerResource::collection($answers);
    }

    public function answer(User $user, Question $question)
    {
        try
        {
            $answer = Answer::query()->create([
                'user_id' => $user->id,
                'question_id' => $question->id
            ]);
            return new AnswerResource($answer);
        } catch (\Exception $exception)
        {
            return response(['error' => $exception->getMessage()], 400);
        }
    }
}
