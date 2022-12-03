<?php

namespace App\Http\Controllers\Api\App\Poll;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\VoteResource;
use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function voteQuestion(User $user, Answer $answer, Question $question)
    {
        $vote = Vote::query()->firstOrCreate([
            'user_id' => $user->id,
            'question_id' => $question->id
        ],[
            'user_id' => $user->id,
            'answer_id' => $answer->id,
            'question_id' => $question->id,
        ]);
        return new VoteResource($vote);
    }
}
