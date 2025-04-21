<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandingPageController; // Import Landing Page Controller
use App\Http\Controllers\PersonController;   // Import Person Controller
use App\Http\Controllers\CompanyController; // Import Company Controller
use App\Http\Controllers\AdminController; // Import Admin Controller
use App\Http\Controllers\LikeController; // Import Like Controller
use App\Http\Controllers\CommentController; // Import Comment Controller
use App\Http\Controllers\SearchController; // Import Search Controller
use App\Http\Controllers\DashboardController; // Import Dashboard Controller
use Illuminate\Support\Facades\Route; // Import Route Facade
use App\Http\Controllers\CountryController;
use App\Http\Controllers\GroupController;

// Landing Page
Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// Default Breeze Dashboard Route 
//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

// --- Add other routes below ---

// Profile Routes (From Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- User Dashboard ---
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard'); // User Dashboard Route

// --- Submission Routes ---
Route::get('/submit/person', [PersonController::class, 'create'])->name('people.create');
Route::post('/submit/person', [PersonController::class, 'store'])->name('people.store');
Route::get('/submit/company', [CompanyController::class, 'create'])->name('companies.create');
Route::post('/submit/company', [CompanyController::class, 'store'])->name('companies.store');

// Edit/Update
Route::get('/people/{person}/edit', [PersonController::class, 'edit'])->name('people.edit');
Route::match(['put', 'patch'], '/people/{person}', [PersonController::class, 'update'])->name('people.update');
Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
Route::match(['put', 'patch'], '/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');

// --- Delete Routes ---
Route::delete('/people/{person}', [PersonController::class, 'destroy'])->name('people.destroy');
Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');

// --- My Submissions Route ---
Route::get('/my-submissions', [DashboardController::class, 'submissions'])->name('my.submissions'); //

// --- Like Toggle Routes ---
Route::post('/like/{type}/{id}', [LikeController::class, 'toggle'])
    ->whereIn('type', ['person', 'company', 'comment']) // Constrain type parameter
    ->whereNumber('id') // Ensure ID is numeric
    ->name('likes.toggle');

// Store Comment Route
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');

// --- Unified Search Results Route ---
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

// --- Directory Routes (Public) ---
Route::get('/people', [App\Http\Controllers\PersonController::class, 'index'])->name('people.index');
Route::get('/companies', [App\Http\Controllers\CompanyController::class, 'index'])->name('companies.index');
Route::get('/people/{person:slug}', [App\Http\Controllers\PersonController::class, 'show'])->name('people.show');
Route::get('/companies/{company:slug}', [App\Http\Controllers\CompanyController::class, 'show'])->name('companies.show');

// --- ADD Country Routes ---
Route::get('/countries', [CountryController::class, 'index'])->name('countries.index');
Route::get('/countries/{country:slug}', [CountryController::class, 'show'])->name('countries.show'); // Use slug for binding

// --- ADD Group Routes ---
Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
Route::get('/groups/{group:slug}', [GroupController::class, 'show'])->name('groups.show'); // Use slug for binding

// --- Organization Routes ---
Route::get('/organizations', [App\Http\Controllers\OrganizationController::class, 'index'])->name('organizations.index');
Route::get('/organizations/{organization:slug}', [App\Http\Controllers\OrganizationController::class, 'show'])->name('organizations.show');

// --- Event Routes ---
Route::get('/events', [App\Http\Controllers\EventController::class, 'index'])->name('events.index');
Route::get('/events/{event:slug}', [App\Http\Controllers\EventController::class, 'show'])->name('events.show');

// --- Directory Routes (Protected) ---
Route::middleware('auth')->group(function () {
    // Person Routes (Public list & detail)
    Route::get('/submit/person', [App\Http\Controllers\PersonController::class, 'create'])->name('people.create');
    Route::post('/submit/person', [App\Http\Controllers\PersonController::class, 'store'])->name('people.store');

    // Company Routes (Public list & detail)
    Route::get('/submit/company', [App\Http\Controllers\CompanyController::class, 'create'])->name('companies.create');
    Route::post('/submit/company', [App\Http\Controllers\CompanyController::class, 'store'])->name('companies.store');
});

// --- Admin Routes (Protected) ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () { // Apply 'auth' for now
    Route::get('/pending', [AdminController::class, 'pendingList'])->name('pending');
    Route::patch('/approve/person/{person}', [AdminController::class, 'approvePerson'])->name('people.approve');
    Route::patch('/approve/company/{company}', [AdminController::class, 'approveCompany'])->name('companies.approve');
    Route::patch('/reject/person/{person}', [AdminController::class, 'rejectPerson'])->name('people.reject');
    Route::patch('/reject/company/{company}', [AdminController::class, 'rejectCompany'])->name('companies.reject');
    // Add Dashboard route 
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
});


require __DIR__ . '/auth.php'; // Include Breeze auth routes
