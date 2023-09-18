<?php


namespace Transave\CommonBase\Tests\Feature\Kuda;


use Laravel\Sanctum\Sanctum;
use Transave\CommonBase\Actions\Kuda\Transfer\BankList;
use Transave\CommonBase\Actions\Kuda\Transfer\IntrabankFundTransfer;
use Transave\CommonBase\Actions\Kuda\Transfer\MainAccountFundTransfer;
use Transave\CommonBase\Actions\Kuda\Transfer\NameEnquiry;
use Transave\CommonBase\Actions\Kuda\Transfer\VirtualAccountFundTransfer;
use Transave\CommonBase\Http\Models\User;
use Transave\CommonBase\Tests\TestCase;

class KudaFundTransferTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $user = User::query()->find('e5951615-3f2c-4903-962f-d2cfdabd10cb');
        Sanctum::actingAs($user);
    }

    /** @test */
    function can_fetch_bank_list()
    {
        $response = (new BankList())->execute();
        $this->assertTrue($response['success']);
        $this->assertNotNull($response['data']['banks']);
    }

    /** @test */
    function can_get_beneficiary_account_details()
    {
        $inputs = [
            "beneficiary_account_number" => "2504199022",
            "beneficiary_bank_code" => "999129",
            "user_id" => "e5951615-3f2c-4903-962f-d2cfdabd10cb"
        ];
        $response = (new NameEnquiry($inputs))->execute();
        $this->assertTrue($response['success']);
        $this->assertNotNull($response['data']['sessionID']);
        $this->assertNotNull($response['data']['beneficiaryName']);
    }

    /** @test */
    function can_transfer_using_main_account()
    {
        $inputs = [
            "beneficiary_bank_code" => "999129",
            "beneficiary_account_number" => "2504199022",
            "beneficiary_name" => "(Slait)-Maduka Emmanuel",
            "amount" => "600",
            "narration" => "testing fund transfer",
            "name_enquiry_sessionID" => "NA",
            "client_fee_charge" => "5"
        ];
        $response = (new MainAccountFundTransfer($inputs))->execute();
        $this->assertTrue($response['success']);
        $this->assertNull($response['data']);
        $this->assertNotNull($response['message']);
    }

    /** @test */
    function can_transfer_using_virtual_account()
    {
        $inputs = [
            "beneficiary_bank_code" => "999129",
            "beneficiary_account_number" => "2504199022",
            "beneficiary_name" => "(Slait)-Maduka Emmanuel",
            "amount" => "600",
            "narration" => "testing fund transfer",
            "name_enquiry_sessionID" => "NA",
            "client_fee_charge" => "5",
            "user_id" => "c0329e2b-67d8-4c30-8bd9-51208c0b5975",
        ];
        $response = (new VirtualAccountFundTransfer($inputs))->execute();
        $this->assertTrue($response['success']);
        $this->assertNull($response['data']);
        $this->assertNotNull($response['message']);
    }

    /** @test */
    function can_transfer_wallet_to_wallet()
    {
        $input = [
            'beneficiary_user_id' => 'c0329e2b-67d8-4c30-8bd9-51208c0b5975',
            'amount' => "500",
        ];
        $response = (new IntrabankFundTransfer($input))->execute();
        dd($response);
        $this->assertTrue($response['success']);
        $this->assertNull($response['data']);
        $this->assertNotNull($response['message']);
    }

}