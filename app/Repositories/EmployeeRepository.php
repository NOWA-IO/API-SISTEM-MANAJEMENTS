<?php

namespace App\Repositories;

use App\Interfaces\EmployeInterface;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeRepository extends BaseRepository implements EmployeInterface
{
    public function __construct(Employe $employe)
    {
        $this->model = $employe;
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
        $query = $this->model->query();

        if ($request->filled('role')) {
            $query->where('role', strtolower($request->role));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate($pagination);
    }
}
