<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:hc');
    }

    public function index()
    {
        $departments = Department::all();
        return response()->json(['departments' => $departments]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:departments',
            'active' => 'boolean'
        ]);

        $department = Department::create($validated);
        return response()->json(['department' => $department, 'message' => 'Department created successfully']);
    }

    public function show(Department $department)
    {
        return response()->json(['department' => $department]);
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:departments,code,' . $department->id,
            'active' => 'boolean'
        ]);

        $department->update($validated);
        return response()->json(['department' => $department, 'message' => 'Department updated successfully']);
    }

    public function destroy(Department $department)
    {
        // Check if department has users
        if ($department->users()->count() > 0) {
            return response()->json(['message' => 'Cannot delete department with associated users'], 422);
        }

        $department->delete();
        return response()->json(['message' => 'Department deleted successfully']);
    }
}
