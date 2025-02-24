<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MeetingComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MeetingCommentController extends Controller
{
    // Dodawanie komentarza do spotkania
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meeting_id' => 'required|exists:meetings,id',
            'user_id'    => 'required|exists:users,id',
            'content'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $comment = MeetingComment::create($validator->validated());
        return response()->json($comment, 201);
    }
}
