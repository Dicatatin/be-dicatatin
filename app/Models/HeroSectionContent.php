<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSectionContent extends Model
{
    protected $fillable = [
        'title_prefix',
        'title_highlight',
        'title_suffix',
        'subtitle',
    ];

    /**
     * Default hero section content values.
     * Used when resetting or seeding initial data.
     */
    public static function defaults(): array
    {
        return [
            'title_prefix'     => 'Ubah Catatan Berantakan Menjadi ',
            'title_highlight'  => 'Pengetahuan Terstruktur',
            'title_suffix'     => ' dalam Hitungan Detik',
            'subtitle'         => 'Platform AI yang memahami tulisan tanganmu. Pilih dari 7 metode belajar dan transformasi catatanmu menjadi alat belajar aktif.',
        ];
    }
}
