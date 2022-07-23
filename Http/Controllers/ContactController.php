<?php

namespace App\Http\Controllers;

use App\Models\ContactForm;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        ContactForm::create($request->all());
        return response('Your message sent succesfully!', 200);
    }
}
