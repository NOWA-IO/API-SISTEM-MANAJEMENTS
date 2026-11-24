# AGENTS.md — Development Guidelines & Codebase Architecture

Panduan ini ditujukan untuk AI Agent maupun pengembang dalam memahami arsitektur, konvensi penulisan kode, dan aturan pengembangan pada repositori **API-SISTEM-MANAJEMENTS**.

---

## 1. Project Overview & Tech Stack

- **Framework:** Laravel 11/12 (`laravel/framework`)
- **PHP Version:** PHP 8.2+ / 8.3+
- **Database:** MySQL
- **Authentication:** Laravel Sanctum (`auth:sanctum`)
- **Role & Permission:** Spatie Laravel-Permission (`spatie/laravel-permission`)
- **Primary Key Convention:** Menggunakan **UUID** (`$table->uuid('id')->primary()`, trait `HasUuids`) di semua tabel dan model.
- **Timestamp Formatting:** Trait `HasFormattedTimestamps` di setiap model.

---

## 2. Struktur Direktori Proyek

```
app/
├── Enums/                 # Enum kelas (UserRoleEnum, dsb.)
├── Helpers/               # ResponseHelper, PaginateHelper, QueryFilterHelper, EnumHelper
├── Http/
│   ├── Controllers/       # Controller (UserController, EmployeeController, ProductController)
│   ├── Handlers/          # Business logic handlers (HANYA jika ada business logic khusus)
│   └── Requests/          # Form Request validasi (UserRequest, EmployeeRequest, ProductRequest)
├── Interfaces/            # BaseInterface & Domain Interfaces (ProductInterface, EmployeInterface, dsb.)
│   └── Eloquent/          # Interface CRUD Eloquent (Get, Store, Show, Update, Delete, Paginate, dsb.)
├── Models/                # Eloquent Models (User, Employe, Product)
├── Providers/             # AppServiceProvider (dependency injection bindings)
├── Repositories/          # BaseRepository & Domain Repositories (ProductRepository, dsb.)
├── Resources/             # API Resources per modul (Users/, Employees/, Products/)
└── Traits/                # Trait reusable (HasFormattedTimestamps)
routes/
└── api.php                # API Routes (auth, sanctum, role-based groups)
```

---

## 3. Aturan Arsitektur & Pola Desain (Coding Standards)

Setiap pembuatan modul fitur baru (misal: CRUD fitur baru) **wajib** mengikuti pola layer berikut:

### A. Model (`app/Models/`)
- Gunakan UUID:
  ```php
  use App\Traits\HasFormattedTimestamps;
  use Illuminate\Database\Eloquent\Concerns\HasUuids;
  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;

  class ModelName extends Model
  {
      use HasFactory, HasUuids, HasFormattedTimestamps;

      protected $table = 'table_names';
      protected $keyType = 'string';
      public $incrementing = false;

      protected $fillable = [...];
  }
  ```

### B. Interface (`app/Interfaces/`)
- Setiap modul memiliki Interface yang meng-extend `BaseInterface`:
  ```php
  namespace App\Interfaces;

  interface ProductInterface extends BaseInterface
  {
  }
  ```

### C. Repository (`app/Repositories/`)
- Meng-extend `BaseRepository` dan mengimplementasikan Domain Interface terkait.
- Gunakan `QueryFilterHelper` untuk filter, pencarian (`search`), sorting (`sort_by`, `sort_dir`), dan paginasi:
  ```php
  namespace App\Repositories;

  use App\Helpers\QueryFilterHelper;
  use App\Interfaces\ProductInterface;
  use App\Models\Product;
  use Illuminate\Pagination\LengthAwarePaginator;

  class ProductRepository extends BaseRepository implements ProductInterface
  {
      public function __construct(Product $model)
      {
          $this->model = $model;
      }

      public function getAllData(array $filters = [], int $perPage = 10): LengthAwarePaginator
      {
          $query = $this->model->newQuery();
          $searchColumn = ['name'];

          QueryFilterHelper::applyFilters($query, $filters, $searchColumn);
          QueryFilterHelper::applySorting($query, $filters);

          return $query->paginate($perPage);
      }
  }
  ```

### D. Service Provider Binding (`app/Providers/AppServiceProvider.php`)
- Setiap interface baru **harus didaftarkan** di properti `$bindings` dalam `AppServiceProvider::register()`:
  ```php
  $bindings = [
      \App\Interfaces\UserInterface::class => \App\Repositories\UserRepository::class,
      \App\Interfaces\EmployeInterface::class => \App\Repositories\EmployeeRepository::class,
      \App\Interfaces\ProductInterface::class => \App\Repositories\ProductRepository::class,
  ];
  ```

### E. Handler (`app/Http/Handlers/`) — ATURAN KHUSUS
> [!IMPORTANT]
> **ATURAN HANDLER:**
> - **JANGAN** membuat file `Handler` jika fitur hanya melakukan CRUD standar tanpa *business logic* khusus.
> - Controller harus langsung memanggil method dari Repository.
> - Buat `Handler` **HANYA JIKA** terdapat *business logic* yang kompleks, seperti:
>   - Hashing/enkripsi password
>   - Sinkronisasi atau manipulasi multi-role Spatie (`assignRole`, `syncRoles`)
>   - Upload/processing file atau relasi kompleks multi-tabel
>   - Otentikasi dan token generation

### F. Form Request (`app/Http/Requests/`)
- Meng-extend `FormRequest` dengan validasi rules dan pesan berbahasa Indonesia:
- Wajib override `failedValidation` menggunakan `ResponseHelper::error`:
  ```php
  public function failedValidation(Validator $validator)
  {
      return ResponseHelper::error($validator->errors(), trans('alert.validation_errors') ?? 'Validasi gagal.');
  }
  ```

### G. API Resources (`app/Resources/<Modul>/`)
- Buat 2 resource per entitas:
  1. `<Modul>Resource`: Representasi objek tunggal.
  2. `<Modul>PaginateResource`: Format collection berpaginasi dengan `PaginateHelper::getPaginate($paginator)`.

### H. Controller (`app/Http/Controllers/`)
- Selalu bungkus operasi create, update, dan delete dalam `DB::beginTransaction()` dan `DB::commit()` / `DB::rollBack()`.
- Selalu gunakan `ResponseHelper::success()` dan `ResponseHelper::error()`:
  ```php
  // Format standar controller
  class ExampleController extends Controller
  {
      protected ExampleRepository $repository;

      public function __construct(ExampleRepository $repository)
      {
          $this->repository = $repository;
      }

      public function index(Request $request): JsonResponse
      {
          try {
              $perPage = $request->get('per_page', 10);
              $filters = $request->only(['sort_by', 'sort_dir', 'search']);
              $data = $this->repository->getAllData($filters, $perPage);

              return ResponseHelper::success(
                  ExamplePaginateResource::make($data, PaginateHelper::getPaginate($data)),
                  'Data berhasil diambil.'
              );
          } catch (\Throwable $th) {
              return ResponseHelper::error(message: $th->getMessage());
          }
      }

      public function store(ExampleRequest $request): JsonResponse
      {
          DB::beginTransaction();
          try {
              $payload = $request->validated();
              $payload['user_id'] = auth()->id(); // Jika membutuhkan user login

              $item = $this->repository->store($payload);

              DB::commit();
              return ResponseHelper::success(ExampleResource::make($item), 'Data berhasil ditambahkan.', 201);
          } catch (\Throwable $th) {
              DB::rollBack();
              return ResponseHelper::error(message: 'Gagal menambahkan data: ' . $th->getMessage());
          }
      }
  }
  ```

---

## 4. Response & Helper Standard

Format JSON API distandarisasi oleh `ResponseHelper`:
```json
{
  "meta": {
    "code": 200,
    "status": "success",
    "message": "Pesan keberhasilan",
    "pagination": { ... } // opsional jika endpoint berpaginasi
  },
  "data": { ... }
}
```

Format JSON Error:
```json
{
  "meta": {
    "code": 400,
    "status": "error",
    "message": "Pesan error"
  },
  "data": { ... } // validation errors detail jika ada
}
```

---

## 5. Role & Route Grouping (`routes/api.php`)

Enum Role (`App\Enums\UserRoleEnum`):
- `UserRoleEnum::ADMIN->value` (`"Admin"`)
- `UserRoleEnum::EMPLOYEE->value` (`"Employee"`)
- `UserRoleEnum::SELLER->value` (`"Seller"`)

Struktur route API:
- `auth/*`: Route publik otentikasi (`login`, `register`).
- `middleware('auth:sanctum')`:
  - Route umum untuk semua pengguna yang sudah login (misal: `products`, `users`).
  - `middleware(['role:Admin'])`: Khusus hak akses Admin (misal: manajemen `employees`).
  - `middleware(['role:Employee'])`: Khusus hak akses Karyawan.
  - `middleware(['role:Seller'])`: Khusus hak akses Seller.

---

## 6. Checklist Sebelum Menyelesaikan Perubahan

1. [ ] Apakah sintaks PHP valid (`php -l <file>`) tanpa syntax error?
2. [ ] Apakah Interface sudah dibinding ke Repository di `AppServiceProvider.php`?
3. [ ] Apakah Model menggunakan trait UUID dan formatted timestamp yang sesuai?
4. [ ] Apakah Controller hanya menggunakan Handler jika memang ada *business logic* khusus?
5. [ ] Apakah respons JSON menggunakan `ResponseHelper` dan `PaginateHelper`?
6. [ ] Apakah route sudah didaftarkan di grup middleware yang tepat pada `routes/api.php`?
