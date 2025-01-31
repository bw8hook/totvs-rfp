@if($disableLink == "false")
    @if($alwaysOn == "true")
        <a href="{{ route($route) }}"
            class=" flex items-center justify-center w-full py-3 rounded-lg font-semibold transition mb-6 bg-{{$activeBgColor}}"
            style="color: {{$inactiveColor}}; box-shadow: 0px 19px 34px -20px #43BBED; background-color: {{ $activeBgColor}}; color: {{$activeColor}};">
            <svg width="29"  viewBox="0 0 29 22" class="mr-2" fill="{{ request()->routeIs($route) || request()->is($routePrefix) ? $activeIconColor : $inactiveIconColor }}" xmlns="http://www.w3.org/2000/svg">
                {{ $slot }}
            </svg>
            <div class="menu_texto"> {{ $label }}</div>
        </a>
    @else
        <a href="{{ route($route) }}" class="btn_menu w-full py-1 font-semibold  mb-6 {{ request()->routeIs($route) || request()->is($routePrefix) ? 'menu_active' : '#478' }}" >
            <div class="menu_icon">
                <svg width="29" viewBox="0 0 29 20" class="mr-2" fill="{{ request()->routeIs($route) || request()->is($routePrefix) ? $activeIconColor : $inactiveIconColor }}" xmlns="http://www.w3.org/2000/svg">
                    {{ $slot }}
                </svg>
            </div>
            @if($breakLine == "true")
            
                <div class="menu_texto"> {{ $label }}</div>
            @else
                {{ $label }}
            @endif
        </a>
    @endif
@else
    @if($alwaysOn == "true")
        <div
            style="background: linear-gradient(to right, #aca9ff61, transparent); color: #5570f1;"
            class="btn_menu w-full py-1 font-semibold mb-6 menu_active"
            style="color: {{ $inactiveColor }}; box-shadow: 0px 19px 34px -20px #43BBED; background-color: {{ $activeBgColor}}; color: {{$activeColor}};  opacity: 0.3;">
            <div class="menu_icon">
                <svg width="29" viewBox="0 0 29 20" class="mr-2" fill="{{ request()->routeIs($route) || request()->is($routePrefix) ? $activeIconColor : $inactiveIconColor }}" xmlns="http://www.w3.org/2000/svg">
                    {{ $slot }}
                </svg>
            </div>
            @if($breakLine == "true")
                <div class="menu_texto"> {{ $label }}</div>
            @else
                {{ $label }}
            @endif
        </div>
    @else
        <div class="btn_menu w-full py-1 font-semibold mb-6 " style="color: {{ $inactiveColor }}; opacity: 0.3; ">
            <div class="menu_icon">
                <svg width="29" viewBox="0 0 29 20" class="mr-2" fill="{{ request()->routeIs($route) || request()->is($routePrefix) ? $activeIconColor : $inactiveIconColor }}" xmlns="http://www.w3.org/2000/svg">
                    {{ $slot }}
                </svg>
            </div>
            @if($breakLine == "true")
                <div class="menu_texto"> {{ $label }}</div>
            @else
                {{ $label }}
            @endif
        </div>
    @endif




@endif