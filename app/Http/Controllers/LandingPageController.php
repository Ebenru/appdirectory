<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LandingPageController extends Controller
{
    public function index()
    {
        // --- Fetch Category Counts for Category Cards ---
         $peopleCategoriesCounts = Person::where('status', 'approved')
            ->select('pplCategory', DB::raw('count(*) as count'))
            ->groupBy('pplCategory')
            ->pluck('count', 'pplCategory');

        $companyCategoriesCounts = Company::where('status', 'approved')
            ->select('cmpCategory', DB::raw('count(*) as count'))
            ->groupBy('cmpCategory')
            ->pluck('count', 'cmpCategory');

         $categoryCardsData = []; // Renamed variable

        foreach (Person::CATEGORIES as $slug => $name) {
             if ($peopleCategoriesCounts->has($slug) && $peopleCategoriesCounts[$slug] > 0) {
                 $categoryCardsData['people-'.$slug] = [
                    'name' => $name,
                    'count' => $peopleCategoriesCounts[$slug],
                    'icon' => Person::CATEGORY_ICONS[$slug] ?? 'Briefcase',
                    'slug' => $slug,
                    'type' => 'people'
                ];
            }
        }
         foreach (Company::CATEGORIES as $slug => $name) {
             if ($companyCategoriesCounts->has($slug) && $companyCategoriesCounts[$slug] > 0) {
                $categoryCardsData['company-'.$slug] = [
                    'name' => $name,
                    'count' => $companyCategoriesCounts[$slug],
                    'icon' => Company::CATEGORY_ICONS[$slug] ?? 'Briefcase',
                    'slug' => $slug,
                    'type' => 'companies'
                ];
             }
        }
        // --- End Category Card Data Fetching ---

        // --- Fetch Categories for Search Filter ---
        $allPeopleCategoriesForFilter = Person::CATEGORIES;
        $allCompanyCategoriesForFilter = Company::CATEGORIES;
        // --- End Search Filter Data ---


        return view('landing', [
            'categories' => $categoryCardsData, // For the category cards section
            'peopleCategories' => $allPeopleCategoriesForFilter, // For the search filter partial
            'companyCategories' => $allCompanyCategoriesForFilter, // For the search filter partial
        ]);
    }
}