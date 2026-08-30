<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Service;
use App\Models\Page;
use App\Models\Blog;
use App\Models\Testimonial;
use App\Models\ContactEnquiry;
use App\Models\User;
use App\Models\Location;
use App\Models\Package;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categories
        $categories = [
            [
                'name' => 'Pathology',
                'slug' => 'pathology',
                'description' => 'Blood, urine, and other body fluid tests for detailed analysis.',
                'status' => 'active',
            ],
            [
                'name' => 'Radiology',
                'slug' => 'radiology',
                'description' => 'Imaging services including X-Ray, CT scan, and Ultrasound.',
                'status' => 'active',
            ],
            [
                'name' => 'Cardiology',
                'slug' => 'cardiology',
                'description' => 'Heart diagnostics including ECG, Echo, and TMT.',
                'status' => 'active',
            ]
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        $pathology = Category::where('slug', 'pathology')->first();
        $radiology = Category::where('slug', 'radiology')->first();
        $cardiology = Category::where('slug', 'cardiology')->first();

        // 2. Services
        $services = [
            [
                'category_id' => $pathology->id,
                'title' => 'Complete Blood Count (CBC)',
                'slug' => 'complete-blood-count',
                'short_description' => 'Evaluates overall health and detects a wide range of disorders.',
                'description' => 'A complete blood count (CBC) is a blood test used to evaluate your overall health and detect a wide range of disorders, including anemia, infection and leukemia. A complete blood count test measures several components and features of your blood.',
                'price' => 45.00,
                'status' => 'active',
                'home_collection_available' => true,
            ],
            [
                'category_id' => $pathology->id,
                'title' => 'Thyroid Profile (T3, T4, TSH)',
                'slug' => 'thyroid-profile',
                'short_description' => 'Measures thyroid hormone levels in blood.',
                'description' => 'Thyroid tests are a series of blood tests used to measure how well your thyroid gland is working. Available tests include the T3, T3RU, T4, and TSH.',
                'price' => 60.00,
                'status' => 'active',
                'home_collection_available' => true,
            ],
            [
                'category_id' => $radiology->id,
                'title' => 'Digital X-Ray Chest',
                'slug' => 'digital-x-ray-chest',
                'short_description' => 'Produces images of the heart, lungs, airways, blood vessels.',
                'description' => 'A chest X-ray produces images of the heart, lungs, airways, blood vessels and the bones of the spine and chest. An X-ray is a noninvasive medical test that helps physicians diagnose and treat medical conditions.',
                'price' => 50.00,
                'status' => 'active',
                'home_collection_available' => false, // X-Ray requires in-lab visit
            ],
            [
                'category_id' => $cardiology->id,
                'title' => 'Electrocardiogram (ECG)',
                'slug' => 'electrocardiogram-ecg',
                'short_description' => 'Records the electrical signals in your heart.',
                'description' => 'An electrocardiogram (ECG or EKG) records the electrical signals in your heart. It\'s a common and painless test used to quickly detect heart problems and monitor your heart\'s health.',
                'price' => 30.00,
                'status' => 'active',
                'home_collection_available' => true,
            ]
        ];

        foreach ($services as $serv) {
            Service::updateOrCreate(['slug' => $serv['slug']], $serv);
        }

        // 3. Pages
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<h1>About SuperLab</h1><p>SuperLab is a leading clinical laboratory, offering a wide array of pathological and medical tests. We employ state-of-the-art medical technology to provide highly accurate and reliable results.</p>',
                'meta_title' => 'About SuperLab Diagnostics',
                'meta_description' => 'Learn more about SuperLab, our state-of-the-art facility, and our commitment to diagnostic excellence.',
                'status' => 'active',
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h1>Privacy Policy</h1><p>Your privacy is important to us. It is SuperLab\'s policy to respect your privacy regarding any information we may collect from you across our website.</p>',
                'meta_title' => 'Privacy Policy - SuperLab',
                'meta_description' => 'Read our privacy policy to understand how we protect your personal and medical information.',
                'status' => 'active',
            ]
        ];

        foreach ($pages as $pg) {
            Page::updateOrCreate(['slug' => $pg['slug']], $pg);
        }

        // 4. Blogs
        $author = User::where('email', 'superadmin@superlab.com')->first();
        if ($author) {
            $blogs = [
                [
                    'title' => 'Understanding Your CBC Test Results',
                    'slug' => 'understanding-cbc-test-results',
                    'content' => '<p>A complete blood count (CBC) is one of the most common blood tests. It measures the cells that make up your blood: red blood cells, white blood cells, and platelets. Knowing what these levels mean is critical to managing your health.</p>',
                    'category_id' => $pathology->id,
                    'author_id' => $author->id,
                    'status' => 'published',
                ],
                [
                    'title' => 'Why Annual Health Checkups are Crucial',
                    'slug' => 'why-annual-health-checkups-crucial',
                    'content' => '<p>Annual checkups help identify potential health issues before they become severe. By getting regular screening tests, you are taking active control of your wellness.</p>',
                    'category_id' => $pathology->id,
                    'author_id' => $author->id,
                    'status' => 'published',
                ]
            ];

            foreach ($blogs as $blog) {
                Blog::updateOrCreate(['slug' => $blog['slug']], $blog);
            }
        }

        // 5. Testimonials
        $testimonials = [
            [
                'client_name' => 'John Doe',
                'client_designation' => 'Fitness Trainer',
                'message' => 'SuperLab provided my blood report within 4 hours. The online report downloading feature was seamless!',
                'rating' => 5,
                'status' => 'active',
            ],
            [
                'client_name' => 'Emma Watson',
                'client_designation' => 'Retired Teacher',
                'message' => 'The staff was very gentle and professional. The blood sample collection was completely painless. Highly recommended!',
                'rating' => 5,
                'status' => 'active',
            ]
        ];

        foreach ($testimonials as $test) {
            Testimonial::updateOrCreate(['client_name' => $test['client_name']], $test);
        }

        // 6. Contact Enquiry
        ContactEnquiry::updateOrCreate(
            ['email' => 'jane@example.com', 'subject' => 'Enquiry about Home Sample Collection'],
            [
                'name' => 'Jane Smith',
                'message' => 'Do you provide home sample collection in the Medical District area? If yes, what are the timings and additional charges?',
                'status' => 'unread'
            ]
        );

        // 7. Locations
        $locations = [
            [
                'name' => 'Main Diagnostic Center',
                'slug' => 'main-diagnostic-center',
                'address' => '123 Health Ave, Medical District, NY 10001',
                'phone' => '+1 (555) 019-2834',
                'email' => 'mainbranch@superlab.com',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.4282583852077!2d-73.98741368459384!3d40.75492997932731!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c259a9b3117469%3A0xd134e199a405a163!2sEmpire%20State%20Building!5e0!3m2!1sen!2sus!4v1633000000000!5m2!1sen!2sus" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'status' => 'active',
            ],
            [
                'name' => 'Brooklyn Collection Center',
                'slug' => 'brooklyn-collection-center',
                'address' => '456 Clinic Road, Brooklyn, NY 11201',
                'phone' => '+1 (555) 019-5678',
                'email' => 'brooklyn@superlab.com',
                'map_iframe' => null,
                'status' => 'active',
            ]
        ];

        foreach ($locations as $loc) {
            Location::updateOrCreate(['slug' => $loc['slug']], $loc);
        }

        // 8. Packages
        $packages = [
            [
                'name' => 'Wellwise Total Profile',
                'slug' => 'wellwise-total-profile',
                'badge' => 'MOST BOOKED',
                'discount_percentage' => 49,
                'tests_included_count' => 91,
                'original_price' => 4500,
                'offer_price' => 2279,
                'description' => 'Complete health screening package covering all major organs and metabolic functions including liver, kidney, blood, and thyroid checks.',
                'test_components' => ['Hemogram (24 parameters)', 'Kidney Function Test (8 parameters)', 'Liver Function Test (11 parameters)', 'Lipid Profile (8 parameters)', 'Thyroid Profile (3 parameters)', 'Diabetic Screening (2 parameters)'],
                'faqs' => [
                    ['question' => 'What preparation is required?', 'answer' => 'Fasting of 10-12 hours is mandatory before sample collection.'],
                    ['question' => 'How long does it take to get reports?', 'answer' => 'Usually within 12 to 24 hours. Reports will be sent via Email/WhatsApp.']
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Wellwise Exclusive Profile',
                'slug' => 'wellwise-exclusive-profile',
                'badge' => 'MOST BOOKED',
                'discount_percentage' => 48,
                'tests_included_count' => 95,
                'original_price' => 6000,
                'offer_price' => 3119,
                'description' => 'Extended comprehensive checkup covering advanced parameters including Vitamin D, Vitamin B12, and iron levels.',
                'test_components' => ['Wellwise Total Profile Tests', 'Vitamin D (25-Hydroxy)', 'Vitamin B12', 'Iron Profile (3 parameters)'],
                'faqs' => [
                    ['question' => 'Who is this test for?', 'answer' => 'Recommended for adults experiencing joint pain, fatigue, or general weakness.']
                ],
                'status' => 'active',
            ]
        ];

        foreach ($packages as $pkg) {
            Package::updateOrCreate(['slug' => $pkg['slug']], $pkg);
        }
    }
}
