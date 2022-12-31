<?php

namespace App\Http\Controllers\Api\App\Poll;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AnswerResource;
use App\Models\Answer;
use App\Models\Company;
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

    public function answer(User $user, Question $question, Company $company)
    {
        try
        {
            $answer = Answer::query()->create([
                'user_id' => $user->id,
                'question_id' => $question->id,
                'company_id' => $company->id
            ]);
            return new AnswerResource($answer);
        } catch (\Exception $exception)
        {
            return response(['error' => $exception->getMessage()], 400);
        }
    }
}
