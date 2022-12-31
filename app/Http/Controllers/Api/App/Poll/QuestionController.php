<?php

namespace App\Http\Controllers\Api\App\Poll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Poll\QuestionRequest;
use App\Http\Resources\Admin\QuestionResource;
use App\Models\Company;
use App\Models\Group;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::query()->latest()->paginate(15);
        return QuestionResource::collection($questions);
    }

    public function store(QuestionRequest $request, Company $company)
    {
        try
        {
            $attrs = $request->validated();
            $question = Question::query()->create([
                'title' => $attrs['title'],
                'company_id' => $company->id,
            ]);
            return new QuestionResource($question);
        } catch (\Exception $exception) {
            return response(['error:' => $exception->getMessage()], 400);
        }

    }

    public function userType(Question $question, User $user)
    {
        try
        {
            $user->questionTypes()->attach($question);
            return response('added', 200);
        } catch (\Exception $exception)
        {
            return response(['error:' => $exception->getMessage()], 400);
        }

    }

    public function groupType(Question $question, Group $group)
    {
        try
        {
            $group->questionTypes()->attach($question);
            return response('added', 200);
        } catch (\Exception $exception)
        {
            return response(['error:' => $exception->getMessage()], 400);
        }

    }

    public function update(QuestionRequest $request, Question $question)
    {
        try {
            auth()->user();
            $question->update($request->validated());
            return new QuestionResource($question);
        } catch (\Exception $e) {
            return response('error', 400);
        }
    }

    public function destroy(Question $question)
    {
        try {
            $question->forceDelete();
            return response('question deleted', 200);
        } catch (\Exception $e) {
            return response('error', 400);
        }
    }
}
