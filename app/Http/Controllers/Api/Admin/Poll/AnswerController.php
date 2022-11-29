<?php

namespace App\Http\Controllers\Api\Admin\Poll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Poll\AnswerRequest;
use App\Http\Resources\Admin\AnswerResource;
use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;
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
            DB::beginTransaction();
            // insert answer
            $answer = new Answer();
            $answer->user_id = $user->id;
            $answer->question_id = $question->id;
            $answer->save();
            DB::commit();
        } catch (\Exception $exception)
        {
            return response(['errro' => $exception->getMessage()], 400);
        }
        return new AnswerResource($answer);
    }
}
