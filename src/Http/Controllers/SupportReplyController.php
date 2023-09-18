<?php


namespace Transave\CommonBase\Http\Controllers;


use Illuminate\Http\Request;
use Transave\CommonBase\Actions\SupportReply\CreateSupportReply;
use Transave\CommonBase\Actions\SupportReply\DeleteSupportReply;
use Transave\CommonBase\Actions\SupportReply\SearchSupportReply;
use Transave\CommonBase\Actions\SupportReply\UpdateSupportReply;
use Transave\CommonBase\Http\Models\SupportReply;

class SupportReplyController extends Controller
{
    /**
     * SupportReplyController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['index', 'show', 'delete']);
    }

    /**
     * Get a listing of support replies
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        return (new SearchSupportReply(SupportReply::class, ['user', 'support']))->execute();
    }

    /**
     * Get a specified support reply
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show($id)
    {
        return (new SearchSupportReply(SupportReply::class, ['user', 'support'], $id))->execute();
    }

    /**
     * Create a new support reply
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|CreateSupportReply
     */
    public function store(Request $request)
    {
        return (new CreateSupportReply($request->all()))->execute();
    }

    /**
     * Update a specified support reply
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|UpdateSupportReply
     */
    public function update(Request $request, $id)
    {
        $data = array_merge($request->all(), ['support_reply_id'=>$id]);
        return (new UpdateSupportReply($data))->execute();
    }

    /**
     * Delete a specified support reply from storage
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|DeleteSupportReply
     */
    public function destroy($id)
    {
        return (new DeleteSupportReply(['support_reply_id' => $id]))->execute();
    }
}