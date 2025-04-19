<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\TicketCategory;
use Illuminate\Http\Request;

class TicketCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:hc');
    }

    public function index()
    {
        $categories = TicketCategory::all();
        return response()->json(['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|in:IT,GA',
            'active' => 'boolean'
        ]);

        $category = TicketCategory::create($validated);
        return response()->json(['category' => $category, 'message' => 'Category created successfully']);
    }

    public function show(TicketCategory $category)
    {
        return response()->json(['category' => $category]);
    }

    public function update(Request $request, TicketCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|in:IT,GA',
            'active' => 'boolean'
        ]);

        $category->update($validated);
        return response()->json(['category' => $category, 'message' => 'Category updated successfully']);
    }

    public function destroy(TicketCategory $category)
    {
        // Check if category has tickets
        if ($category->tickets()->count() > 0) {
            return response()->json(['message' => 'Cannot delete category with associated tickets'], 422);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
