<?php

namespace Helori\LaravelSaas\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'email_verified_at' => $this->email_verified_at,

            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,
            
            'is_root' => $this->is_root,
            'current_team_id' => $this->current_team_id,
            'activated' => $this->activated,

            'invited_at' => $this->invited_at,
            'invited_to' => $this->invited_to,
            
            'role' => $this->pivot->role,
        ];
    }
}
