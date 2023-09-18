<?php


namespace Transave\CommonBase\Actions\Resource;


use Transave\CommonBase\Actions\Action;

class CreateResource extends Action
{
    private $request;
    private $validatedInput;
    private $routeConfig = [];
    private $route;
    private $resource;
    private $model;

    public function __construct(array $request)
    {
        $this->request = $request;
        $this->routeConfig = config('commonbase.endpoints.routes');
    }

    public function execute()
    {
        try {
            return $this
                ->validateRequest()
                ->setModel()
                ->createResource();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function setModel()
    {
        abort_if(!array_key_exists('model', $this->route), 401, 'model not configured');
        $this->model = $this->route['model'];
        return $this;
    }

    private function createResource()
    {
        $this->resource = $this->model::create($this->validatedInput);
        return $this->sendSuccess($this->resource, 'resource created successfully');
    }

    private function validateRequest()
    {
        abort_if(!array_key_exists($this->request['endpoint'], $this->routeConfig), 401, 'endpoint not found');
        $this->route = $this->routeConfig[$this->request['endpoint']];

        abort_if(!array_key_exists('rules', $this->route), 401, 'rules not found');
        abort_if(!array_key_exists('store', $this->route['rules']), 401, 'validation rules not configured');

        $this->validatedInput = $this->validate($this->request['data'], $this->route['rules']['store']);
        return $this;
    }
}