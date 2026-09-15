<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SkuTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SkuTemplateController extends Controller
{
    public function index(): View
    {
        $templates = SkuTemplate::orderBy('sort_order')->orderBy('name')->paginate(15);

        return view('admin.sku-templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('admin.sku-templates.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'pattern' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [
            'name.required' => 'Template name is required.',
            'pattern.required' => 'SKU pattern is required.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        SkuTemplate::create($validated);

        return redirect()->route('admin.sku-templates.index')
            ->with('success', 'SKU template created successfully.');
    }

    public function edit(SkuTemplate $skuTemplate): View
    {
        return view('admin.sku-templates.edit', ['template' => $skuTemplate]);
    }

    public function update(Request $request, SkuTemplate $skuTemplate): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'pattern' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [
            'name.required' => 'Template name is required.',
            'pattern.required' => 'SKU pattern is required.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $skuTemplate->update($validated);

        return redirect()->route('admin.sku-templates.index')
            ->with('success', 'SKU template updated successfully.');
    }

    public function destroy(SkuTemplate $skuTemplate): RedirectResponse
    {
        $skuTemplate->delete();

        return redirect()->route('admin.sku-templates.index')
            ->with('success', 'SKU template deleted successfully.');
    }
}
