<?php

namespace App\Http\Controllers\Api\Admin\Poll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Poll\QuestionRequest;
use App\Http\Resources\Admin\QuestionResource;
use App\Models\Question;
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
     *      path="/api/admin/polls/store",
     *      operationId="store new poll",
     *      tags={"polls"},
     *      summary="store new poll",
     *      description="store new poll",
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
    public function store(QuestionRequest $request)
    {
        try {
            DB::beginTransaction();
            $question = Question::create($request->validated());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response('error', 400);
        }
        return new QuestionResource($question);
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
            DB::beginTransaction();
            $question->update($request->validated());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response('error', 400);
        }
        return new QuestionResource($question);
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
            DB::beginTransaction();
            $question->forceDelete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response('error', 400);
        }
        return response('question deleted', 200);
    }
}