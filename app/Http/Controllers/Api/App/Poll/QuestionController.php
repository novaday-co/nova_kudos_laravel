<?php

namespace App\Http\Controllers\Api\App\Poll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Poll\QuestionRequest;
use App\Http\Resources\Admin\QuestionResource;
use App\Models\Group;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * @OA\Get (
     *      path="/api/admin/polls/all",
     *      operationId="get all questions",
     *      tags={"polls"},
     *      summary="get all questions",
     *      description="get all questions",
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
        $questions = Question::latest()->paginate(15);
        return QuestionResource::collection($questions);
    }

    /**
     * @OA\Post (
     *      path="/api/admin/polls/group/{group}/user/{user}/store",
     *      operationId="store new poll",
     *      tags={"polls"},
     *      summary="store new poll",
     *      description="store new poll",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="id",
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
    public function store(QuestionRequest $request, User $user)
    {
        try
        {
            $attrs = $request->validated();
            $question = Question::query()->create([
                'title' => $attrs['title'],
                'user_id' => $user->id,
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

    /**
     * @OA\Put(
     *      path="/api/admin/polls/update/{question}",
     *      operationId="update question",
     *      tags={"polls"},
     *      summary="update question",
     *      description="update question",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="id",
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
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"title"},
     *                  @OA\Property(property="title", type="text", format="text", example="yasin"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *       ),
     *     @OA\Response(
     *          response=401,
     *          description="validation error",
     *      ),
     *     @OA\Response(
     *          response=400,
     *          description="error",
     *       ),
     *     @OA\Response(
     *          response=500,
     *          description="server error",
     *      ),
     * )
     */
    public function update(QuestionRequest $request, Question $question)
    {
        try {
            $question->update($request->validated());
            return new QuestionResource($question);
        } catch (\Exception $e) {
            return response('error', 400);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/admin/polls/delete/{question}",
     *      operationId="delete question",
     *      tags={"polls"},
     *      summary="delete question",
     *      description="delete question",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="id",
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
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *       ),
     *     @OA\Response(
     *          response=500,
     *          description="server error",
     *      ),
     * )
     */
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
