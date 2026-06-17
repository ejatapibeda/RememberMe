<?php

namespace App\Http\Controllers;

use App\Models\RecurringReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecurringReminderController extends Controller
{
    public function index()
    {
        $items = RecurringReminder::where('user_id', auth()->id())
            ->orderBy('time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'days' => 'required|array|min:1',
            'days.*' => 'integer|between:0,6',
            'time' => 'required|date_format:H:i',
            'enabled' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $reminder = RecurringReminder::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'body' => $request->body,
            'days' => $request->days,
            'time' => $request->time,
            'enabled' => $request->boolean('enabled', true),
        ]);

        return response()->json([
            'success' => true,
            'data' => $reminder,
            'message' => 'Rutinitas berhasil dibuat.',
        ]);
    }

    public function show($id)
    {
        $reminder = RecurringReminder::where('user_id', auth()->id())->findOrFail($id);
        return response()->json(['success' => true, 'data' => $reminder]);
    }

    public function update(Request $request, $id)
    {
        $reminder = RecurringReminder::where('user_id', auth()->id())->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'days' => 'required|array|min:1',
            'days.*' => 'integer|between:0,6',
            'time' => 'required|date_format:H:i',
            'enabled' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $reminder->update([
            'title' => $request->title,
            'body' => $request->body,
            'days' => $request->days,
            'time' => $request->time,
            'enabled' => $request->boolean('enabled', $reminder->enabled),
        ]);

        return response()->json([
            'success' => true,
            'data' => $reminder,
            'message' => 'Rutinitas berhasil diperbarui.',
        ]);
    }

    public function destroy($id)
    {
        $reminder = RecurringReminder::where('user_id', auth()->id())->findOrFail($id);
        $reminder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rutinitas berhasil dihapus.',
        ]);
    }

    public function toggle($id)
    {
        $reminder = RecurringReminder::where('user_id', auth()->id())->findOrFail($id);
        $reminder->enabled = ! $reminder->enabled;
        $reminder->save();

        return response()->json([
            'success' => true,
            'data' => $reminder,
            'message' => $reminder->enabled ? 'Rutinitas diaktifkan.' : 'Rutinitas dinonaktifkan.',
        ]);
    }
}
