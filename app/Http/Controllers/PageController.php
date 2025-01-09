<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slider;
use App\Models\Banner;
use App\Models\Article;
use App\Models\Contact;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function home() {
        $slider = Slider::first();
        $banner = Banner::first();
        return view('welcome', [
            'slider' => $slider,
            'banner' => $banner,
        ]);
    }
    public function about()
    {
        return view('pages.about');
    }

    public function notice()
    {
        $article = Article::selectRaw('id, title, content, DATE(created_at) as created_date')->get();
        return view('pages.notice.index', [
            'article' => $article
        ]);
    }

    public function noticeShow($id) {
        $article = Article::find($id);
        return view('pages.notice.show', [
            'article' => $article
        ]);
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function application(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|max:11', 
            'email' => 'email|max:255',
            'urls' => 'url|max:255',
            'project_price' => 'string',
            'project_date' => 'string',
            'information' => 'string',
        ]);

        //if ($validator->fails()) {
        //    return redirect()->back()->withErrors($validator)->withInput();
        //}

        // Process the valid data
        $data = $request->only([
            'name', 'phone', 'email', 'urls', 'project_price', 'project_date', 'information'
        ]);

        Contact::insert($data);

        // Redirect with a success message
        return redirect()->back()->with('success', '제출되었습니다. 감사합니다');
    }
}
