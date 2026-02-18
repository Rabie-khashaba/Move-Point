<?php

namespace App\Http\Controllers;

use App\Models\ResignationMessage;
use Illuminate\Http\Request;

class ResignationMessageController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view_messages');

        $messages = ResignationMessage::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('resignation-messages.index', compact('messages'));
    }

    public function create()
    {
        $this->authorize('create_messages');

        return view('resignation-messages.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create_messages');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        ResignationMessage::create($validated);

        return redirect()->route('resignation-messages.index')
            ->with('success', 'تم إنشاء رسالة الاستقالة بنجاح');
    }

    public function edit($id)
    {
        $this->authorize('edit_messages');

        $message = ResignationMessage::findOrFail($id);

        return view('resignation-messages.edit', compact('message'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_messages');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $message = ResignationMessage::findOrFail($id);
        $message->update($validated);

        return redirect()->route('resignation-messages.index')
            ->with('success', 'تم تحديث رسالة الاستقالة بنجاح');
    }

    public function destroy($id)
    {
        $this->authorize('delete_messages');

        $message = ResignationMessage::findOrFail($id);
        $message->delete();

        return redirect()->route('resignation-messages.index')
            ->with('success', 'تم حذف رسالة الاستقالة بنجاح');
    }
}
