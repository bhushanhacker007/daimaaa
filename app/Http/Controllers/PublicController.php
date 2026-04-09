<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Package;
use App\Models\Testimonial;
use App\Models\Faq;
use App\Models\ServiceCategory;
use App\Models\CmsPage;

class PublicController extends Controller
{
    public function home()
    {
        $services = Service::where('is_active', true)->take(6)->get();
        $packages = Package::where('is_active', true)->take(3)->get();
        $testimonials = Testimonial::where('is_featured', true)->take(6)->get();
        $faqs = Faq::orderBy('sort_order')->take(8)->get();

        return view('public.home', compact('services', 'packages', 'testimonials', 'faqs'));
    }

    public function services()
    {
        $categories = ServiceCategory::with(['services' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();
        $packages = Package::where('is_active', true)->get();

        return view('public.services', compact('categories', 'packages'));
    }

    public function serviceDetail(string $slug)
    {
        $service = Service::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('public.service-detail', compact('service'));
    }

    public function howItWorks()
    {
        return view('public.how-it-works');
    }

    public function about()
    {
        return view('public.about');
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function faq()
    {
        $faqs = Faq::orderBy('sort_order')->get();
        return view('public.faq', compact('faqs'));
    }

    public function privacy()
    {
        return view('public.privacy');
    }

    public function terms()
    {
        return view('public.terms');
    }

    public function refundPolicy()
    {
        return view('public.refund-policy');
    }
}
