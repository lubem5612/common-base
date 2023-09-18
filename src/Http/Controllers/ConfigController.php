<?php


namespace Transave\CommonBase\Http\Controllers;


use Transave\CommonBase\Helpers\ResponseHelper;

class ConfigController extends Controller
{
    use ResponseHelper;
    /**
     * @var array
     */
    private $mapping = [
        'transaction-categories' => 'transaction_category',
        'transaction-status' => 'transaction_status',
        'identity-status' => 'identity_type',
        'residential-status' => 'residential_status',
        'employment-status' => 'employment_status',
        'educational-qualifications' => 'educational_qualification',
    ];

    /**
     * @param $endpoint
     * @return \Illuminate\Http\Response
     */
    public function index($endpoint)
    {
        $array = config("commonbase.{$this->mapping[$endpoint]}");
        return $this->sendSuccess($array, 'resource retrieved successfully');
    }

}