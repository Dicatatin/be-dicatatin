<?php

namespace App\Http\Controllers;

use App\Models\HeroSectionContent;
use Illuminate\Http\Request;

class HeroSectionController extends Controller
{
    /**
     * GET /api/landing/hero (Public)
     * Retrieve the active hero section content.
     */
    public function show()
    {
        // Singleton pattern: get the first record, or create with defaults if none exists.
        $hero = HeroSectionContent::firstOrCreate([], HeroSectionContent::defaults());

        return response()->json([
            'success' => true,
            'message' => 'Hero section content retrieved successfully',
            'data'    => [
                'title_prefix'    => $hero->title_prefix,
                'title_highlight' => $hero->title_highlight,
                'title_suffix'    => $hero->title_suffix,
                'subtitle'        => $hero->subtitle,
            ],
            'errors'  => null,
        ]);
    }

    /**
     * PUT /api/admin/landing/hero (Admin Only)
     * Update the hero section content.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'title_prefix'    => 'required|string|max:255',
            'title_highlight' => 'required|string|max:255',
            'title_suffix'    => 'required|string|max:255',
            'subtitle'        => 'required|string',
        ]);

        // Singleton pattern: update the first record, or create it if it doesn't exist.
        $hero = HeroSectionContent::firstOrCreate([], HeroSectionContent::defaults());
        $hero->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Hero section content updated successfully',
            'data'    => [
                'title_prefix'    => $hero->title_prefix,
                'title_highlight' => $hero->title_highlight,
                'title_suffix'    => $hero->title_suffix,
                'subtitle'        => $hero->subtitle,
            ],
            'errors'  => null,
        ]);
    }

    /**
     * POST /api/admin/landing/hero/reset (Admin Only)
     * Reset the hero section content back to the system defaults.
     */
    public function reset()
    {
        $defaults = HeroSectionContent::defaults();

        $hero = HeroSectionContent::firstOrCreate([], $defaults);
        $hero->update($defaults);

        return response()->json([
            'success' => true,
            'message' => 'Hero section content has been reset to default successfully',
            'data'    => [
                'title_prefix'    => $hero->title_prefix,
                'title_highlight' => $hero->title_highlight,
                'title_suffix'    => $hero->title_suffix,
                'subtitle'        => $hero->subtitle,
            ],
            'errors'  => null,
        ]);
    }
}
