<?php


namespace Transave\CommonBase\Actions\Resource;


use Transave\CommonBase\Actions\Action;

class GetResource extends Action
{
    private $request;
    private $validatedInput;
    private $routeConfig = [];
    private $route, $model;
    private $resource, $relationships;
    private $queryBuilder;

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
                ->setModelRelationship()
                ->getResource()
                ->sendSuccess($this->resource, 'resource retrieved successfully');
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function setModel()
    {
        abort_if(!array_key_exists('model', $this->route), 401, 'model not configured');
        $this->model = $this->route['model'];
        $this->queryBuilder = $this->model::query();
        return $this;
    }

    private function setModelRelationship()
    {
        if (array_key_exists('relationships', $this->route) && count($this->route['relationships']) > 0) {
            $this->relationships = $this->route['relationships'];
            $this->queryBuilder = $this->queryBuilder->with($this->relationships);
        }
        return $this;
    }

    private function getResource()
    {
        $this->resource = $this->queryBuilder->find($this->validatedInput['id']);
        return $this;
    }

    private function validateRequest()
    {
        abort_if(!array_key_exists($this->request['endpoint'], $this->routeConfig), 401, 'endpoint not found');
        $this->route = $this->routeConfig[$this->request['endpoint']];

        $table = $this->route['table'];
        $this->validatedInput = $this->validate($this->request, [
            'id' => ["required", "exists:$table,id"],
            "endpoint" => ["string", "required"],
        ]);
        return $this;
    }
}