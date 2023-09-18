<?php


namespace Transave\CommonBase\Http\Controllers;


use Illuminate\Http\Request;
use Transave\CommonBase\Actions\Support\DeleteSupport;
use Transave\CommonBase\Actions\Support\CreateSupport;
use Transave\CommonBase\Actions\Support\SearchSupport;
use Transave\CommonBase\Actions\Support\UpdateSupport;
use Transave\CommonBase\Http\Models\Support;

class SupportController extends Controller
{
    /**
     * SupportController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['index', 'show', 'delete']);
    }

    /**
     * Get a listing of support tickets
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        return (new SearchSupport(Support::class, ['user']))->execute();
    }

    /**
     * Get a specified support ticket
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show($id)
    {
        return (new SearchSupport(Support::class, ['user'], $id))->execute();
    }

    /**
     * Store a newly created support ticket
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|CreateSupport
     */
    public function store(Request $request)
    {
        return (new CreateSupport($request->all()))->execute();
    }

    /**
     * Update a specified support ticket
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|UpdateSupport
     */
    public function update(Request $request, $id)
    {
        $data = array_merge($request->all(), ['support_id' => $id]);
        return (new UpdateSupport($data))->execute();
    }

    /**
     * Delete a specified support ticket
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|DeleteSupport
     */
    public function destroy($id)
    {
        return (new DeleteSupport(['support_id' => $id]))->execute();
    }
}