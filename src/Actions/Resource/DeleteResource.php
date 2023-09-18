<?php


namespace Transave\CommonBase\Actions\Resource;


use Transave\CommonBase\Actions\Action;

class DeleteResource extends Action
{
    private $request, $validatedInput;
    private $routeConfig, $route, $model;
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
                ->deleteResource()
                ->sendSuccess(null, 'resource deleted successfully');
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

    private function deleteResource()
    {
        $this->model::destroy($this->validatedInput['id']);
        return $this;
    }

    private function validateRequest()
    {
        abort_unless(array_key_exists($this->request['endpoint'], $this->routeConfig), 401, 'endpoint not found');
        $this->route = $this->routeConfig[$this->request['endpoint']];

        $table = $this->route['table'];
        $this->validatedInput = $this->validate($this->request, [
            'id' => ["required", "exists:$table,id"],
            "endpoint" => ["string", "required"],
        ]);
        return $this;
    }
}