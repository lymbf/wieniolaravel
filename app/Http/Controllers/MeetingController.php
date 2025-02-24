<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Project;
use App\Models\MeetingHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{
    /**
     * Wyświetla listę spotkań dla danego projektu.
     */
    public function index(Project $project)
    {
        // Eager loading: attachments, comments.user, comments.attachments
        $meetings = Meeting::where('project_id', $project->id)
            ->with(['attachments', 'comments.user', 'comments.attachments'])
            ->orderBy('date', 'asc')
            ->get();
    
        return view('projects.meetings', compact('project', 'meetings'));
    }

    /**
     * Wyświetla formularz dodawania nowego spotkania dla danego projektu.
     */
    public function create(Project $project)
    {
        return view('projects.create_meeting', compact('project'));
    }

    /**
     * Przetwarza formularz dodawania nowego spotkania.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'date'        => 'required|date_format:Y-m-d\TH:i',
            'type'        => 'required|in:online,on_site,custom',
            'location'    => 'nullable|string|required_if:type,custom',
            'title'       => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        $formattedDate = str_replace('T', ' ', $validated['date']) . ':00';
        $location = $validated['type'] === 'custom'
            ? $validated['location']
            : ($validated['type'] === 'on_site' ? 'na budowie' : 'online');

        Meeting::create([
            'project_id'  => $project->id,
            'user_id'     => Auth::id(),
            'date'        => $formattedDate,
            'type'        => $validated['type'],
            'location'    => $location,
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'status'      => 'scheduled'
        ]);

        return redirect()
            ->route('projects.meetings', $project->id)
            ->with('success', 'Spotkanie zostało dodane.');
    }

    /**
     * Wyświetla szczegóły pojedynczego spotkania.
     */
    public function show(Project $project, Meeting $meeting)
    {
        // Załaduj komentarze wraz z użytkownikami i załącznikami
        $meeting->load([
            'comments.user',
            'comments.attachments'
        ]);

        return view('projects.meetings.show', [
            'project' => $project,
            'meeting' => $meeting,
        ]);
    }

    /**
     * Usuwa spotkanie (tylko admin lub organizator).
     */
    public function destroy(Project $project, Meeting $meeting)
    {
        // Sprawdź, czy użytkownik jest adminem lub twórcą spotkania
        if (!auth()->user()->isAdmin() && $meeting->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Nie masz uprawnień do usunięcia tego spotkania.');
        }

        $meeting->delete();

        return redirect()->route('projects.meetings', $project->id)
            ->with('success', 'Spotkanie zostało usunięte.');
    }

    /**
     * Wyświetla (ewentualny) formularz zmiany terminu spotkania.
     */
    public function editDate(Project $project, Meeting $meeting)
    {
        return view('projects.meeting_edit_date', compact('project', 'meeting'));
    }

    /**
     * Aktualizuje termin spotkania oraz zapisuje historię zmiany.
     */
    public function updateDate(Request $request, Project $project, Meeting $meeting)
    {
        $validated = $request->validate([
            'new_date' => 'required|date_format:Y-m-d\TH:i'
        ]);

        $oldDate = $meeting->date;
        $newDate = str_replace('T', ' ', $validated['new_date']) . ':00';

        // Dodajemy nowy rekord historii za każdym razem, gdy termin jest zmieniany
        MeetingHistory::create([
            'meeting_id' => $meeting->id,
            'old_date'   => $oldDate,
            'new_date'   => $newDate,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);

        // Aktualizujemy termin spotkania w tabeli meetings
        $meeting->update([
            'date' => $newDate
        ]);

        return redirect()->route('projects.meetings.show', ['project' => $project->id, 'meeting' => $meeting->id])
            ->with('success', 'Termin spotkania został zmieniony.');
    }

    /**
     * Usuwa wpis historii zmiany terminu.
     * Jeśli jest to najnowszy wpis, przywracamy datę spotkania do poprzedniego stanu.
     */
    public function destroyHistory(Project $project, Meeting $meeting, MeetingHistory $history)
    {
        // Sprawdź uprawnienia – tylko administrator może usuwać historię zmian
        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Brak uprawnień do usunięcia tej zmiany.');
        }

        // Pobierz najnowszy rekord historii zmiany
        $latestHistory = MeetingHistory::where('meeting_id', $meeting->id)
            ->orderBy('changed_at', 'desc')
            ->first();

        // Jeśli usuwany rekord jest najnowszy (odpowiada bieżącemu terminowi spotkania)
        if ($latestHistory && $latestHistory->id == $history->id) {
            // Usuń rekord
            $history->delete();

            // Sprawdź, czy została jakaś inna historia
            $newLatest = MeetingHistory::where('meeting_id', $meeting->id)
                ->orderBy('changed_at', 'desc')
                ->first();

            if ($newLatest) {
                // Jeśli jest kolejny najnowszy rekord, przywróć termin do new_date z tego wpisu
                $meeting->update(['date' => $newLatest->new_date]);
            } else {
                // Jeśli nie ma już historii, przywróć termin do old_date usuwanego rekordu
                $meeting->update(['date' => $history->old_date]);
            }
        } else {
            // Jeśli usuwany rekord nie jest najnowszy, wystarczy go usunąć
            $history->delete();
        }

        return redirect()->route('projects.meetings.show', [
            'project' => $project->id,
            'meeting' => $meeting->id
        ])->with('success', 'Zmiana terminu została usunięta.');
    }
}
