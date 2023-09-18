<?php


namespace Transave\CommonBase\Helpers;


use Illuminate\Support\Facades\Log;
use Transave\CommonBase\Http\Models\Kyc;
use Transave\CommonBase\Http\Models\Support;
use Transave\CommonBase\Http\Models\User;

class KycHelper
{
    use ValidationHelper;
    private User $user;
    private Kyc $kyc;
    private $request, $validatedData;
    private $k_data = [], $k_success = false, $k_message = "", $missing_fields = [], $percentage_completion = 0;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            $this
                ->validateRequest()
                ->setUserAndKyc()
                ->updateIfClassicCompliant()
                ->updateIfPremiumCompliant()
                ->updateIfLoanCompliant()
                ->getPercentage();
            $this->k_success = true;
        }catch (\Exception $e) {
            Log::error($e);
            $this->k_message = $e->getMessage();
            $this->k_data = [];
        }
        return $this->processResponse();
    }

    private function updateIfClassicCompliant()
    {
        if ($this->isClassicCompliant() && $this->user->is_verified == 'yes') {
            if ($this->user->account_type == 'ordinary') {
                Support::query()->updateOrCreate([
                    'user_id' => $this->user->id,
                    'title' => "Kindly update my account {$this->user->account_number} to classic",
                    'type' => "ACCOUNT_UPGRADE",
                    'status' => "opened",
                ]);
                $this->k_message = 'classic account request sent successfully';
            }
        }
        return $this;
    }

    private function updateIfPremiumCompliant()
    {
        if ($this->isPremiumCompliant() && $this->user->is_verified == 'yes') {
            if ($this->user->account_type == 'classic') {
                $this->user->update([
                    'withdrawal_limit' => config('commonbase.withdrawal_limits.premium', 150000),
                    'account_type' => 'premium'
                ]);
                $this->kyc->update([
                    'verification_status' => 'verified'
                ]);
                $this->k_message = 'updated to premium account successfully';
            }
        }
        return $this;
    }

    private function updateIfLoanCompliant()
    {
        if ($this->isLoanCompliant() && $this->user->is_verified == 'yes') {
            if ($this->kyc->is_loan_compliant != 'yes') {
                $this->kyc->update([
                    'is_loan_compliant' => 'yes'
                ]);
                $this->k_message = 'updated for loan application successfully';
            }
        }
        return $this;
    }


    private function isClassicCompliant()
    {
        return Kyc::query()
            ->where('user_id', $this->user->id)
            ->whereNotNull('image_url')
            ->whereNotNull('identity_card_url')
            ->whereNotNull('address_proof_url')
            ->whereNotNull('identity_type')
            ->whereNotNull('identity_card_number')->exists();
    }

    private function isPremiumCompliant()
    {
        return Kyc::query()
            ->where('user_id', $this->user->id)
            ->whereNotNull('country_of_origin_id')
            ->whereNotNull('country_of_residence_id')
            ->whereNotNull('state_id')
            ->whereNotNull('lga_id')
            ->whereNotNull('city')
            ->whereNotNull('next_of_kin')
            ->whereNotNull('next_of_kin_contact')
            ->whereNotNull('mother_maiden_name')
            ->exists();
    }

    private function isLoanCompliant()
    {
        return Kyc::query()
            ->where('user_id', $this->user->id)
            ->whereNotNull('residential_status')
            ->whereNotNull('employment_status')
            ->whereNotNull('employer')
            ->whereNotNull('job_title')
            ->whereNotNull('educational_qualification')
            ->whereNotNull('income_range')
            ->exists();
    }

    private function setUserAndKyc()
    {
        $this->user = User::query()->find($this->validatedData['user_id']);
        $this->kyc = $this->user->kyc;
        return $this;
    }

    private function getPercentage()
    {
        $this->kyc = $this->kyc->refresh();
        $total = 0;
        $filled = 0;
        $nullables = ['verification_status', 'date_of_employment', 'number_of_children', 'is_loan_compliant', 'user_id'];
        // using $kyc->getFillable() for return all fields from the model
        foreach ($this->kyc->getFillable() as $key => $row) {
            //exclude data were kyc not compulsory
            if (!in_array($row, $nullables)) {
                $total += 1;
                if ($this->kyc->$row != null) $filled++;
                else array_push($this->missing_fields, $this->kyc->$row);
            }
        }
        //calculate percentage
        $this->percentage_completion = $filled / $total * 100;
        return $this;
    }

    private function processResponse()
    {
        return [
            'success' => $this->k_success,
            'message' => $this->k_message,
            'data' => [
                'withdrawal_limit' => $this->user->refresh()->withdrawal_limit,
                'account_type' => $this->user->refresh()->account_type,
                'percentage_completion' => $this->percentage_completion,
                'missing_fields' => $this->missing_fields,
                'account' => $this->kyc
            ],
        ];
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "user_id" => "required|exists:users,id",
        ]);

        return $this;
    }

}