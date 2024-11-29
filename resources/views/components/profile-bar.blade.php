@if (isset(Auth::user()->name))
<div class="flex flex-col">
    <div style="display: flex; padding-top: 5px; align-self: end; padding-bottom: 5px; padding-right: 20px;">
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px;">
            <img src="{{ asset('icons/bell.svg') }}"/>
            <!-- <img src="{{ asset('icons/moon.svg') }}"/> -->
            <img src="{{ asset('icons/gear.svg') }}"/>
            <span style="color: #333;">{{ Auth::user()->name }}</span>
            <div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; border: 1px solid #ccc;">
                <img src="{{ asset('icons/default-photo.png') }}"/>
            </div>
            <a href="logout">
                <img src="{{ asset('icons/logout.svg') }}"/>
            </a>
        </div>
    </div>
</div>
@endif