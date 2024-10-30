<div style="display: flex; align-items: center; gap: 10px; padding: 10px;">
    <img src="{{ asset('icons/bell.svg') }}"/>
    <img src="{{ asset('icons/moon.svg') }}"/>
    <img src="{{ asset('icons/info.svg') }}"/>
    <span style="color: #333;">{{ Auth::user()->name }}</span>
    <div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; border: 1px solid #ccc;">
        <img src="https://via.placeholder.com/30" alt="Profile Picture" style="width: 100%; height: 100%;">
    </div>
    <img src="{{ asset('icons/logout.svg') }}"/>
</div>