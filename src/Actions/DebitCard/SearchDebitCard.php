<?php


namespace Transave\CommonBase\Actions\DebitCard;


use Illuminate\Database\Query\Builder;
use Transave\CommonBase\Helpers\SearchHelper;

class SearchDebitCard
{
    use SearchHelper;

    public function searchTerms(): SearchHelper
    {
        $search = $this->searchParam;
        $user = request()->query('user_id');
        if (isset($user)) {
            $this->queryBuilder->where('user_id', $user);
        }
        $this->queryBuilder->where(function (Builder $builder) use($search) {
            $builder->where('first_digits', 'like', "%$search%")
                ->orWhere('last_digits','like', "%$search%")
                ->orWhere('issuer','like', "%$search%")
                ->orWhere('email','like', "%$search%")
                ->orWhere('type','like', "%$search%")
                ->orWhere('country','like', "%$search%")
                ->orWhere('currency','like', "%$search%")
                ->orWhere('expiry','like', "%$search%")
                ->orWhere('is_third_party','like', "%$search%")
                ->orWhereHas('user', function (Builder $builder2) use($search) {
                    $builder2->where('first_name', "%$search%")
                        ->orWhere('last_name', "%$search%")
                        ->orWhere('email', "%$search%")
                        ->orWhere('phone', "%$search%");
                });
        });
    }
}