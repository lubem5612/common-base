<?php


namespace Transave\CommonBase\Actions\Transaction;


use Illuminate\Database\Query\Builder;
use Transave\CommonBase\Helpers\SearchHelper;

class SearchTransaction
{
    use SearchHelper;

    public function searchTerms(): SearchHelper
    {
        $search = $this->searchParam;
        $this->queryBuilder->where(function (Builder $builder) use($search){
            $builder->where('reference', 'like', "%$search%")
                ->orWhere('amount', 'like', "%$search%")
                ->orWhere('charges', 'like', "%$search%")
                ->orWhere('commission', 'like', "%$search%")
                ->orWhere('type', 'like', "%$search%")
                ->orWhere('category', 'like', "%$search%")
                ->orWhere('status', 'like', "%$search%")
                ->orWhereHas('user', function (Builder $builder2) use($search) {
                    $builder2->where('first_name', "%$search%")
                        ->orWhere('last_name', "%$search%")
                        ->orWhere('email', "%$search%")
                        ->orWhere('phone', "%$search%");
                });
        });
    }
}