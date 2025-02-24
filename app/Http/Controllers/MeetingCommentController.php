<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingComment;
use App\Models\MeetingCommentAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingCommentController extends Controller
{
    public function store(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'content'    => 'required|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $comment = MeetingComment::create([
            'meeting_id' => $meeting->id,
            'user_id'    => Auth::id(),
            'content'    => $validated['content'],
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $fileName = time().'_'.$originalName;
            $path = $file->storeAs('meeting_comment_attachments', $fileName, 'public');

            MeetingCommentAttachment::create([
                'meeting_comment_id' => $comment->id,
                'file_path'          => $path,
                'original_name'      => $originalName,
            ]);
        }

        return redirect()->back()->with('success', 'Komentarz został dodany.');
    }

   public function destroy(MeetingComment $comment)
   {
       // Sprawdzenie uprawnień – tylko admin lub autor komentarza mogą usunąć
       if (!auth()->user()->isAdmin() && $comment->user_id !== auth()->id()) {
           return redirect()->back()->with('error', 'Brak uprawnień do usunięcia tego komentarza.');
       }
       
       $comment->delete();
       return redirect()->back()->with('success', 'Komentarz został usunięty.');
   }

}
