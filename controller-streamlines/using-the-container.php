<?php

namespace App\Http\Controllers;

use App\ApplicationSubmitted;
use App\GuestRegistration;
use App\Services\PropertyService;
use Auth;
use Illuminate\Http\Request;
use PrimitiveSocial\BlueMoonSoapWrapper\BlueMoonSoapWrapper;
use PrimitiveSocial\BlueMoonWrapper\BlueMoonWrapper;
use PrimitiveSocial\MaderaApiWrapper\MaderaApiWrapper;

class LeaseController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new MaderaApiWrapper();

        $this->middleware('guest');
    }

    public function create(Request $request)
    {

        if (!$request->query('application') || !$request->query('guest')) {
            return redirect('/dashboard');
        }

        $application = ApplicationSubmitted::with('application', 'creditCheck', 'application.incomes', 'application.occupants', 'application.pets', 'application.vehicles', 'application.coapplicants')
            ->where('id', $request->query('application'))
            ->first();

        $guest = GuestRegistration::with('coapplicants', 'pets')
            ->where('magic_login_code', $request->query('guest'))
            ->first();

        // Get property information
        $property = PropertyService::getProperty($guest->property_id);

        if (!$property || !$application || !$guest) {
            return redirect('/dashboard');
        }

        // We need the blue moon token for the JS on the page
        // $clientLicense = null, $clientUrl = null, $clientSecret = null, $clientId = null, $clientUsername = null, $clientPassword = null
        $bm = new BlueMoonWrapper($property->property->bluemoon_serial_number, null, null, $property->property->bluemoon_id, $property->property->bluemoon_property_username, $property->property->bluemoon_property_password);

        $token = $bm->getToken();

        return view('bluemoon.lease', compact('guest', 'application', 'token', 'property'));
    }

    public function getCallback(Request $request)
    {

        // I have no idea
        $data = $request->all();

        $application = ApplicationSubmitted::where('id', $request->query('application'))
            ->first();

        $application->lease_id = $data['id'];
        $application->save();
    }
}
