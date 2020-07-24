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
    protected $rules = [
        'title' => 'required|in:M,F',
        'fullname' => 'required|max:200',
        'birthday' => 'required|date_format:"d.m.Y"',
        'email' => 'required|email',
        'mobile_nr' => 'required|max:200',
        'street' => 'required|max:200',
        'city' => 'required|max:200',
        'postcode' => 'required|digits_between:3,8',
        'country_code' => 'required|in:de,at,ch',
    ];

    protected $ruleMessages = [
        'fullname.required' => 'Bitte geben Sie einen Namen an.',
        'birthday.required' => 'Bitte geben Sie ein Geburtsdatum an.',
        'birthday.date_format' => 'Bitte geben Sie ein Datum im Format tt.mm.jjjj an',
        'email.required' => 'Bitte geben Sie eine E-Mail Adresse an.',
        'mobile_nr.required' => 'Bitte geben Sie eine Telefonnummer an.',
        'street.required' => 'Bitte geben Sie eine Straße an.',
        'city.required' => 'Bitte geben Sie eine Stadt an.',
        'postcode.required' => 'Bitte geben Sie eine PLZ an.',
    ];


    public function index(Webpage $page)
    {
        if (! $page->visible) {
            return abort(404);
        }

        return view('webpage.index', compact('page'));
    }

    public function show(Webpage $page, Course $course)
    {
        if (! $page->visible) {
            return abort(404);
        }

        return view('webpage.show', compact('page', 'course'));
    }

    public function about(Webpage $page)
    {
        if (! $page->visible) {
            return abort(404);
        }

        return view('webpage.about', compact('page'));
    }

    public function contact(Webpage $page)
    {
        if (! $page->visible) {
            return abort(404);
        }

        return view('webpage.contact', compact('page'));
    }

    public function register(Webpage $page, Course $course)
    {
        if (! $page->visible) {
            return abort(404);
        }

        if (! $course->is_registrable) {
            return abort(403);
        }

        $organization = $course->organization;

        return view('webpage.register', compact('page', 'course', 'organization'));
    }

    public function store(Webpage $page, Course $course, Request $request)
    {
        if (! $page->visible) {
            return abort(404);
        }

        if (! $course->is_registrable) {
            return abort(403);
        }

        if ($course->memberships->count() >= $course->member_max) {
            $error = 'Die maximale Anzahl an Teilnehmern für diesen Kurs wurde leider bereits erreicht.';

            Session::flash('message', $error);
            Session::flash('messageType', 'error');

            throw ValidationException::withMessages(['member_max' => $error]);
        }

        // enrich validation rules
        if ($page->require_sport_health) {
            $this->rules['is_healthy'] = 'accepted';
        }

        // add sepa validation
        if ($request->input('payment_method') === 'sepa') {
            $this->addSepaValidation();
        }

        // add custom ToS validation
        if ($page->has_documents_validation()) {
            $this->addTosOrgValidation();
        }

        $data = $this->validateRequest();

        // clear data which should not be in the database
        unset($data['tos1']);
        unset($data['tos2']);

        // set default values
        $data['is_member'] = $data['is_member'] ?? false;
        $data['is_healthy'] = $data['is_healthy'] ?? false;
        $data['association'] = $data['association'] ?? '';
        $data['registration_note'] = $data['registration_note'] ?? '';
        $data['fullname_representative'] = $data['fullname_representative'] ?? '';
        $data['organization_id'] = $page->organization->id;

        // split sepa data
        if (isset($data['sepa_fullname'])) {
            $dataSepa = [
                'fullname' => $data['sepa_fullname'],
                'IBAN' => $data['sepa_IBAN'],
                'BIC' => $data['sepa_BIC'],
                'mandate_nr' => '0',
                'mandate_date' => now()
            ];
            unset($data['sepa_fullname']);
            unset($data['sepa_IBAN']);
            unset($data['sepa_BIC']);
        }

        $customer = Customer::create($data);

        Membership::create([
            'course_id' => $course->id,
            'customer_id' => $customer->id,
            'price' => 0,
            'date_joined' => now(),
            'on_hold' => true
        ]);

        if (isset($dataSepa)) {
            $dataSepa['customer_id'] = $customer->id;
            CustomerSEPA::create($dataSepa);
        }

        (new SendWebpageRegistrationMailAction($course, $customer))
            ->execute();

        return redirect(route('webpage.show', [$page, $course]))
            ->with('message', 'Vielen Dank für deine Anmeldung, wir haben deine Daten nun gespeichert und werden uns per E-Mail bei dir melden.');
    }

    protected function addSepaValidation(): void
    {
        $this->rules['sepa_fullname'] = 'required|max:200';
        $this->rules['sepa_IBAN'] = 'required|iban';
        $this->rules['sepa_BIC'] = 'required|bic';
    }

    protected function addTosOrgValidation(): void
    {
        $this->rules['tos2'] = 'accepted';
    }

    protected function validateRequest()
    {
        return request()->validate($this->rules, $this->ruleMessages);
    }
}
