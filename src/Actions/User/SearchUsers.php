<?php


namespace Transave\CommonBase\Actions\User;


use Illuminate\Database\Eloquent\Builder;
use Transave\CommonBase\Helpers\SearchHelper;

class SearchUsers
{
    use SearchHelper;

    public function searchTerms(): SearchHelper
    {
        $userType = request()->query('role');
        $account = request()->query('account_number');
        if (isset($userType)) {
            if ($userType == 'admin' && auth()->check()) {
                $this->queryBuilder->where('id', '!=', auth()->id());
            }
            $this->queryBuilder->where('role', $userType);
        }

        if (isset($account)) {
            $this->queryBuilder->where('account_number', $account);
        }

        $search = $this->searchParam;
        $this->queryBuilder->where(function(Builder $query) use($search) {
            $query->where('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%")
                ->orWhere('middle_name', 'like', "%$search%")
                ->orWhere('business_name', 'like', "%$search%")
                ->orWhere('is_verified', 'like', "%$search%")
                ->orWhere('account_type', 'like', "%$search%")
                ->orWhere('account_status', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%")
                ->orWhereHas('kyc', function (Builder $query2) use($search) {
                    $query2->where('identity_type', 'like', "%$search%")
                        ->orWhere('identity_card_number', 'like', "%$search%")
                        ->orWhere('city', 'like', "%$search%")
                        ->orWhere('next_of_kin', 'like', "%$search%")
                        ->orWhere('next_of_kin_contact', 'like', "%$search%")
                        ->orWhere('mother_maiden_name', 'like', "%$search%")
                        ->orWhere('residential_status', 'like', "%$search%")
                        ->orWhere('employment_status', 'like', "%$search%")
                        ->orWhere('employer', 'like', "%$search%")
                        ->orWhere('job_title', 'like', "%$search%")
                        ->orWhere('educational_qualification', 'like', "%$search%")
                        ->orWhere('income_range', 'like', "%$search%")
                        ->orWhere('verification_status', 'like', "%$search%")
                        ->orWhere('is_loan_compliant', 'like', "%$search%")
                        ->orWhereHas('state', function (Builder $query2) use($search) {
                            $query2->where('name', 'like', "%$search%")
                                ->orWhere('capital', 'like', "%$search%");
                        })
                        ->orWhereHas('lga', function (Builder $query3) use($search) {
                            $query3->where('name', 'like', "%$search%");
                        })
                        ->orWhereHas('origin', function (Builder $query4) use($search) {
                            $query4->where('name', 'like', "%$search%")
                                ->orWhere('code', 'like', "%$search%")
                                ->orWhere('continent', 'like', "%$search%");
                        })
                        ->orWhereHas('residence', function (Builder $query5) use($search) {
                            $query5->where('name', 'like', "%$search%")
                                ->orWhere('code', 'like', "%$search%")
                                ->orWhere('continent', 'like', "%$search%");
                        });
                });
        });

        return $this;
    }

}