<?php


namespace Transave\CommonBase\Actions\SupportReply;


use Illuminate\Database\Eloquent\Builder;
use Transave\CommonBase\Helpers\SearchHelper;

class SearchSupportReply
{
    use SearchHelper;

    public function searchTerms()
    {
        $search = $this->searchParam;
        $user = request()->query('user_id');
        if (isset($user)) {
            $this->queryBuilder->where('user_id', $user);
        }
        $this->queryBuilder->where(function(Builder $query) use($search) {
            $query->where('content', "like", "%$search%")
                ->orWhereHas('user', function (Builder $builder) use ($search) {
                    $builder->where('first_name', "%$search%")
                        ->orWhere('last_name', "%$search%")
                        ->orWhere('email', "%$search%")
                        ->orWhere('phone', "%$search%");
                })
                ->orWhereHas('support', function (Builder $builder2) use ($search) {
                    $builder2->where('title', "like", "%$search%")
                        ->orWhere('content', "like", "%$search%")
                        ->orWhere('type', "like", "%$search%")
                        ->orWhere('status', "like", "%$search%");
                });
        });
        return $this;
    }
}