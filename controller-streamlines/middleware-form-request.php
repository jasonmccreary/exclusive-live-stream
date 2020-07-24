<?php

namespace App\Http\Controllers;

use App\Actions\SendWebpageRegistrationMailAction;
use App\Course;
use App\Customer;
use App\CustomerSEPA;
use App\Membership;
use App\Webpage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class WebpageController extends Controller
{
    public function index(Webpage $page)
    {
        return view('webpage.index', compact('page'));
    }

    public function show(Webpage $page, Course $course)
    {
        return view('webpage.show', compact('page', 'course'));
    }

    public function about(Webpage $page)
    {
        return view('webpage.about', compact('page'));
    }

    public function contact(Webpage $page)
    {
        return view('webpage.contact', compact('page'));
    }

    public function register(Webpage $page, Course $course)
    {
        $organization = $course->organization;

        return view('webpage.register', compact('page', 'course', 'organization'));
    }

    public function store(Webpage $page, Course $course, CourseStoreRequest $request)
    {
        $customer = Customer::createFromRequest($request);

        (new SendWebpageRegistrationMailAction($course, $customer))
            ->execute();

        return redirect(route('webpage.show', [$page, $course]))
            ->with('message', 'Vielen Dank für deine Anmeldung, wir haben deine Daten nun gespeichert und werden uns per E-Mail bei dir melden.');
    }
}
