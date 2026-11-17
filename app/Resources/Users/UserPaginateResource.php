<?php

namespace App\Resources\Users;

use Illuminate\Http\Request;
use App\Resources\Users\UserResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserPaginateResource extends ResourceCollection
{
    protected array $paginate;

    public function __construct($resource, $paginate)
    {
        parent::__construct($resource);
        $this->paginate = $paginate;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => UserResource::collection($this->items()), // items dari paginator
            'paginate' => $this->paginate,
        ];
    }
}
