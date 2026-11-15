<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Homepage text that HR and Admin edit on the Homepage page.
 * Every field has a default, so the site looks complete before anyone edits it.
 */
final class SiteContent
{
    /** key => [label, default, rows (1 = single line), group] */
    public const FIELDS = [
        'hero_place' => ['Location line', 'Poblacion, Kawit, Cavite', 1, 'Top of the page'],
        'hero_title' => ['Headline', 'Teach, serve, and grow at St. Mary Magdalene School', 1, 'Top of the page'],
        'hero_lede' => ['Introduction', "This is the school's human resource portal. Apply for an open position, or log in to see your attendance, leave, and payslips.", 3, 'Top of the page'],

        'history_p1' => ['History, first paragraph', 'St. Mary Magdalene School (Parochial), Inc. was established in 1961 when Msgr. Eulogio San Juan, the Kawit Parish Priest, envisioned setting up a kindergarten class, which at that time was not being offered by any school in the town. With the help of Mr. Angel S. Colet, the convent became the first classroom, and Mrs. Angelita Quiamson Samaniego served as the pioneer teacher for fifteen enrolled pupils. In 1963, the Department of Education granted the Kindergarten class a permit to operate.', 6, 'Our history'],
        'history_p2' => ['History, second paragraph', "The name St. Mary Magdalene School was adopted in honor of the town's patroness. March 11, 1966 was designated as the school's Foundation Day, coinciding with the celebration of Msgr. Eulogio San Juan's birthday. On this day, we are reminded of how the school was formed and how far we have come as a school community.", 6, 'Our history'],
        'timeline' => ['Key dates (one per line, as: year | what happened)', "1961 | A kindergarten class opens in the parish convent with fifteen pupils.\n1963 | The Department of Education grants the class its permit to operate.\n1966 | March 11 is designated as the school's Foundation Day.", 5, 'Our history'],

        'careers_title' => ['Call to action headline', 'Be a Magdalenean', 1, 'Careers banner'],
        'careers_text' => ['Call to action text', 'See which teaching and non-teaching positions are open, then apply online.', 2, 'Careers banner'],

        'address' => ['Address', 'Poblacion, Kawit, Cavite', 1, 'Contact details'],
        'phone' => ['Phone number', '(+63) 967-239-9400', 1, 'Contact details'],
        'email' => ['Email address', 'communications@smms.ph.education', 1, 'Contact details'],
        'facebook' => ['Facebook page link', 'https://www.facebook.com/SMMSchool1963', 1, 'Contact details'],
    ];

    public static function all(): array
    {
        $saved = Cache::remember('site_contents', 3600, function () {
            try {
                return DB::table('site_contents')->pluck('value', 'key')->all();
            } catch (Throwable) {
                return [];
            }
        });

        $out = [];
        foreach (self::FIELDS as $key => [, $default]) {
            $value = $saved[$key] ?? null;
            $out[$key] = filled($value) ? $value : $default;
        }

        return $out;
    }

    public static function get(string $key): string
    {
        return (string) (self::all()[$key] ?? '');
    }

    /** Timeline lines "1961 | text" as [[year, text], ...]. */
    public static function timeline(): array
    {
        return collect(preg_split('/\r\n|\r|\n/', self::get('timeline')))
            ->map(fn ($line) => array_map('trim', explode('|', $line, 2)))
            ->filter(fn ($p) => count($p) === 2 && $p[0] !== '' && $p[1] !== '')
            ->values()
            ->all();
    }

    public static function forget(): void
    {
        Cache::forget('site_contents');
    }
}
