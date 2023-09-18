<?php


namespace Transave\CommonBase\Http\Controllers;


use Illuminate\Http\Request;
use Transave\CommonBase\Actions\Transaction\CreateTransaction;
use Transave\CommonBase\Actions\Transaction\SearchTransaction;
use Transave\CommonBase\Actions\Transaction\UpdateTransaction;
use Transave\CommonBase\Http\Models\Transaction;

class TransactionController extends Controller
{
    /**
     * TransactionController constructor.
     */
    public function __construct()
    {
        $this->middleware('admin')->except(['show']);
    }

    /**
     * Get a listing of transactions
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        return (new SearchTransaction(Transaction::class, ['user']))->execute();
    }

    /**
     * Get a specified transaction
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show($id)
    {
        return (new SearchTransaction(Transaction::class, ['user'], $id))->execute();
    }

    /**
     * Store a newly created transaction
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Transave\CommonBase\Actions\Action
     */
    public function store(Request $request)
    {
        return (new CreateTransaction($request->all()))->execute();
    }

    /**
     * Update a specified transaction
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|UpdateTransaction
     */
    public function update(Request $request, $id)
    {
        return (new UpdateTransaction(array_merge($request->all(),['transaction_id'=>$id])))->execute();
    }
}