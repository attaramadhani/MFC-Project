<?php

if (!function_exists('get_menu_image_url')) {
    function get_menu_image_url($filename)
    {
        if (empty($filename)) {
            return 'https://placehold.co/400x400/eeeeee/999999?text=No+Image'; // placeholder if any
        }

        $supabaseUrl = env('SUPABASE_URL');
        $bucket = env('SUPABASE_BUCKET', 'mfc-images');

        // Prioritaskan file lokal (gambar bawaan) jika ada
        if (is_file(public_path('img/' . $filename))) {
            return asset('img/' . $filename);
        }

        if ($supabaseUrl) {
            // Remove trailing slash if any
            $supabaseUrl = rtrim($supabaseUrl, '/');
            return $supabaseUrl . '/storage/v1/object/public/' . $bucket . '/' . $filename;
        }

        return asset('img/' . $filename);
    }
}
