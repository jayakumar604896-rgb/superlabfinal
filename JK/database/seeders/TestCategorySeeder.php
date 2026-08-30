<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categoriesData = [
            [
                'category_name' => 'Pregnancy Test',
                'slug' => 'pregnancy-test',
                'sort_order' => 1,
                'tests' => [
                    'Haemoglobin Estimation Test', 'Prolactin Test', 'Double Marker Test',
                    'Progesterone Test', 'Quadruple Marker Test', 'TORCH IgG IgM Test',
                    'Beta HCG Test', 'Haemoglobin HPLC Test', 'LH Test', 'AFP Test',
                    'Bile Acid Test', 'Urine Pregnancy Test', 'Urine Culture Test',
                    'Estradiol Test', 'GTT Test', 'Viral Marker Profile', 'Free Testosterone Test'
                ]
            ],
            [
                'category_name' => 'Heart Test',
                'slug' => 'heart-test',
                'sort_order' => 2,
                'tests' => [
                    'Lipid Profile Test', 'Troponin I Test', 'CPK Test', 'NT ProBNP Test',
                    'CK MB Test', 'Ionized Calcium Test', 'LDL Cholesterol Test',
                    'HDL Cholesterol Test', 'Apolipoprotein Test'
                ]
            ],
            [
                'category_name' => 'HIV Test',
                'slug' => 'hiv-test',
                'sort_order' => 3,
                'tests' => [
                    'HIV Rapid Test', 'HIV 1 & 2 Test', 'STD Panel Test',
                    'HIV RNA PCR Quantitative Test', 'CD4 And CD8 Count Test', 'Western Blot HIV Test'
                ]
            ],
            [
                'category_name' => 'Fever Test',
                'slug' => 'fever-test',
                'sort_order' => 4,
                'tests' => [
                    'Dengue NS1 Antigen Test', 'Malaria Parasite Test', 'Typhoid Test',
                    'Widal Test', 'Chikungunya IgM Test', 'Scrub Typhus Test'
                ]
            ],
            [
                'category_name' => 'Hormone Test',
                'slug' => 'hormone-test',
                'sort_order' => 5,
                'tests' => [
                    'Testosterone Total Test', 'Cortisol Test', 'Insulin Fasting Test',
                    'Growth Hormone Test', 'DHEAS Test', 'FSH Test'
                ]
            ],
            [
                'category_name' => 'Allergy Test',
                'slug' => 'allergy-test',
                'sort_order' => 6,
                'tests' => [
                    'IgE Total Test', 'Food Allergy Panel Test', 'Inhalant Allergy Panel Test'
                ]
            ],
            [
                'category_name' => 'Tuberculosis Test',
                'slug' => 'tuberculosis-test',
                'sort_order' => 7,
                'tests' => [
                    'TB Gold Quantiferon Test', 'Sputum AFB Test', 'GeneXpert MTB Test'
                ]
            ],
            [
                'category_name' => 'Diabetes Test',
                'slug' => 'diabetes-test',
                'sort_order' => 8,
                'tests' => [
                    'HbA1c Test', 'Fasting Blood Sugar Test', 'Post Prandial Blood Sugar Test',
                    'Glucose Tolerance Test', 'Insulin Post Prandial Test', 'Microalbumin Test'
                ]
            ],
            [
                'category_name' => 'Thyroid Test',
                'slug' => 'thyroid-test',
                'sort_order' => 9,
                'tests' => [
                    'Total Thyroid Test (T3, T4, TSH)', 'Free T4 Test', 'Free T3 Test',
                    'Anti TPO Test', 'TSH Antibody Receptor Test', 'Thyroid Profile Advance Test'
                ]
            ]
        ];

        foreach ($categoriesData as $catData) {
            $cleanedCategoryName = preg_replace('/\s*[tT]est\s*$/', '', $catData['category_name']);

            $category = Category::updateOrCreate(
                ['slug' => $catData['slug']],
                [
                    'name' => $cleanedCategoryName,
                    'description' => 'Diagnostic and laboratory tests for ' . $cleanedCategoryName,
                    'status' => 'active',
                ]
            );

            foreach ($catData['tests'] as $testName) {
                $testSlug = Str::slug($cleanedCategoryName . '-' . $testName);
                $price = rand(299, 1499);

                Service::updateOrCreate(
                    ['slug' => $testSlug],
                    [
                        'category_id' => $category->id,
                        'title' => $testName,
                        'short_description' => 'Comprehensive laboratory test for ' . $testName,
                        'description' => 'A comprehensive diagnostic blood test for ' . $testName . ' designed to evaluate key health parameters.',
                        'price' => $price,
                        'status' => 'active',
                        'home_collection_available' => true,
                    ]
                );
            }
        }
    }
}
