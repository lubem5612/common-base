<?php


namespace Transave\CommonBase\Http\Controllers;


use Illuminate\Http\Request;
use Transave\CommonBase\Actions\DebitCard\CreateDebitCard;
use Transave\CommonBase\Actions\DebitCard\DeleteDebitCard;
use Transave\CommonBase\Actions\DebitCard\SearchDebitCard;
use Transave\CommonBase\Actions\DebitCard\UpdateDebitCard;
use Transave\CommonBase\Http\Models\DebitCard;

class DebitCardController extends Controller
{
    /**
     * DebitCardController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['index', 'show', 'delete']);
    }

    /**
     * Get a listing of debit cards
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        return (new SearchDebitCard(DebitCard::class, ['user']))->execute();
    }

    /**
     * Show a specified debit card
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show($id)
    {
        return (new SearchDebitCard(DebitCard::class, ['user'], $id))->execute();
    }

    /**
     * Store a newly created debit card
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|CreateDebitCard
     */
    public function store(Request $request)
    {
        return (new CreateDebitCard($request->all()))->execute();
    }

    /**
     * Update a specified debit card
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|UpdateDebitCard
     */
    public function update(Request $request, $id)
    {
        $data = array_merge($request->all(), ['debit_card_id'=>$id]);
        return (new UpdateDebitCard($data))->execute();
    }

    /**
     * Delete specified debit card from storage
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|DeleteDebitCard
     */
    public function destroy($id)
    {
        return (new DeleteDebitCard(['debit_card_id' => $id]))->execute();
    }
}