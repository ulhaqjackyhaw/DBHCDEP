use App\Http\Controllers\EmployeeController;

Route::get('/employees', [EmployeeController::class, 'index']);
