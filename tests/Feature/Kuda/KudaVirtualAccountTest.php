<?php


namespace Transave\CommonBase\Tests\Feature\Kuda;


use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Transave\CommonBase\Actions\Kuda\Account\CreateVirtualAccount;
use Transave\CommonBase\Actions\Kuda\Account\DisableVirtualAccount;
use Transave\CommonBase\Actions\Kuda\Account\EnableVirtualAccount;
use Transave\CommonBase\Actions\Kuda\Account\GetVirtualAccount;
use Transave\CommonBase\Actions\Kuda\Account\ListVirtualAccounts;
use Transave\CommonBase\Actions\Kuda\Account\MainAccountBalance;
use Transave\CommonBase\Actions\Kuda\Account\UpdateVirtualAccount;
use Transave\CommonBase\Actions\Kuda\Account\VirtualAccountBalance;
use Transave\CommonBase\Http\Models\Country;
use Transave\CommonBase\Http\Models\Lga;
use Transave\CommonBase\Http\Models\State;
use Transave\CommonBase\Tests\TestCase;

class KudaVirtualAccountTest extends TestCase
{
    private $create, $update, $faker;
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    /** @test */
//    public function can_create_virtual_account()
//    {
//        $this->createAccountData();
//        $response = (new CreateVirtualAccount($this->create))->execute();
//        $arrayData = json_decode($response->getContent(), true);
//        $this->assertEquals(true, $arrayData['success']);
//        $this->assertNull($arrayData['data']);
//    }

    /** @test */
    public function can_fetch_all_virtual_accounts()
    {
        $response = (new ListVirtualAccounts(['PageSize' => '3', 'PageNumber' => '1']))->execute();
        $this->assertTrue($response['success']);
        $this->assertNotNull($response['data']['accounts']);
    }

    /** @test */
    public function can_disable_virtual_account()
    {
        $response = (new DisableVirtualAccount(['user_id' => 'c0329e2b-67d8-4c30-8bd9-51208c0b5975']))->execute();
        $this->assertTrue($response['success']);
        $this->assertNotNull($response['data']['accountNumber']);
    }

    /** @test */
    public function can_enable_virtual_account()
    {
        $response = (new EnableVirtualAccount(['user_id' => 'c0329e2b-67d8-4c30-8bd9-51208c0b5975']))->execute();
        $this->assertTrue($response['success']);
        $this->assertNotNull($response['data']['accountNumber']);
    }

    /** @test */
    public function can_get_virtual_account()
    {
        $response = (new GetVirtualAccount(['user_id' => 'c0329e2b-67d8-4c30-8bd9-51208c0b5975']))->execute();
        $this->assertTrue($response['success']);
        $this->assertNotNull($response['data']['account']);
    }

    /** @test */
    public function can_fetch_main_account_balance()
    {
        $response = (new MainAccountBalance())->execute();
        $this->assertTrue($response['success']);
        $this->assertNotNull($response['data']);
    }

    /** @test */
    public function can_fetch_virtual_account_balance()
    {
        $response = (new VirtualAccountBalance(['user_id' => 'c0329e2b-67d8-4c30-8bd9-51208c0b5975']))->execute();

        $this->assertTrue($response['success']);
        $this->assertNotNull($response['data']);
    }

    /** @test */
    public function can_update_virtual_account()
    {
        $this->updateAccountData();
        $response = (new UpdateVirtualAccount($this->update))->execute();
        $response = json_decode($response->getContent(), true);
        $this->assertTrue($response['success']);
        $this->assertNotNull($response['data']);
    }

    private function createAccountData()
    {
        $this->create = [
            "email" => $this->faker->safeEmail,
            "phone" => '080'.(string)rand(30000000, 99999999),
            "last_name" => explode(' ', $this->faker->name, 2)[1],
            "first_name" => explode(' ', $this->faker->name, 2)[1],
            "password" => 'password',
            "password_confirmation" => "password"
        ];
    }

    private function updateAccountData()
    {
        $this->update = [
            'user_id' => "c0329e2b-67d8-4c30-8bd9-51208c0b5975",
            "phone" => '080'.(string)rand(30000000, 99999999),
//            "last_name" => explode(' ', $this->faker->name, 2)[1],
//            "first_name" => explode(' ', $this->faker->name, 2)[1],
//
//            'middle_name' => explode(' ', $this->faker->name, 2)[1],
//            'business_name' => $this->faker->name,
            'bvn' => rand(10000000000,99999999999),
            'image' => UploadedFile::fake()->image('photo.jpg'),
            'identity_card' => UploadedFile::fake()->image('identity-card.jpg'),
            'address_proof' => UploadedFile::fake()->image('address.jpg'),
            'identity_type' => $this->faker->randomElement(['NIN', 'Voter-Card', 'Passport', 'Driving-Licence']),
            'identity_card_number' => Str::random(16),
            'country_of_origin_id' => Country::factory()->create()->id,
            'country_of_residence_id' => Country::factory()->create()->id,
            'state_id' => State::factory()->create()->id,
            'lga_id' => Lga::factory()->create()->id,
            'city' => $this->faker->city,
            'next_of_kin' => $this->faker->name,
            'next_of_kin_contact' => '080'.(string)rand(30000000, 99999999),
            'mother_maiden_name' => $this->faker->name,

            'residential_status' => $this->faker->randomElement(['tenant', 'house-owner', 'family']),
            'employment_status' => $this->faker->randomElement(['part-time', 'full-time', 'contract', 'unemployed']),
            'employer' => $this->faker->company,
            'job_title' => $this->faker->sentence(2, 6),
            'educational_qualification' => $this->faker->sentence(2, 6),
            'date_of_employment' => Carbon::now()->subYears(20),
            'number_of_children' => rand(1, 6),
            'income_range' => json_encode(['min' => rand(10000, 100000), 'max' => rand(200000, 400000)]),
            'verification_status' => $this->faker->randomElement(['verified', 'incomplete', 'unverified']),
            'is_loan_compliant' => $this->faker->randomElement(['no', 'yes'])
        ];
    }
}