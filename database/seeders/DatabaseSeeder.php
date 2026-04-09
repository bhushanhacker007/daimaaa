<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\City;
use App\Models\Pincode;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\Package;
use App\Models\AddOn;
use App\Models\Faq;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@daimaaa.com',
            'phone' => '9999999999',
            'role' => 'super_admin',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Demo customer
        User::create([
            'name' => 'Priya Sharma',
            'email' => 'priya@example.com',
            'phone' => '9876543210',
            'role' => 'customer',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Demo daimaa
        $daimaa = User::create([
            'name' => 'Savita Bai',
            'email' => 'savita@example.com',
            'phone' => '9876543211',
            'role' => 'daimaa',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $daimaa->daimaaProfile()->create([
            'years_of_experience' => 12,
            'bio' => 'Experienced Daimaa with 12 years of traditional maternal care expertise.',
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        // Cities
        $mumbai = City::create(['name' => 'Mumbai', 'state' => 'Maharashtra']);
        $pune = City::create(['name' => 'Pune', 'state' => 'Maharashtra']);
        $delhi = City::create(['name' => 'New Delhi', 'state' => 'Delhi']);
        $bangalore = City::create(['name' => 'Bangalore', 'state' => 'Karnataka']);

        foreach (['400001', '400002', '400050', '400053', '400058', '400070'] as $pin) {
            Pincode::create(['pincode' => $pin, 'city_id' => $mumbai->id]);
        }
        foreach (['411001', '411004', '411014'] as $pin) {
            Pincode::create(['pincode' => $pin, 'city_id' => $pune->id]);
        }
        foreach (['110001', '110011', '110025'] as $pin) {
            Pincode::create(['pincode' => $pin, 'city_id' => $delhi->id]);
        }
        foreach (['560001', '560011', '560034'] as $pin) {
            Pincode::create(['pincode' => $pin, 'city_id' => $bangalore->id]);
        }

        // Service categories
        $motherCare = ServiceCategory::create(['name' => 'Mother Care', 'slug' => 'mother-care', 'icon' => 'favorite', 'description' => 'Nurturing care for new mothers during recovery.', 'sort_order' => 1]);
        $babyCare = ServiceCategory::create(['name' => 'Baby Care', 'slug' => 'baby-care', 'icon' => 'child_care', 'description' => 'Gentle traditional care for your newborn.', 'sort_order' => 2]);
        $comboCare = ServiceCategory::create(['name' => 'Mother & Baby Combo', 'slug' => 'combo-care', 'icon' => 'diversity_1', 'description' => 'Complete care for both mother and baby.', 'sort_order' => 3]);

        // Services
        Service::create(['category_id' => $motherCare->id, 'name' => 'Mother Massage', 'slug' => 'mother-massage', 'short_description' => 'Traditional full-body massage to restore strength and reduce post-delivery pain.', 'description' => 'A 60-minute traditional full-body massage using warm herbal oils. Designed to restore the mother\'s core energy, ease muscle tension, and promote recovery after delivery.', 'duration_minutes' => 60, 'base_price' => 1200, 'icon' => 'spa', 'sort_order' => 1]);
        Service::create(['category_id' => $motherCare->id, 'name' => 'Post-Pregnancy Belly Binding', 'slug' => 'belly-binding', 'short_description' => 'Traditional belly wrapping technique for postpartum recovery.', 'description' => 'Ancient belly binding technique using soft cotton cloth. Supports abdominal muscles, improves posture, and aids the uterus in returning to its original size.', 'duration_minutes' => 30, 'base_price' => 600, 'icon' => 'healing', 'sort_order' => 2]);
        Service::create(['category_id' => $babyCare->id, 'name' => 'Baby Massage', 'slug' => 'baby-massage', 'short_description' => 'Gentle traditional massage to strengthen your baby\'s muscles and bones.', 'description' => 'A gentle 30-minute oil massage for newborns. Strengthens bones, improves blood circulation, promotes better sleep, and builds a loving bond through the healing touch of a Daimaa.', 'duration_minutes' => 30, 'base_price' => 800, 'icon' => 'self_improvement', 'sort_order' => 3]);
        Service::create(['category_id' => $babyCare->id, 'name' => 'Baby Bath', 'slug' => 'baby-bath', 'short_description' => 'Safe and soothing traditional bathing ritual for your newborn.', 'description' => 'A carefully administered warm bath for the baby using mild natural products. Includes proper handling, gentle cleansing, and post-bath moisturizing and swaddling.', 'duration_minutes' => 30, 'base_price' => 600, 'icon' => 'bathtub', 'sort_order' => 4]);
        Service::create(['category_id' => $comboCare->id, 'name' => 'Mother & Baby Combo Session', 'slug' => 'mother-baby-combo', 'short_description' => 'Complete care session covering both mother and baby in one visit.', 'description' => 'A comprehensive 90-minute session that includes mother massage, baby massage, and baby bath. The most popular choice for families wanting complete care in a single visit.', 'duration_minutes' => 90, 'base_price' => 2200, 'icon' => 'diversity_1', 'sort_order' => 5]);
        Service::create(['category_id' => $motherCare->id, 'name' => 'Herbal Steam Bath', 'slug' => 'herbal-steam-bath', 'short_description' => 'Therapeutic herbal steam to detoxify and rejuvenate.', 'description' => 'A traditional herbal steam therapy using neem, turmeric, and other Ayurvedic herbs. Helps detoxify the body, open pores, and promote overall well-being during recovery.', 'duration_minutes' => 45, 'base_price' => 900, 'icon' => 'local_florist', 'sort_order' => 6]);

        // Packages
        $p1 = Package::create(['name' => 'Essential Care', 'slug' => 'essential-care', 'description' => 'Perfect for first-time mothers. 10 sessions of foundational care.', 'total_sessions' => 10, 'price' => 9999, 'discount_percent' => 15, 'is_featured' => true, 'sort_order' => 1]);
        $p1->services()->attach([1 => ['session_count' => 5], 3 => ['session_count' => 5]]);

        $p2 = Package::create(['name' => 'Complete Nurture', 'slug' => 'complete-nurture', 'description' => 'Our most popular package. 20 sessions of comprehensive mother and baby care.', 'total_sessions' => 20, 'price' => 17999, 'discount_percent' => 20, 'is_featured' => true, 'sort_order' => 2]);
        $p2->services()->attach([1 => ['session_count' => 8], 3 => ['session_count' => 6], 4 => ['session_count' => 6]]);

        $p3 = Package::create(['name' => 'Sacred 40 Days', 'slug' => 'sacred-40-days', 'description' => 'The ultimate postpartum package. Daily care for the full traditional 40-day recovery period.', 'total_sessions' => 40, 'price' => 34999, 'discount_percent' => 25, 'is_featured' => true, 'sort_order' => 3]);
        $p3->services()->attach([1 => ['session_count' => 15], 2 => ['session_count' => 5], 3 => ['session_count' => 10], 4 => ['session_count' => 10]]);

        // Add-ons
        AddOn::create(['name' => 'Extra Herbal Oil Kit', 'description' => 'Premium Ayurvedic oil blend for at-home self-massage between sessions.', 'price' => 499]);
        AddOn::create(['name' => 'Lactation Support Session', 'description' => 'Guided lactation support and breast massage for new mothers.', 'price' => 799]);
        AddOn::create(['name' => 'Baby Photography Session', 'description' => 'Professional newborn photography during one of your care sessions.', 'price' => 1499]);

        // FAQs
        $faqs = [
            ['question' => 'What is a Daimaa?', 'answer' => 'A Daimaa is an experienced traditional caregiver who specializes in pre-pregnancy and post-pregnancy care for mothers and newborns. Our Daimaas carry generations of knowledge in traditional Indian maternal care practices including massage, bathing rituals, and postnatal recovery techniques.', 'category' => 'General', 'sort_order' => 1],
            ['question' => 'Are your Daimaas verified and trained?', 'answer' => 'Yes, every Daimaa goes through a rigorous verification process including KYC documentation, background checks, and skill assessment. We verify years of experience and ensure they are trained in both traditional practices and modern hygiene standards.', 'category' => 'Trust & Safety', 'sort_order' => 2],
            ['question' => 'How do I book a service?', 'answer' => 'Simply register on our platform, choose your service or package, select your preferred date and time slot, enter your address, and confirm your booking. You can also call us for assistance with booking.', 'category' => 'Booking', 'sort_order' => 3],
            ['question' => 'Can I reschedule or cancel a booking?', 'answer' => 'Yes, you can reschedule up to 12 hours before your scheduled session. Cancellations made 24 hours in advance are eligible for a full refund. Please refer to our refund policy for complete details.', 'category' => 'Booking', 'sort_order' => 4],
            ['question' => 'Which cities do you currently serve?', 'answer' => 'We currently operate in Mumbai, Pune, Delhi, and Bangalore. We are expanding to more cities soon. Enter your pincode on our website to check availability in your area.', 'category' => 'General', 'sort_order' => 5],
            ['question' => 'What should I prepare before the Daimaa arrives?', 'answer' => 'Please prepare a clean, warm, and comfortable space — a bed or mattress with a soft sheet. We recommend keeping the room warm and having clean towels ready. The Daimaa will bring all necessary oils and supplies.', 'category' => 'General', 'sort_order' => 6],
            ['question' => 'Is the service safe for my newborn?', 'answer' => 'Absolutely. Our Daimaas are specifically trained in gentle newborn handling. We use only baby-safe, natural products. All practices follow time-tested traditions that have been used safely for generations.', 'category' => 'Trust & Safety', 'sort_order' => 7],
            ['question' => 'What payment methods do you accept?', 'answer' => 'We accept UPI, credit/debit cards, net banking, and cash on delivery. For packages, you can also opt for partial advance payment. All online payments are processed securely.', 'category' => 'Payments', 'sort_order' => 8],
        ];
        foreach ($faqs as $faq) {
            Faq::create($faq);
        }

        // Testimonials
        Testimonial::create(['name' => 'Ananya Deshmukh', 'content' => 'The Daimaa who came to our home was incredibly gentle and experienced. My baby loved the massage sessions, and I could see the difference in his sleep patterns within a week.', 'city' => 'Mumbai', 'rating' => 5, 'is_featured' => true]);
        Testimonial::create(['name' => 'Meera Patel', 'content' => 'After my C-section, I was in so much pain. The mother massage sessions were a blessing. My Daimaa knew exactly where the tension was and worked on it with such care.', 'city' => 'Pune', 'rating' => 5, 'is_featured' => true]);
        Testimonial::create(['name' => 'Kavitha Reddy', 'content' => 'I was skeptical at first, but the Sacred 40 Days package transformed my recovery. The traditional belly binding and daily massages made me feel like myself again so much faster.', 'city' => 'Bangalore', 'rating' => 5, 'is_featured' => true]);
        Testimonial::create(['name' => 'Sneha Gupta', 'content' => 'What I loved most was the trust factor. The Daimaa was verified, professional, and treated my home with respect. It felt like having a caring elder in the house.', 'city' => 'Delhi', 'rating' => 5, 'is_featured' => true]);
    }
}
