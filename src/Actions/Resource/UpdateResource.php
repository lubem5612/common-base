<?php


namespace Transave\CommonBase\Actions\Resource;


use Transave\CommonBase\Actions\Action;

class UpdateResource extends Action
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
                ->updateResource();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function setModel()
    {
        abort_if(!array_key_exists('model', $this->route), 401, 'model not configured');
        $this->model = $this->route['model'];
        $this->resource = $this->model::query()->find($this->validatedInput['id']);
        return $this;
    }

    private function updateResource()
    {
        $this->resource->fill($this->validatedInput)->save();
        return $this->sendSuccess($this->resource->refresh(), 'resource updated successfully');
    }

    private function validateRequest()
    {
        abort_if(!array_key_exists($this->request['endpoint'], $this->routeConfig), 401, 'endpoint not found');
        $this->route = $this->routeConfig[$this->request['endpoint']];

        abort_if(!array_key_exists('rules', $this->route), 401, 'rules not found');
        abort_if(!array_key_exists('update', $this->route['rules']), 401, 'validation rules not configured');

        $table = $this->route['table'];
        $rules = array_merge($this->route['rules']['update'], ['id' => ["required", "exists:$table,id"]]);
        $this->validatedInput = $this->validate($this->request['data'], $rules);
        return $this;
    }
}