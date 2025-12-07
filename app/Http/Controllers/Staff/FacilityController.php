<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Facility;
use Illuminate\Validation\Rule;

class FacilityController extends Controller
{
    /**
     * Display a listing of the facilities.
     * Corresponds to GET /staff/facilities
     */
    public function index()
    {
        // Fetch all facilities, ordered by creation date in ASCENDING order.
        // This ensures the oldest facility is at the top, and the latest added is at the bottom.
        $facilities = Facility::orderBy('created_at', 'asc')->get();
        return view('staff.facilities.index', compact('facilities'));
    }

    /**
     * Show the form for creating a new facility.
     * Corresponds to GET /staff/facilities/create
     */
    public function create()
    {
        return view('staff.facilities.create');
    }

    /**
     * Store a newly created facility in storage.
     * Corresponds to POST /staff/facilities
     */
    public function store(Request $request)
    {
        // 1. Validation (Ensures data integrity and uniqueness)
        $request->validate([
            'name' => 'required|string|max:255|unique:facilities,name',
            'description' => 'nullable|string',
            // Facility types are now restricted to 'room' and 'pad' only.
            'type' => ['required', 'string', Rule::in(['room', 'pad'])],
            'capacity' => 'required|integer|min:1',
            // Ensure status is one of the allowed values
            'status' => ['required', Rule::in(['available', 'not available', 'under maintenance'])],
        ]);

        // 2. Create the Facility record
        Facility::create($request->all());

        // 3. Redirect back to the index page with a success message
        return redirect()->route('staff.facilities.index')
                             ->with('success', 'Facility created successfully!');
    }

    /**
     * Show the form for editing the specified facility.
     * Corresponds to GET /staff/facilities/{facility}/edit
     */
    public function edit(Facility $facility)
    {
        // Pass the facility object to the edit view (used for pre-filling the form)
        return view('staff.facilities.edit', compact('facility'));
    }

    /**
     * Update the specified facility in storage.
     * Corresponds to PUT/PATCH /staff/facilities/{facility}
     */
    public function update(Request $request, Facility $facility)
    {
        // 1. Validation (Ensures uniqueness check ignores the current facility)
        $request->validate([
            // Rule::unique ignores the current facility's ID
            'name' => ['required', 'string', 'max:255', Rule::unique('facilities')->ignore($facility->id)],
            'description' => 'nullable|string',
            // Facility types are now restricted to 'room' and 'pad' only.
            'type' => ['required', 'string', Rule::in(['room', 'pad', 'venue'])],
            'capacity' => 'required|integer|min:1',
            // Crucially, this allows staff to update the status
            'status' => ['required', Rule::in(['available', 'not available', 'under maintenance'])],
        ]);

        // 2. Update the Facility record with the validated data
        $facility->update($request->all());

        // 3. Redirect back to the index page with a success message
        return redirect()->route('staff.facilities.index')
                             ->with('success', 'Facility updated successfully, including status.');
    }

    /**
     * Remove the specified facility from storage.
     * Corresponds to DELETE /staff/facilities/{facility}
     */
    public function destroy(Facility $facility)
    {
        // 1. Delete the facility record
        $facility->delete();

        // 2. Redirect back to the index page with a success message
        return redirect()->route('staff.facilities.index')
                             ->with('success', 'Facility deleted successfully.');
    }
}