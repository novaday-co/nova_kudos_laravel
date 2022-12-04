<?php

namespace App\Http\Controllers\Api\App\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AnswerResource;
use App\Http\Resources\Admin\EventResource;
use App\Http\Resources\Admin\ProductResource;
use App\Http\Resources\Admin\QuestionResource;
use App\Models\Product;
use App\Models\Question;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * @OA\Get (
     *      path="/questions/users/{user}",
     *      operationId="get all questions for user",
     *      tags={"Home"},
     *      summary="get all questions for user",
     *      description="get all questions for user",
     *      security={{ "sanctum": {}},},
     *      @OA\Parameter(
     *          name="locale",
     *          in="header",
     *          required=true,
     *          example="fa",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="Accept",
     *          in="header",
     *          required=true,
     *          example="application/json",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="Content-Type",
     *          in="header",
     *          required=true,
     *          example="application/json",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="user",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/companies"),
     *       ),
     *     @OA\Response(
     *          response=422,
     *          description="error",
     *       ),
     *     @OA\Response(
     *          response=401,
     *          description="not authenticate",
     *       ),
     * )
     */
    public function questions(User $user)
    {
        try {
            $questions = $user->questionTypes()->get();
            return QuestionResource::collection($questions);
        } catch (\Exception $e)
        {
            return response(['bad request' => $e->getMessage()], 400);
        }
    }

    public function answerQuestions(Question $question)
    {
        try {
            $answers = $question->answers()->get();
            return AnswerResource::collection($answers);
        } catch (\Exception $e)
        {
            return response(['bad request' => $e->getMessage()], 400);
        }
    }

    public function countOfVotes(Question $question)
    {
        try
        {
            $votes = $question->votes()->get();
            $count = count($votes);
            return $count;
        } catch (\Exception $e)
        {
            return response(['bad request' => $e->getMessage()], 400);
        }
    }

    public function voteUser(Question $question, User $user)
    {

    }

    public function events(User $user)
    {
        try {
            $events = $user->eventTypes()->get();
            return EventResource::collection($events);
        } catch (\Exception $exception)
        {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    public function products()
    {
        try
        {
            $products = Product::query()->get();
            return ProductResource::collection($products);
        } catch (\Exception $exception)
        {
            return response(['error' => $exception->getMessage()], 400);
        }
    }
}
