<?php


return [
    /*
     | ___________________________________________________________
     | ENDPOINTS FOR RESOURCE CONTROLLER VIA RESOURCE ACTIONS
     | ___________________________________________________________
     |
     | Endpoints registered for the source actions and endpoints
     | Endpoints registered here are variables that follow these patterns
     |
     | GET: {endpoint}?search={optional search term}&start={optional start date}&end={optional end date}
     | for getting a listing of resources
     |
     | GET: {endpoint}/{id} for getting a specified resource
     |
     | POST: {endpoint} for creating a new resource in storage
     |
     | POST, PUT, PATCH: {endpoint}/{id} for updating a specified resource
     |
     | DELETE: {endpoint}/{id} for deleting a specified resource from storage
     |
     */

    'routes' => [
        'countries' => [
            'model' => \Transave\CommonBase\Http\Models\Country::class,
            'table' => 'countries',
            'rules' => [
                'store' => [
                    'name' => 'required|string|max:150',
                    'continent' => 'nullable|string',
                    'code' => 'nullable|string',
                ],
                'update' => [
                    'name' => 'sometimes|required|string|max:100',
                    'continent' => 'nullable|string',
                    'code' => 'nullable|string',
                ]
            ],
            'order' => [
                'column' => 'name',
                'pattern' => 'ASC',
            ],
            'relationships' => [],
        ],
        'states' => [
            'model' => \Transave\CommonBase\Http\Models\State::class,
            'table' => 'states',
            'rules' => [
                'store' => [
                    'country_id' => 'required|exists:countries,id',
                    'name' => 'required|string|max:100|unique:states,name',
                    'capital' => 'required|string'
                ],
                'update' => [
                    'country_id' => 'sometimes|required|exists:countries,id',
                    'name' => 'sometimes|required|string|max:100',
                    'capital' => 'nullable'
                ]
            ],
            'order' => [
                'column' => 'name',
                'pattern' => 'ASC',
            ],
            'relationships' => ['country'],
        ],
        'lgas' => [
            'model' => \Transave\CommonBase\Http\Models\Lga::class,
            'table' => 'lgas',
            'rules' => [
                'store' => [
                    'name' => 'required|string|max:100',
                    'state_id' => 'required|exists:states,id',
                ],
                'update' => [
                    'name' => 'sometimes|required|string|max:100',
                    'state_is' => 'sometimes|required|exists:states,id',
                ]
            ],
            'order' => [
                'column' => 'name',
                'pattern' => 'ASC',
            ],
            'relationships' => ['state', 'state.country'],
        ],
    ]
];
