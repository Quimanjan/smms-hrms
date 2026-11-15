<?php

namespace App\Support;

/**
 * Illustrated, animated avatars drawn from an employee's own record.
 *
 * - Gender (from the employee's Gender field) decides hair and face details.
 * - Job (from Position, Role Type and Department) decides the outfit, the
 *   headwear and a small prop: a book for teachers, a stethoscope for the
 *   nurse, a cap and wrench for maintenance, a badge for security, an ID
 *   lanyard for office staff.
 * - Skin tone, hair style and small details are picked from the person's
 *   name, so the same person always gets the same avatar.
 *
 * The SVG is plain markup; the movement (breathing, head sway, blinking and
 * a job-specific motion) comes from the av-* rules in partials/app-theme.
 */
class Avatar
{
    public const JOBS = ['teacher', 'nurse', 'maintenance', 'guard', 'finance', 'leader', 'office'];

    private const SKIN = ['#f3cfae', '#e6b48a', '#d29a6c', '#b97c51'];
    private const HAIR = ['#1d1411', '#2a1b15', '#3b261b', '#151010'];

    /** Picks the job group from the employee's record. */
    public static function job(?string $position, ?string $roleType = null, ?string $department = null): string
    {
        $text = strtolower(trim(($position ?? '').' '.($roleType ?? '').' '.($department ?? '')));
        $pos = strtolower((string) $position);

        return match (true) {
            (bool) preg_match('/nurse|clinic|medical/', $text) => 'nurse',
            (bool) preg_match('/guard|security/', $text) => 'guard',
            (bool) preg_match('/maint|grounds|utility|janitor|custodian|driver|technician|carpenter|electrician/', $text) => 'maintenance',
            (bool) preg_match('/principal|director|president|administrator|head|dean|coordinator/', $pos) => 'leader',
            (bool) preg_match('/teacher|faculty|instructor|professor|librarian|adviser|teaching staff/', $text) => 'teacher',
            (bool) preg_match('/account|cashier|finance|bookkeep|payroll|bursar/', $text) => 'finance',
            default => 'office',
        };
    }

    public static function gender(?string $gender): string
    {
        $g = strtolower(trim((string) $gender));

        return match (true) {
            in_array($g, ['female', 'f', 'woman'], true) => 'female',
            in_array($g, ['male', 'm', 'man'], true) => 'male',
            default => 'neutral',
        };
    }

    /** SVG for an employee-like object (anything with name, gender, position, role_type, department). */
    public static function for(object $person, string $label = ''): string
    {
        // The panel asked for initials instead of illustrated avatars.
        return self::initials((string) ($person->name ?? ''), $label);
    }

    /** First letters of the first and last name, e.g. "Juan Carlos Dela Cruz" => "JC". */
    public static function initialsOf(string $name): string
    {
        $suffixes = ['jr', 'jr.', 'sr', 'sr.', 'ii', 'iii', 'iv'];
        $words = array_values(array_filter(
            preg_split('/\s+/', trim($name)) ?: [],
            fn ($w) => $w !== '' && ! in_array(strtolower($w), $suffixes, true)
        ));

        if (! $words) {
            return '?';
        }

        $first = mb_substr($words[0], 0, 1);
        $last = count($words) > 1 ? mb_substr(end($words), 0, 1) : '';

        return mb_strtoupper($first.$last);
    }

    /** A round badge with the person's initials in school colors. */
    public static function initials(string $name, string $label = ''): string
    {
        $palettes = [
            ['#4a0a12', '#7a1420', '#e6cf9a'],
            ['#a5121e', '#6e0c15', '#fff4dc'],
            ['#c9a24b', '#9c7a2c', '#33060c'],
            ['#5b2a33', '#33060c', '#e6cf9a'],
            ['#8a3b2b', '#5a1f17', '#fbe9d7'],
        ];
        $seed = crc32(strtolower(trim($name)) ?: 'smms');
        [$from, $to, $ink] = $palettes[$seed % count($palettes)];
        $id = 'ini'.substr(md5($name), 0, 8);
        $text = e(self::initialsOf($name));
        $title = e($label !== '' ? $label : $name);
        $size = mb_strlen(self::initialsOf($name)) > 1 ? 44 : 52;

        return <<<SVG
<svg class="av av--initials" viewBox="0 0 120 120" role="img" aria-label="{$title}" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="{$id}" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{$from}"/><stop offset="1" stop-color="{$to}"/></linearGradient></defs><circle cx="60" cy="60" r="60" fill="url(#{$id})"/><circle cx="60" cy="60" r="55" fill="none" stroke="{$ink}" stroke-opacity="0.28" stroke-width="1.5"/><text x="60" y="61" text-anchor="middle" dominant-baseline="central" font-size="{$size}" fill="{$ink}" class="av-initials">{$text}</text></svg>
SVG;
    }

    /** Illustrated avatar (kept for reference; not used since the panel asked for initials). */
    public static function illustrated(object $person, string $label = ''): string
    {
        return self::svg(
            (string) ($person->name ?? ''),
            $person->gender ?? null,
            $person->position ?? null,
            $person->role_type ?? null,
            $person->department ?? null,
            $label,
        );
    }

    public static function svg(string $name, ?string $gender, ?string $position, ?string $roleType = null, ?string $department = null, string $label = ''): string
    {
        $job = self::job($position, $roleType, $department);
        $sex = self::gender($gender);
        $seed = crc32(strtolower(trim($name)) ?: 'smms');

        $skin = self::SKIN[$seed % count(self::SKIN)];
        $skinShade = self::shade($skin, -18);
        $hair = self::HAIR[intdiv($seed, 7) % count(self::HAIR)];
        $variant = intdiv($seed, 13) % 3;
        $glasses = $job === 'teacher' ? ($seed % 2 === 0) : ($job === 'finance' && $seed % 3 === 0);
        $delay = number_format(($seed % 40) / 10, 1);
        $id = 'av'.substr(md5($name.$job.$sex), 0, 8);

        [$bgA, $bgB] = match ($job) {
            'teacher' => ['#fbe9c6', '#f1cf8a'],
            'nurse' => ['#dff3ef', '#b7e3da'],
            'maintenance' => ['#e3efd9', '#bcd8a6'],
            'guard' => ['#dde4f2', '#aebbd8'],
            'finance' => ['#e6efe9', '#b9d3c4'],
            'leader' => ['#5a0d17', '#2f060c'],
            default => ['#f8e3e3', '#e9b9b9'],
        };

        $title = htmlspecialchars($label !== '' ? $label : 'Avatar', ENT_QUOTES);

        $parts = [];
        $parts[] = "<defs><linearGradient id=\"{$id}bg\" x1=\"0\" y1=\"0\" x2=\"0\" y2=\"1\"><stop offset=\"0\" stop-color=\"{$bgA}\"/><stop offset=\"1\" stop-color=\"{$bgB}\"/></linearGradient>"
            ."<clipPath id=\"{$id}c\"><circle cx=\"60\" cy=\"60\" r=\"60\"/></clipPath></defs>";
        $parts[] = "<circle cx=\"60\" cy=\"60\" r=\"60\" fill=\"url(#{$id}bg)\"/>";
        if ($job === 'leader') {
            $parts[] = '<circle cx="60" cy="60" r="52" fill="none" stroke="#c9a24b" stroke-opacity=".55" stroke-width="1.2" class="av-halo"/>';
        }
        $parts[] = "<g clip-path=\"url(#{$id}c)\"><g class=\"av-body\">";

        // Long hair sits behind the head and shoulders.
        if ($sex === 'female' && $variant !== 2) {
            $parts[] = "<path d=\"M37 50 C34 76 36 92 44 100 L76 100 C84 92 86 76 83 50 C80 32 40 32 37 50Z\" fill=\"{$hair}\"/>";
        }

        $parts[] = self::outfit($job, $sex, $seed, $skin);

        // Neck
        $parts[] = "<path d=\"M52 70 L52 82 Q60 88 68 82 L68 70Z\" fill=\"{$skinShade}\"/>";

        // Head group (sways)
        $parts[] = '<g class="av-head">';
        $parts[] = "<ellipse cx=\"39.5\" cy=\"55\" rx=\"4\" ry=\"5.5\" fill=\"{$skinShade}\"/><ellipse cx=\"80.5\" cy=\"55\" rx=\"4\" ry=\"5.5\" fill=\"{$skinShade}\"/>";
        $parts[] = "<ellipse cx=\"60\" cy=\"52\" rx=\"20.5\" ry=\"23\" fill=\"{$skin}\"/>";
        $parts[] = self::hair($sex, $variant, $hair, $job);

        // Face
        $parts[] = "<path d=\"M47 46 q5 -3 9 0\" stroke=\"{$hair}\" stroke-width=\"1.8\" fill=\"none\" stroke-linecap=\"round\"/>"
            ."<path d=\"M64 46 q5 -3 9 0\" stroke=\"{$hair}\" stroke-width=\"1.8\" fill=\"none\" stroke-linecap=\"round\"/>";
        $parts[] = "<g class=\"av-eyes\" style=\"animation-delay:{$delay}s\"><ellipse cx=\"51.5\" cy=\"53\" rx=\"2.4\" ry=\"2.8\" fill=\"#231815\"/><ellipse cx=\"68.5\" cy=\"53\" rx=\"2.4\" ry=\"2.8\" fill=\"#231815\"/>"
            .'<circle cx="52.3" cy="52" r=".8" fill="#fff"/><circle cx="69.3" cy="52" r=".8" fill="#fff"/></g>';
        if ($sex === 'female') {
            $parts[] = '<path d="M48.6 50.6 l-1.6 -1.2 M71.4 50.6 l1.6 -1.2" stroke="#231815" stroke-width="1" stroke-linecap="round"/>';
        }
        $parts[] = '<ellipse cx="46.5" cy="60.5" rx="3.6" ry="2.2" fill="#e8796f" opacity=".28"/><ellipse cx="73.5" cy="60.5" rx="3.6" ry="2.2" fill="#e8796f" opacity=".28"/>';
        $parts[] = "<path d=\"M58.5 56.5 q1.5 2.2 3 0\" stroke=\"{$skinShade}\" stroke-width=\"1.4\" fill=\"none\" stroke-linecap=\"round\"/>";
        $parts[] = '<path class="av-smile" d="M53.5 63.5 q6.5 5.5 13 0" stroke="#7a2e2a" stroke-width="2" fill="none" stroke-linecap="round"/>';
        if ($sex === 'female') {
            $parts[] = '<circle cx="39.5" cy="61" r="1.6" fill="#e6cf9a"/><circle cx="80.5" cy="61" r="1.6" fill="#e6cf9a"/>';
        }
        if ($glasses) {
            $parts[] = '<g class="av-glasses" fill="none" stroke="#3a2a22" stroke-width="1.4"><rect x="45" y="48.5" width="13" height="10" rx="4"/><rect x="62" y="48.5" width="13" height="10" rx="4"/><path d="M58 52.5 h4"/></g>'
                .'<path class="av-glint" d="M47 50 l3 -1.6" stroke="#fff" stroke-width="1.2" stroke-linecap="round" opacity=".8"/>';
        }
        $parts[] = self::headwear($job, $sex, $hair);
        $parts[] = '</g>'; // head

        $parts[] = self::prop($job, $sex, $skin);
        $parts[] = '</g></g>';

        return "<svg class=\"av av--{$job}\" viewBox=\"0 0 120 120\" role=\"img\" aria-label=\"{$title}\" xmlns=\"http://www.w3.org/2000/svg\"><title>{$title}</title>"
            .implode('', $parts).'</svg>';
    }

    private static function outfit(string $job, string $sex, int $seed, string $skin): string
    {
        $shoulders = 'M14 124 C14 96 34 82 60 82 C86 82 106 96 106 124Z';

        return match ($job) {
            'teacher' => (function () use ($shoulders, $sex, $seed) {
                $blazer = ['#7a1622', '#2f3b5c', '#4a3d33'][$seed % 3];
                $out = "<path d=\"{$shoulders}\" fill=\"{$blazer}\"/>";
                $out .= '<path d="M49 82 L60 104 L71 82 Q60 88 49 82Z" fill="#fff"/>';
                $out .= $sex === 'male'
                    ? '<path d="M58 88 L62 88 L63.5 104 L60 108 L56.5 104Z" fill="#a5121e"/>'
                    : '<path d="M53 86 Q60 96 67 86" stroke="#e6cf9a" stroke-width="1.6" fill="none"/><circle cx="60" cy="93" r="1.8" fill="#e6cf9a"/>';
                $out .= "<path d=\"M44 84 L56 112 L50 124 L36 124Z\" fill=\"".self::shade($blazer, -14)."\" opacity=\".55\"/><path d=\"M76 84 L64 112 L70 124 L84 124Z\" fill=\"".self::shade($blazer, -14)."\" opacity=\".55\"/>";

                return $out;
            })(),
            'nurse' => "<path d=\"{$shoulders}\" fill=\"#f7fbfa\"/>"
                .'<path d="M50 82 L60 96 L70 82" stroke="#bfe3db" stroke-width="2.4" fill="none"/>'
                .'<g class="av-cross"><rect x="73" y="98" width="12" height="12" rx="2.5" fill="#fff" stroke="#e7d6d6"/><path d="M79 101 v6 M76 104 h6" stroke="#c1121f" stroke-width="2.4" stroke-linecap="round"/></g>',
            'maintenance' => "<path d=\"{$shoulders}\" fill=\"#3f6b3a\"/>"
                .'<path d="M50 82 L60 92 L70 82" fill="#355c31"/><path d="M54 86 L60 92 L66 86 L66 100 L54 100Z" fill="#2c4d29" opacity=".6"/>'
                .'<rect x="70" y="100" width="14" height="10" rx="2" fill="#2c4d29"/><path d="M72 104 h10" stroke="#e6cf9a" stroke-width="1.4"/>',
            'guard' => "<path d=\"{$shoulders}\" fill=\"#24345a\"/>"
                .'<path d="M50 82 L60 94 L70 82" fill="#e9eef8"/><path d="M58.5 90 L61.5 90 L62.5 108 L60 111 L57.5 108Z" fill="#111827"/>'
                .'<path d="M22 100 L40 92" stroke="#c9a24b" stroke-width="2"/><path d="M98 100 L80 92" stroke="#c9a24b" stroke-width="2"/>'
                .'<g class="av-badge"><path d="M74 98 l5 -3 l5 3 v6 q-5 5 -10 0Z" fill="#c9a24b"/><path d="M76.5 100.5 l2.5 -1.5 l2.5 1.5" stroke="#fff" stroke-width=".9" fill="none" class="av-shine"/></g>',
            'finance' => "<path d=\"{$shoulders}\" fill=\"".(['#2d4a3e', '#3b3f4a', '#5b2a33'][$seed % 3])."\"/>"
                .'<path d="M49 82 L60 100 L71 82 Q60 88 49 82Z" fill="#f4f1ec"/>'
                .($sex === 'male' ? '<path d="M58 88 L62 88 L63 102 L60 105 L57 102Z" fill="#c9a24b"/>' : '<circle cx="60" cy="92" r="2" fill="#c9a24b"/>'),
            'leader' => "<path d=\"{$shoulders}\" fill=\"#1d1a22\"/>"
                .'<path d="M49 82 L60 104 L71 82 Q60 88 49 82Z" fill="#fff"/>'
                .($sex === 'female' ? '<path d="M52 86 Q60 98 68 86" stroke="#c9a24b" stroke-width="2" fill="none"/><circle cx="60" cy="96" r="2.4" fill="#c9a24b"/>' : '<path d="M58 88 L62 88 L63.5 105 L60 109 L56.5 105Z" fill="#c9a24b"/>')
                .'<path d="M44 84 L56 112 L50 124 L36 124Z" fill="#0f0d12" opacity=".7"/><path d="M76 84 L64 112 L70 124 L84 124Z" fill="#0f0d12" opacity=".7"/>'
                .'<circle cx="80" cy="96" r="2.2" fill="#c9a24b" class="av-shine"/>',
            default => "<path d=\"{$shoulders}\" fill=\"".(['#8b1d2c', '#3d4b6b', '#6b4b3a'][$seed % 3])."\"/>"
                .'<path d="M50 82 L60 94 L70 82 Q60 87 50 82Z" fill="#fff"/>',
        };
    }

    private static function hair(string $sex, int $variant, string $hair, string $job): string
    {
        // Caps cover most of the hair for these jobs.
        $capped = in_array($job, ['maintenance', 'guard'], true);

        if ($sex === 'female') {
            $bangs = "<path d=\"M39 50 C38 30 52 25 62 26 C76 27 84 36 81 52 C76 42 68 38 60 38 C52 40 45 44 39 50Z\" fill=\"{$hair}\"/>";

            return match ($variant) {
                2 => "<circle cx=\"60\" cy=\"25\" r=\"8.5\" fill=\"{$hair}\" class=\"av-bun\"/>".$bangs,
                1 => $bangs."<path d=\"M42 40 C46 34 52 33 56 36 C50 37 45 40 42 46Z\" fill=\"".self::shade($hair, 12)."\" opacity=\".6\"/>",
                default => $bangs,
            };
        }

        if ($capped) {
            return "<path d=\"M40 50 C40 40 46 34 60 34 C74 34 80 40 80 50 L80 44 C78 36 70 31 60 31 C50 31 42 36 40 44Z\" fill=\"{$hair}\"/>";
        }

        return match ($variant) {
            0 => "<path d=\"M39.5 52 C37 34 48 26 61 26 C74 26 84 33 80.5 52 C78 44 74 40 68 38 C62 41 50 41 44 40 C41 43 40 47 39.5 52Z\" fill=\"{$hair}\"/>",
            1 => "<path d=\"M39.5 50 C38 33 50 25 62 26 C75 27 83 35 80.5 50 C77 41 70 36 58 36 C50 36 44 41 39.5 50Z\" fill=\"{$hair}\"/><path d=\"M52 30 q10 -5 20 2\" stroke=\"".self::shade($hair, 14)."\" stroke-width=\"2\" fill=\"none\" opacity=\".7\"/>",
            default => "<path d=\"M40 48 C40 34 49 28 60 28 C71 28 80 34 80 48 C76 40 68 36 60 36 C52 36 44 40 40 48Z\" fill=\"{$hair}\"/>",
        };
    }

    private static function headwear(string $job, string $sex, string $hair): string
    {
        return match ($job) {
            'nurse' => $sex === 'female'
                ? '<g class="av-hat"><path d="M46 31 L50 22 L70 22 L74 31 Q60 35 46 31Z" fill="#fff" stroke="#d9e6e3"/><path d="M60 24 v6 M57 27 h6" stroke="#c1121f" stroke-width="1.8" stroke-linecap="round"/></g>'
                : '',
            'maintenance' => '<g class="av-hat"><path d="M38 44 C38 30 48 25 60 25 C72 25 82 30 82 44 Q60 39 38 44Z" fill="#c9a24b"/><path d="M60 41.5 C70 41 84 42 90 46 Q76 48 60 45Z" fill="#a8842f"/><circle cx="60" cy="26.5" r="1.6" fill="#a8842f"/></g>',
            'guard' => '<g class="av-hat"><path d="M38 40 C40 28 50 24 60 24 C70 24 80 28 82 40 Q60 36 38 40Z" fill="#24345a"/><path d="M36 42 Q60 35 84 42 L84 45 Q60 40 36 45Z" fill="#111827"/><path d="M56 30 h8 v6 h-8Z" fill="#c9a24b" class="av-shine"/></g>',
            default => '',
        };
    }

    private static function prop(string $job, string $sex, string $skin): string
    {
        return match ($job) {
            'teacher' => '<g class="av-prop av-book"><rect x="20" y="96" width="24" height="18" rx="2" fill="#a5121e" transform="rotate(-10 32 105)"/><rect x="22" y="98" width="20" height="14" rx="1.5" fill="#fff7e6" transform="rotate(-10 32 105)"/><path d="M26 101 h12 M26 105 h10 M26 109 h11" stroke="#d8c9a8" stroke-width="1" transform="rotate(-10 32 105)"/>'
                ."<ellipse cx=\"33\" cy=\"114\" rx=\"6\" ry=\"4\" fill=\"{$skin}\"/></g>",
            'nurse' => '<g class="av-prop"><path d="M47 84 C44 96 50 104 58 104" stroke="#4b5563" stroke-width="2" fill="none"/><path d="M73 84 C76 96 70 104 62 104" stroke="#4b5563" stroke-width="2" fill="none"/><circle cx="60" cy="106" r="3.4" fill="#9ca3af" stroke="#4b5563" stroke-width="1.2" class="av-pulse"/></g>',
            'maintenance' => '<g class="av-prop av-wrench"><path d="M22 116 L36 100" stroke="#9aa3ad" stroke-width="4.5" stroke-linecap="round"/><path d="M34 96 a6 6 0 1 0 6 6 l-3 -1 l-1 -3 Z" fill="#9aa3ad"/></g>',
            'finance' => '<g class="av-prop av-calc"><rect x="20" y="98" width="18" height="22" rx="3" fill="#33373f"/><rect x="23" y="101" width="12" height="5" rx="1" fill="#b9e4c9"/><g fill="#c9a24b"><circle cx="25.5" cy="110" r="1.3"/><circle cx="29" cy="110" r="1.3"/><circle cx="32.5" cy="110" r="1.3"/><circle cx="25.5" cy="114.5" r="1.3"/><circle cx="29" cy="114.5" r="1.3"/><circle cx="32.5" cy="114.5" r="1.3"/></g></g>',
            'office' => '<g class="av-prop av-lanyard"><path d="M50 83 L58 104 M70 83 L62 104" stroke="#a5121e" stroke-width="2.2"/><rect x="54" y="103" width="12" height="15" rx="2" fill="#fff" stroke="#e5d7d7"/><rect x="56.5" y="106" width="7" height="5" rx="1" fill="#e9b9b9"/><path d="M56.5 113.5 h7" stroke="#c9a24b" stroke-width="1.2"/></g>',
            default => '',
        };
    }

    private static function shade(string $hex, int $amount): string
    {
        $hex = ltrim($hex, '#');
        $rgb = array_map(fn ($c) => max(0, min(255, hexdec($c) + $amount)), str_split($hex, 2));

        return sprintf('#%02x%02x%02x', ...$rgb);
    }
}
