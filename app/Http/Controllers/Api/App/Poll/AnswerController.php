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
    /**
     * @OA\Get (
     *      path="/api/app/polls/answers",
     *      operationId="get answers",
     *      tags={"polls"},
     *      summary="get answers",
     *      description="get answers",
     *      security={ {"sanctum": {} }},
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
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/api/app/polls/answers")
     *       ),
     *     @OA\Response(
     *          response=401,
     *          description="validation error",
     *      ),
     *     @OA\Response(
     *          response=422,
     *          description="error",
     *       ),
     *     @OA\Response(
     *          response=500,
     *          description="server error",
     *      ),
     * )
     */
    public function index()
    {
        $answers = Answer::latest()->paginate(15);
        return AnswerResource::collection($answers);
    }

    /**
     * @OA\Post (
     *      path="/api/app/polls/answers/users/{user}/questions/{question}/companies/{company}",
     *      operationId="store new answer",
     *      tags={"polls"},
     *      summary="store new answer",
     *      description="store new answer",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="user",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *          @OA\Parameter(
     *          name="question",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *          @OA\Parameter(
     *          name="company",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
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
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="title", type="text", format="text", example="text"),
     *                   required={"title"},
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/api/app/polls/answers/users/{user}/questions/{question}/companies/{company}")
     *       ),
     *     @OA\Response(
     *          response=401,
     *          description="validation error",
     *      ),
     *     @OA\Response(
     *          response=422,
     *          description="error",
     *       ),
     *     @OA\Response(
     *          response=500,
     *          description="server error",
     *      ),
     * )
     */
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
