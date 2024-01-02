<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;

class ContactUsController extends Controller
{
    use ResponseTrait;

    public function contactUs()
    {
        $data['pageTitle'] = __('Contact Us');
        return view('frontend.contact-us', $data);
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $contactUs = new ContactUs();
            $contactUs->name = $request->name;
            $contactUs->email = $request->email;
            $contactUs->message = $request->message;
            $contactUs->save();
            DB::commit();

            return back()->with('success',__('Send Successfully'));
        } catch (Exception $e) {
            DB::rollBack();
            $message = getMessage(SOMETHING_WENT_WRONG);
            return back()->with('error', $message);
        }
    }

}
