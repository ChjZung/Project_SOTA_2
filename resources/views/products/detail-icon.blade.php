<svg viewBox="0 0 48 48" aria-hidden="true">
    @switch($type)
        @case('leaf')<path d="M38 8C18 6 8 15 10 28c2 12 18 13 25 2 4-7 3-14 3-22Z M10 39 31 17 M17 30v-9 M24 24h10"/>@break
        @case('options')<path d="m24 5 17 10v19L24 44 7 34V15L24 5Zm0 20 17-10M24 25 7 15m17 10v19M15 10l18 10"/>@break
        @case('label')<path d="M10 6h28v36H10z M16 15h16M16 23h16M16 31h10"/>@break
        @default<path d="M24 5C17 15 10 22 10 29a14 14 0 0 0 28 0c0-7-7-14-14-24Z M17 29a7 7 0 0 0 7 7"/>
    @endswitch
</svg>
