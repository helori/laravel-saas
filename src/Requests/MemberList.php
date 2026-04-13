<?php

namespace Helori\LaravelSaas\Requests;

use Helori\LaravelSaas\Resources\User as UserResource;
use Helori\LaravelSaas\Saas;


class MemberList extends ActionRequest
{
    public function authorize()
    {
        return $this->user()->ownTeam(Saas::$teamModel::findOrFail($this->route('teamId')));
    }

    public function rules()
    {
        return [
            'search' => 'sometimes|string|nullable',
            'order_by' => 'sometimes|string|nullable',
            'order_dir' => 'sometimes|string|in:asc,desc',
            'limit' => 'sometimes|integer',
        ];
    }

    public function action()
    {
        $team = Saas::$teamModel::findOrFail($this->route('teamId'));

        $query = $team->users();

        if($this->search)
        {
            $query->where(function($q){
                $q->where('firstname', 'ILIKE', '%'.$this->search.'%')
                    ->orWhere('lastname', 'ILIKE', '%'.$this->search.'%')
                    ->orWhere('email', 'ILIKE', '%'.$this->search.'%');
            });
        }

        $orderBy = $this->has('order_by') ? $this->order_by : 'id';
        $orderDir = $this->has('order_dir') ? $this->order_dir : 'asc';
        $query->orderBy($orderBy, $orderDir);

        $limit = intVal($this->input('limit', 10));
        $items = $query->paginate($limit);

        return UserResource::collection($items);
    }
}
