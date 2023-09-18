<?php

namespace Transave\CommonBase\Actions\Support;


use Illuminate\Database\Query\Builder;
use Transave\CommonBase\Helpers\SearchHelper;

class SearchSupport
{
    use SearchHelper;

    public function searchTerms() : SearchHelper
    {
        $search = $this->searchParam;
        $user = request()->query('user_id');
        if (isset($user)) {
            $this->queryBuilder->where('user_id', $user);
        }
        $this->queryBuilder->where(function(Builder $query) use($search) {
            $query->where('title', "like", "%$search%")
                ->orWhere('content', "like", "%$search%")
                ->orWhere('type', "like", "%$search%")
                ->orWhere('status', "like", "%$search%")
                ->orWhereHas('user', function (Builder $builder) use ($search) {
                    $builder->where('first_name', "%$search%")
                        ->orWhere('last_name', "%$search%")
                        ->orWhere('email', "%$search%")
                        ->orWhere('phone', "%$search%");
                });
        });
        return $this;
    }
}