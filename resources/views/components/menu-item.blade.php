<a href="{{ route($route) }}"
    class="flex items-center justify-center w-full py-3 rounded-lg font-semibold transition mb-6 {{ request()->routeIs($route) ? 'bg-' . $activeBgColor : '' }}"
    style="color: {{ $inactiveColor }}; {{ request()->routeIs($route) ? 'box-shadow: 0px 19px 34px -20px #43BBED; background-color: ' . $activeBgColor . '; color: ' . $activeColor . ';' : '' }}">
    <svg width="29" height="22" viewBox="0 0 29 22" class="mr-2" fill="{{ request()->routeIs($route) ? $activeIconColor : $inactiveIconColor }}" xmlns="http://www.w3.org/2000/svg">
        {{ $slot }}
    </svg>
    {{ $label }}
</a>