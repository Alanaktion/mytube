<?php

namespace App\Http\Controllers;

use App\Models\JobDetail;
use Illuminate\Http\Request;

class JobDetailsController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'type' => 'sometimes|in:playlist,channel,video',
            'id' => 'sometimes|integer',
        ]);

        $details = JobDetail::query();
        if ($request->filled('type')) {
            $details->where('model_type', 'App\\Models\\' . ucfirst($request->input('type')));
        }
        if ($request->filled('id')) {
            $details->where('model_id', $request->input('id'));
        } else {
            $details->with('model:id,title,uuid');
        }
        return $details->get();
    }
}
