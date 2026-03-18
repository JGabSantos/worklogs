<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimeEntryRequest;
use App\Services\TimeEntryService;
use App\Http\Requests\UpdateTimeEntryRequest;
use App\Models\ActivityType;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;



class TimeEntryController extends Controller
{
    public function __construct(
        private TimeEntryService $timeEntryService
    ) {}

    public function index()
    {
        $query = Auth::user()
            ->timeEntries()
            ->visible()
            ->latest('date')
            ->latest('start_time');

        if (request('date')) {
            $query->whereDate('date', request('date'));
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('client_id')) {
            $query->where('client_id', request('client_id'));
        }

        if (request('activity_type_id')) {
            $query->where('activity_type_id', request('activity_type_id'));
        }

        $timeEntries = $query->paginate(10)->withQueryString();

        $clients = \App\Models\Client::where('is_active', true)
            ->orderBy('name')
            ->get();

        $activityTypes = \App\Models\ActivityType::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('time-entries.index', compact('timeEntries', 'clients', 'activityTypes'));
    }

    public function store(StoreTimeEntryRequest $request)
    {
        try {
            $this->timeEntryService->create(
                $request->validated(),
                $request->user()
            );

            return redirect()
                ->route('time-entries.index')
                ->with('success', 'Registo criado com sucesso.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit(int $id)
    {
        $timeEntry = Auth::user()
            ->timeEntries()
            ->visible()
            ->findOrFail($id);

        return view('time-entries.edit', [
            'timeEntry' => $timeEntry,
            'activityTypes' => ActivityType::where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'clients' => Client::where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(UpdateTimeEntryRequest $request, int $id)
    {
        try {
            $timeEntry = Auth::user()
                ->timeEntries()
                ->visible()
                ->findOrFail($id);

            $this->timeEntryService->update(
                $timeEntry,
                $request->validated(),
                $request->user()
            );

            return redirect()
                ->route('time-entries.index')
                ->with('success', 'Registo atualizado com sucesso.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(int $id)
    {
        $timeEntry = Auth::user()
            ->timeEntries()
            ->visible()
            ->with(['activityType', 'client'])
            ->findOrFail($id);

        return view('time-entries.show', compact('timeEntry'));
    }

    public function destroy(int $id)
    {
        $timeEntry = Auth::user()
            ->timeEntries()
            ->visible()
            ->findOrFail($id);

        $this->timeEntryService->delete($timeEntry, Auth::user());

        return redirect()
            ->route('time-entries.index')
            ->with('success', 'Registo apagado com sucesso.');
    }
}
