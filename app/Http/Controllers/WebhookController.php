<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function create()
    {
        $modules = [
            'New Course' => 'New Course','New Order' => 'New Order','New Zoom Meeting' => 'New Zoom Meeting',
        ];
        $methods = ['GET' => 'GET', 'POST' => 'POST'];
        return view('webhook.create', compact('modules', 'methods'));
    }
    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'module' => 'required',
                'url' => 'required|url',
                'method' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $webhook = new Webhook();
        $webhook->module = $request->module;
        $webhook->url = $request->url;
        $webhook->method = $request->method;
        $webhook->store_id = Auth::user()->current_store;
        $webhook->created_by = Auth::user()->id;
        $webhook->save();
        return redirect()->back()->with('success', __('Webhook Successfully Created.'));
    }
    public function edit(Request $request, $id)
    {
        $modules = [
            'New Course' => 'New Course','New Order' => 'New Order','New Zoom Meeting' => 'New Zoom Meeting',
        ];
        $methods = ['GET' => 'GET', 'POST' => 'POST'];
        $webhook = Webhook::find($id);

        return view('webhook.edit', compact('webhook', 'modules', 'methods'));
    }
    public function update(Request $request, $id)
    {
        $webhook['module']       = $request->module;
        $webhook['url']       = $request->url;
        $webhook['method']      = $request->method;
        $webhook['store_id'] = Auth::user()->current_store;
        $webhook['created_by'] = Auth::user()->creatorId();
        Webhook::where('id', $id)->update($webhook);
        return redirect()->back()->with('success', __('Webhook Setting Succssfully Updated'));
    }
    public function destroy($id)
    {
        $webhook = Webhook::find($id);
        $webhook->delete();

        return redirect()->back()->with('success', __('Webhook successfully deleted.'));
    }
}
