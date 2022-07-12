<?php

namespace Helori\LaravelSaas\Requests;

use Helori\LaravelSaas\Resources\User as UserResource;


class MemberList extends ActionRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = $this->user();
        $team = $user->teams()->findOrFail($this->route('teamId'));
        return $user->ownTeam($team);
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

    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        $user = $this->user();
        $teamId = $this->route('teamId');

        $team = $user->teams()->with('users')->findOrFail($teamId);

        if(!$user->ownTeam($team))
        {
            abort(403, "Vous ne pouvez pas voir les membres de l'équipe");
        }

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
