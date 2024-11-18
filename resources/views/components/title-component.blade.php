<div id="titleComponent" class="text-lg font-bold flex items-center justify-between w-full px-4 space-x-2 relative" >
    <div class="flex items-center space-x-2">
        <img src="{{ asset('icons/new-item.svg') }}" alt="Upload Icon" style="height: 33%; padding-right: 18px;">
        <span>{{$slot}}</span>
    </div>
    <div class="relative flex items-center">

        <!-- <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor" onclick="toggleMenu()">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6h.01M12 12h.01M12 18h.01" />
        </svg> -->


    
        <!-- @if($showButton)
            <a href="{{$urlButton}}" class="flex items-center justify-center w-full py-3 rounded-lg font-semibold transition mb-6 bg-#5570F1" style="box-shadow: 0px 19px 34px -20px #43BBED; background-color: #5570F1; color: white; padding: 0px 24px; height: 45px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; margin-top: 28px;">
                {{ $textButton }}
            </a>
        @endif -->

        <!-- <div id="dropdownMenu" class="hidden absolute right-0 mt-8 w-32 bg-white border border-gray-200 rounded shadow-lg">
            <a href="new-user" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Adicioanr</a>
            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Option 2</a>
            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Option 3</a>
        </div> -->
    </div>
</div>
<script>
    function toggleMenu() {
        const dropdownMenu = document.getElementById("dropdownMenu");
        dropdownMenu.classList.toggle("hidden");
    }

    document.addEventListener("click", function (event) {
        const dropdownMenu = document.getElementById("dropdownMenu");
        const isClickInside = event.target.closest('.relative');
        if (!isClickInside && !dropdownMenu.classList.contains("hidden")) {
            dropdownMenu.classList.add("hidden");
        }
    });
</script>