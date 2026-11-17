<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use App\Interfaces\UserInterface;
use App\Http\Requests\UserRequest;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;


class UserRepository extends BaseRepository implements UserInterface
{
    
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * Handle paginate data event from models.
     *
     * @param Request $request
     * @param int $pagination
     *
     * @return LengthAwarePaginator
     */

    public function customPaginate(Request $request, int $pagination = 10): LengthAwarePaginator
    {
        return $this->model->paginate($pagination);
    }
}
