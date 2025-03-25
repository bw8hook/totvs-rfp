<x-app-layout>
    <div class="py-4" style=" padding-bottom: 130px;">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">

            <div id="titleComponent" class="text-lg font-bold flex items-center justify-between w-full px-4 space-x-2 relative">
                <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"  style="color: #5570F1;"></path>
                        <circle cx="12" cy="7" r="4"  style="color: #5570F1;"></circle>
                    </svg>
                    <span>Agentes</span>
                </div>
                <div class="relative flex items-center">        
                    <a href="{{ route('agents.create') }}" class="btn_titleComponent" >Cadastrar novo agente </a>
                </div>
            </div>


            <div id="BlocoLista" class="p-2" style="background:#FFF;">

            @if($Itens->isEmpty())
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2000 2000" style="height: 230px; margin: auto;">
                            <rect style="fill:#fff;" width="2000" height="2000"/>
                            <path style="fill:#f2f4f9;" d="M908.14,1563.8A165.39,165.39,0,0,1,757.7,1346L520.56,1380.3a165.37,165.37,0,0,1-97.38-311.71L904.78,829,421,816.46l-2.25-.07a165.37,165.37,0,0,1,10.8-330.56L1578,515.54l2.24.07a165.37,165.37,0,0,1,67.15,313.3L1446.25,929a165.39,165.39,0,0,1,39.39,296.19l-481.16,312.1A164.68,164.68,0,0,1,908.14,1563.8Z" transform="translate(0.5 -0.5)"/>
                            <path style="fill:#c4cfe8;" d="M1381.88,832.74l.22-3.75a119.32,119.32,0,0,0,22.12-.79l.48,3.72A123.36,123.36,0,0,1,1381.88,832.74Zm-22.51-3.1-22.18-3.93.66-3.7,22.17,3.93ZM1315,821.78l-22.17-3.93.65-3.69,22.18,3.92Zm-44.35-7.86L1248.48,810l.65-3.7,22.18,3.93Zm-44.36-7.85-22.18-3.93.66-3.7,22.17,3.93ZM1182,798.21l-22.18-3.93.65-3.69,22.18,3.92Zm-44.36-7.86-22.18-3.92.66-3.7,22.17,3.93Zm-44.36-7.85-22.17-3.93.65-3.7,22.18,3.93ZM1427,826.91l-1.16-3.57a120.16,120.16,0,0,0,20.34-8.75l1.8,3.3A124.11,124.11,0,0,1,1427,826.91Zm-378.09-52.27-22.18-3.93.65-3.69,22.18,3.93Zm-44.36-7.85-22.18-3.93.66-3.7,22.17,3.93Zm-44.36-7.86L938,755l.65-3.7,22.18,3.93Zm-44.35-7.86-22.18-3.93.65-3.69,22.18,3.93Zm-44.36-7.85-22.18-3.93.66-3.7,22.18,3.93Zm-44.35-7.86-22.18-3.93.65-3.69,22.18,3.92Zm-44.36-7.86-22.18-3.92.66-3.7,22.17,3.93Zm-44.36-7.85-22.17-3.93.65-3.7L739,716ZM694,711.79l-22.18-3.93.65-3.69,22.18,3.92Zm-44.36-7.86L627.49,700l.66-3.7,22.18,3.93Zm-44.35-7.85-22.18-3.93.65-3.7L606,692.38ZM561,688.22l-22.18-3.93.66-3.69,22.17,3.93Zm-30.55-5.42a125.23,125.23,0,0,1-21.94-6.31l1.36-3.5a121.65,121.65,0,0,0,21.28,6.12Zm936.51,122.39-2.37-2.91a121.34,121.34,0,0,0,15.85-15.47l2.86,2.43A125,125,0,0,1,1466.92,805.19Zm-978.86-139a123,123,0,0,1-18.17-13.83l2.54-2.76a119.91,119.91,0,0,0,17.62,13.41Zm-33.49-30.75a125.23,125.23,0,0,1-12-19.44l3.36-1.67a121.19,121.19,0,0,0,11.62,18.85ZM1496.39,770.56l-3.26-1.87a119.51,119.51,0,0,0,9.17-20.14l3.55,1.23A123.55,123.55,0,0,1,1496.39,770.56Zm-1062-175.81a124.33,124.33,0,0,1-4.16-22.45L434,572a120.17,120.17,0,0,0,4,21.76Zm1077,132.87-3.71-.57c.1-.63.19-1.27.27-1.9a120,120,0,0,0,1-20.2l3.76-.12a125.35,125.35,0,0,1-1.09,20.83C1511.52,726.31,1511.43,727,1511.33,727.62ZM430.27,549.46c.16-1.69.36-3.41.59-5.1s.48-3.3.77-4.93c.74-4.17,1.7-8.34,2.85-12.4l3.61,1c-1.11,3.94-2,8-2.76,12-.28,1.58-.53,3.18-.75,4.77s-.42,3.32-.57,5ZM1506.18,683a119.83,119.83,0,0,0-6.77-21.08l3.45-1.47a123.45,123.45,0,0,1,7,21.74ZM446.08,507.41l-3.36-1.67a123.56,123.56,0,0,1,12-19.41l3,2.26A119.7,119.7,0,0,0,446.08,507.41Zm1042.81,135a121.83,121.83,0,0,0-13.9-17.24l2.7-2.61A125.74,125.74,0,0,1,1492,640.35Zm-30.71-31.64A119.16,119.16,0,0,0,1439,599.72l1.56-3.41a123.88,123.88,0,0,1,19.79,11.4ZM472.68,472.24l-2.53-2.78a124.56,124.56,0,0,1,18.22-13.76l2,3.19A120.73,120.73,0,0,0,472.68,472.24Zm945.43,120.15c-2.22-.56-4.49-1.06-6.75-1.49l-1.61-.3-13.63-2.41.66-3.7,13.62,2.42c.56.09,1.11.2,1.66.3,2.33.45,4.67,1,7,1.54ZM1374,584.26l-22.18-3.93.65-3.69,22.18,3.93Zm-44.36-7.85-22.18-3.93.66-3.7,22.17,3.93Zm-44.36-7.86-22.17-3.93.65-3.7,22.18,3.93Zm-44.35-7.86-22.18-3.93.65-3.69,22.18,3.93Zm-44.36-7.85-22.18-3.93.66-3.7,22.18,3.93ZM1152.17,545,1130,541.05l.65-3.69,22.18,3.92Zm-44.36-7.86-22.18-3.92.66-3.7,22.17,3.93Zm-44.36-7.85-22.17-3.93.65-3.7,22.18,3.93Zm-44.35-7.86-22.18-3.93.65-3.69,22.18,3.92Zm-44.36-7.86-22.18-3.92.66-3.7,22.18,3.93ZM510.15,449l-1.36-3.5a124.18,124.18,0,0,1,21.95-6.29l.69,3.69A120.5,120.5,0,0,0,510.15,449ZM930.39,505.7l-22.18-3.93.65-3.7L931,502ZM886,497.84l-22.18-3.93.66-3.69,22.17,3.93ZM841.67,490l-22.17-3.92.65-3.7,22.18,3.93Zm-44.35-7.85-22.18-3.93.65-3.7L798,478.43ZM753,474.27l-22.18-3.93.66-3.69,22.18,3.93Zm-44.35-7.85-22.18-3.93.65-3.7,22.18,3.93Zm-44.36-7.86-22.18-3.93.66-3.69,22.17,3.92Zm-44.36-7.86-22.17-3.93.65-3.69L620.55,447Zm-66.42-9.87v-3.76A125.9,125.9,0,0,1,575.31,439l.88.16-.65,3.7-.89-.16A121.16,121.16,0,0,0,553.47,440.83Z" transform="translate(0.5 -0.5)"/>
                            <circle style="fill:#f2f4f9;" cx="1426.23" cy="1440.72" r="71.31" transform="translate(-600.51 1429.97) rotate(-45)"/>
                            <path style="fill:#c4cfe8;" d="M1417.09,1450.1a1.75,1.75,0,0,0,1.5.19,52.34,52.34,0,0,0,19.58-11.5,1.77,1.77,0,0,0,.09-2.51,1.86,1.86,0,0,0-.35-.29,1.8,1.8,0,0,0-2.17.2c-.64.6-1.29,1.18-2,1.74h0l-.12.1h0v0h0l-.09.06h0v0h0l-.07.06h-.06l0,0h-.09l0,0h-.1l0,0h-.23v0h-.11l0,0h-.1l0,0h-.09l-.06,0h-.07l0,0h-.08l-.07,0h-.05l-.09.07h0l-.12.09h0a48.31,48.31,0,0,1-14.39,7.48,1.78,1.78,0,0,0-1.15,2.24,1.73,1.73,0,0,0,.74.95Zm31.3-29.22a1.59,1.59,0,0,0,.29.15,1.77,1.77,0,0,0,2.31-1,51.58,51.58,0,0,0,3.64-22.43,1.78,1.78,0,0,0-.82-1.39,1.77,1.77,0,0,0-2.73,1.6c.08,1.35.1,2.71.07,4.07h0v.14h0v0h0v0h0v0h0v.11h0v0h0v.11h0v0h0v0h0v0h0v.1h0v0h0v.07h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v.05h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v.09h0v0h0v0h0v0h0v0h0v.08h0v0h0v0h0v0h0v0h0a.57.57,0,0,0,0,.13h0v0h0v0h0v.1h0v0h0v0h0a47.89,47.89,0,0,1-1,6.71h0l0,.15h0v0h0v0h0l0,.1h0v0h0v0h0v0h0v0h0l0,.08h0v0h0v0h0v0h0v0h0l0,.08h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0l0,.07h0l0,.11h0v0h0l0,.11h0v0h0v0h0a47.5,47.5,0,0,1-1.79,5.32,1.78,1.78,0,0,0,.7,2.16Zm-2.51-42.75a1.76,1.76,0,0,0,1.8.07,1.79,1.79,0,0,0,.72-2.41,51.73,51.73,0,0,0-14.94-17.12l-.1-.06a1.78,1.78,0,0,0-2,2.93c1,.69,1.86,1.41,2.74,2.16h0a.52.52,0,0,0,.11.1h0l0,0h0l.09.08h0v0h0l.06.06h.07v0h0l.06.05h0l0,0h0l0,0h0l0,0h.09l0,0h.1v0h0l0,0h.52l0,0h.12l0,0h.08l0,0h.1l0,0h.1l0,0h.11l0,0h.08l0,0h.06v0h0l.07.06h.07l0,0h0v0h0l.07.07h0v0h0l.06.07h0v0h0v0h0l.07.06h0v0h0l0,0h0v0h.07l.07.07h0v0h0l.07.08h0v0h0l.06.06h0v0h0l.06.07h.06l.07.08h.07l.06.07h.07l.06.07h0v0h0l.07.07h0v0h0l.06.06h0v0h.06l.06.06h.06l.05.06h.08l0,0h.06l.07.08h0l0,0h0l0,.06h.07l.05.06h.08l0,.06h.08l.05.06h.07l0,0h.09l0,0h.09l0,0h.45v0h.08v0h0l0,0h0v0h.06v0h0l0,0h0v0h.06v0h0l0,0h0v0h.05v0h0l0,0h0v0h0l0,.06h0v0h0v0h0l0,0h0v0h0v0h0v0h0l.06.08h0v0h0v0h0l.07.1h0v0h0a47.3,47.3,0,0,1,4.07,6.3,1.71,1.71,0,0,0,.61.64Zm-56.43-24.8a1.78,1.78,0,0,0,1.38.22,48.83,48.83,0,0,1,18.55-1h1.24l.08,0h.06l.13,0h0l1.1.2a1.77,1.77,0,0,0,1.29-3.24,1.74,1.74,0,0,0-.61-.25,52.7,52.7,0,0,0-22.7.58,1.78,1.78,0,0,0-.52,3.23Zm-33.2,27a.77.77,0,0,0,.18.1,1.77,1.77,0,0,0,2.37-.82l.34-.69h0v0h0v0h0l0-.05h0v0h0v0h.06v0h0v0h0v0h0v0h0v0h0v0h0l0,0h0v0h0v0h0v0h0v0h0l0-.06h0v0h0v0h0v0h0l0-.08h0l.06-.11h0c.3-.56.6-1.11.92-1.65h0l.06-.12h0l.06-.1h0l.06-.09h0v0h0v0h0l0-.08h0v0h0v0h0v0h0l0,0h0l0-.06h0v0h0v0h0v0h0v0h0l0,0h0v0h0l0,0h0v0h0v0h0l0,0h0v0h.1v0h0v0h0v0h0v0h0v0h0v0h.07v0h0v0h0v0h0v0h0v0h0v0h0l0,0h0l0,0h0v0h0v0h0l0,0h0v0h0v0h0l0,0h0l0,0h0l0-.05h0l0,0h0v0h0v0h0l0,0h0v0h0v0h0l0,0h0l0,0h0v0h0l0,0h0l0,0h0v0h0v0h0v0h0l0,0h0v0h0v0h0l0,0h0v0h0v0h0v0h0l0,0h0v0h0v0h0l0,0h0l0,0h0v0h0v0h0l0,0h0l0,0h0v0h0v0h0l0,0h0l0,0h0v0h0v0h0l0,0h0l0,0h0v0h0l0,0h0l0,0h0l0,0h0v0h0v0h0l0,0h0l0,0h0v0h0v0h0l0,0h0l0,0h0l0,0h0v0h0l0,0h0l0,0h0l0,0h0l0-.05h0l0,0h0l0,0h0v0h0v0h0l0,0h0l0,0h0l0,0h0v0h0l0-.05h0l0,0h0l0,0h0v0h0l0,0h0l0,0h0l0,0h0v0h0v0h0l0,0h0l0,0h0l0,0h0v0h0v0h0l0,0h0l0,0h0l0,0h0v0h0v0h0l0,0h0l0,0h0l0,0h0l0,0h0v0h0v0h0l0,0h0l0,0h0l0,0h0l0,0h0v0h0v0h0l0,0h0l0,0h0l0,0h0v0h0v0h0l0,0h0l0,0h0l0,0h0l0,0h0l0,0h0v0h0v0h0l0,0h0l0,0h0l0,0h0v0h0v0h0l0,0h0l0,0h0l0,0h0l0,0h0l0,0h0v0h0l0,0h0l0,0h0l0,0h0l0,0h0v0h0v0h0l0,0h0l0,0h0l0,0h0l0,0h0l0,0h0l0,0h0l0,0h0l0,0h0l0,0h0l0,0h0l0,0h0l0,0h0l0,0h0l0,0h0l0,0h0l0,0h0l0-.05h0l0,0h0l0,0h0l0,0h0l0,0h0l0,0h0l0-.05h0l0,0h0l0-.07h0l0,0h0l0,0h0l0-.05h0l0,0h0l.06-.07h0l0,0h0l.06-.07h0l0,0h0l0-.07h0l0,0h0l.06-.07h0l0,0h0l.06-.06h0l0,0h0l.1-.12h0l0,0h0l.06-.07h0l.1-.12h0a48.29,48.29,0,0,1,6-5.86,1.78,1.78,0,0,0,.25-2.5,1.69,1.69,0,0,0-.42-.38,1.79,1.79,0,0,0-2.08.12,52.25,52.25,0,0,0-11.06,12.46,53.33,53.33,0,0,0-3,5.38,1.76,1.76,0,0,0,.65,2.27Zm-.39,42.82a1.78,1.78,0,0,0,2.57-2.24h0l0-.05h0v0h0l0,0h0v0h0l0,0h0l0,0h0l0,0h0v0h0v0h-.1l0,0h0v0h0l0,0h0v0h0l0,0h0v0h0v0h0l0,0h0v0h0l0,0h0l0-.06h0v0h0l0,0h0v0h0v0h0l0-.06h0v0h0l0-.06h0v0h0l0-.06h0v0h0l0-.06h0l0-.06h0l0,0h0v0h0v0h0l0-.06h0v0h0v0h0l0-.05h0v0h0v0h0l0-.06h0v0h0l0-.06h0v0h0v0h0l0-.07h0v0h0v0h0l0-.07h0v0h0v0h0l0-.06h0v0h0v0h0l0-.08h0v0h0l0-.1h0l0-.14h0v0h0l0-.1h0l-.19-.55h0v0h0v0h0a48.8,48.8,0,0,1-1.82-7.64h0l0-.13h0v0h0l0-.1h0v0h0v0h0v-.09h0v0h0v-.09h0v0h0v0h0v0h0v-.09h0v0h0v-.07h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0v0h0V1406h0v0h0v0h0v0h0v-.08h0v-.08h0v0h0v0h0a48.32,48.32,0,0,1-.25-5.38,1.79,1.79,0,0,0-.83-1.51,1.85,1.85,0,0,0-.94-.28,1.79,1.79,0,0,0-1.79,1.77,52.67,52.67,0,0,0,4.75,22.2,1.75,1.75,0,0,0,.66.76Zm18.76,21.49a51.62,51.62,0,0,0,21.35,7.73,1.78,1.78,0,0,0,1.18-3.27,1.94,1.94,0,0,0-.72-.26,47.08,47.08,0,0,1-8.31-1.81h0l-.24-.07h0l-.15,0h0l0,0h0l-.16-.05h-.06l-.12,0h-.06l-.12,0h-.07l-.14,0h-.1l-.08,0h-.08l-.11,0h-.08l-.12,0h-.1l-.06,0h-.31l-.08,0h-.11l-.08,0h-.14l-.06,0h-.11l-.08,0h-.14l0,0h-.14l-.06,0h-.12l0,0h-.14l-.06,0h-.15l0,0h-.14l-.05,0h-.33l0,0h-.54l0,0H1383l0,0h-.16l0,0h-.52l0,0h-.15l0,0h-.34l0,0h-.52l0,0h-.34l0,0h-.34l0,0h-.51l0,0h-.71l0,0h-.34l0,0h-2.29l0,0h-.24l0,0h-.24l0,0h-.33v0h-.25l0,0h-.17l0,0h-.1l0,0h0l-.05,0h0l0,0h0l0,0h0l0,0h0l0,0h0l0,0h0l0,0h0l-.06,0h0c-1.09-.77-2.13-1.59-3.13-2.43l-.2-.14a1.78,1.78,0,0,0-2.1,2.86,54.07,54.07,0,0,0,5.68,4.16Z" transform="translate(0.5 -0.5)"/>
                            <rect style="fill:#91a2c6;" x="723.75" y="624.86" width="684.32" height="611.65" rx="60.81"/>
                            <path style="fill:#c4cfe8;" d="M1212.36,1337.62l-584.88-90.29a64,64,0,0,1-53.46-73L652.65,665a64,64,0,0,1,73-53.45l584.88,90.29a63.94,63.94,0,0,1,53.45,73l-78.63,509.35A64,64,0,0,1,1212.36,1337.62Z" transform="translate(0.5 -0.5)"/>
                            <path style="fill:#a6b3d6;" d="M1310.5,701.86,725.62,611.57a64,64,0,0,0-73,53.45l-11.51,74.57,711.3,109.81L1364,774.82A63.94,63.94,0,0,0,1310.5,701.86Z" transform="translate(0.5 -0.5)"/>
                            <path style="fill:#e9ecf4;" d="M1238.87,965a25,25,0,0,1-3.84-.3L721,885.3a25,25,0,1,1,7.63-49.41l514.06,79.36a25,25,0,0,1-3.79,49.71Z" transform="translate(0.5 -0.5)"/>
                            <path style="fill:#e9ecf4;" d="M1221.47,1086.46a25,25,0,0,1-3.84-.3L703.57,1006.8a25,25,0,1,1,7.63-49.41l514.05,79.36a25,25,0,0,1-3.78,49.71Z" transform="translate(0.5 -0.5)"/>
                            <path style="fill:#e9ecf4;" d="M999.07,1167.43a25,25,0,0,1-3.84-.29l-307.72-47.51a25,25,0,1,1,7.63-49.41l307.71,47.5a25,25,0,0,1-3.78,49.71Z" transform="translate(0.5 -0.5)"/>
                            <path style="fill:#91a2c6;" d="M695.88,678.38a12.48,12.48,0,0,1-9.75-4.67l-86-106.92a12.5,12.5,0,1,1,19.48-15.67l86,106.92a12.51,12.51,0,0,1-9.73,20.34Z" transform="translate(0.5 -0.5)"/>
                            <circle style="fill:#c4cfe8;" cx="610.35" cy="558.45" r="69.94"/>
                            <circle style="fill:#c4cfe8;" cx="1407.57" cy="707.98" r="138.22" transform="translate(-87.85 1202.17) rotate(-45)"/>
                            <path style="fill:#a6b3d6;" d="M1363,650q14.1-21,45.51-21t45.52,21q14.1,21,14.11,57.5,0,36.91-14.11,57.93t-45.52,21q-31.41,0-45.51-21t-14.12-57.93Q1348.92,671,1363,650Zm64.82,24.51q-4.66-11.57-19.31-11.57t-19.31,11.57q-4.66,11.57-4.67,33a138.79,138.79,0,0,0,1.7,23.87q1.7,9.45,6.9,15.28t15.38,5.83q10.18,0,15.38-5.83t6.9-15.28a138.79,138.79,0,0,0,1.7-23.87Q1432.53,686.06,1427.86,674.5Z" transform="translate(0.5 -0.5)"/>
                        </svg>
                        <div style="width: 40%; margin: auto;text-align: center;font-weight: 500;">
                            <p>Nenhum item encontrado na lista. </br> Que tal começar a usar agora e adicionar um novo item?</p>
                            <a href="{{ route('agents.create') }}" class="btn_titleComponent" style="margin: 20px 0; display: inline-table;" >Cadastrar novo agente </a>
                        </div>
                       
                    </div>
                @else
                    <div class="bloco_info_details_header" style="height: 80px; margin-top: 20px; justify-content: end;">
                        <form id="filterFormList" style="display: grid;">
                            <div style="display: inline-flex ;">
                                <div class="inputField">
                                    <select name="sort_order" style="width:190px;">
                                        <option value="id_asc" selected>Ordem crescente</option>
                                        <option value="id_desc">Ordem decrescente</option>
                                        <option value="name_asc">De A a Z</option>
                                        <option value="name_desc">De Z a A</option>
                                    </select>
                                </div>
                            </div>
                        </form> 
                    </div>
               
                    <table id="userTable" class="tabela">
                        <thead>
                            <tr>
                                <th style="width:5%;">Cód</th>
                                <th style="width:60%;">Nome:</th>
                                <th style="width:10%; text-align:center;">Data do Cadastro</th>
                                <th style="width:7%; text-align:center;" >IA Engine</th>
                                <th style="width:7%; text-align:center;" >Status</th>
                                <th style="width:13%; text-align:center;">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="body_table">
                
                        </tbody>
                    </table>

                    <nav id="paginationLinks"></nav>
                
                @endif
                

            </div>
        </div>
    </div>
</x-app-layout>

<script>
    $(document).ready(function () {
        function fetchUsers(url = "{{ route('agents.filter') }}") {
            $.ajax({
                url: url,
                method: 'GET',
                data: $('#filterFormList').serialize(),
                success: function (response) {
                    // Atualizar tabela
                    let rows = '';
                    response.data.forEach(role => {
                        const date = new Date(role.created_at);
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = String(date.getFullYear()).slice(-2);
                        const formattedDate = `${day}/${month}/${year}`;
                        
                        rows += `
                            <tr class="listaTabela" style=" min-height: 60px; max-height: 100%; ">
                              <td style="width:5%; display: flex;">#${role.id}</td>
                                <td style="width:75%; text-align:left; display: flex;">${role.agent_name}</td>
                                <td style="width:10%; text-align:center; display: flex;">${formattedDate}</td>
                                <td style="width:7%; text-align:left; display: flex; text-transform:capitalize;">${role.search_engine}</td>
                                <td style="width:7%; text-align:left; display: flex; text-transform:capitalize;">${role.status}</td>
                                <td style="width:10%; text-align:center; position:relative; display:flex;">
                                    <a class="btn_edit_row" href="/agents/${role.id}" style="margin: -6px 10px;;">
                                        <button type="submit" style="width: 17px; text-align: center; text-transform: uppercase; font-weight: bold; font-size: 13px; margin: 3px;">
                                            <svg width="17" height="17" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_329_10365)"><path d="M15.6775 8.33333C15.935 8.33333 16.1783 8.21417 16.3358 8.01083C16.4933 7.8075 16.5483 7.5425 16.485 7.29333C16.2258 6.27917 15.6975 5.3525 14.9575 4.6125L12.0533 1.70833C10.9517 0.606667 9.48667 0 7.92833 0H4.16583C1.86917 0 0 1.86917 0 4.16667V15.8333C0 18.1308 1.86917 20 4.16667 20H6.66667C7.12667 20 7.5 19.6267 7.5 19.1667C7.5 18.7067 7.12667 18.3333 6.66667 18.3333H4.16667C2.78833 18.3333 1.66667 17.2117 1.66667 15.8333V4.16667C1.66667 2.78833 2.78833 1.66667 4.16667 1.66667H7.92917C8.065 1.66667 8.2 1.67333 8.33333 1.68583V5.83333C8.33333 7.21167 9.455 8.33333 10.8333 8.33333H15.6775ZM10 5.83333V2.21583C10.3158 2.3975 10.61 2.6225 10.875 2.8875L13.7792 5.79167C14.0408 6.05333 14.265 6.34833 14.4483 6.66667H10.8333C10.3742 6.66667 10 6.2925 10 5.83333ZM19.2683 9.89917C18.3233 8.95417 16.6767 8.95417 15.7325 9.89917L10.1433 15.4883C9.51417 16.1175 9.16667 16.955 9.16667 17.8458V19.1675C9.16667 19.6275 9.54 20.0008 10 20.0008H11.3217C12.2125 20.0008 13.0492 19.6533 13.6783 19.0242L19.2675 13.435C19.74 12.9625 20 12.335 20 11.6667C20 10.9983 19.74 10.3708 19.2683 9.89917ZM18.0892 12.2558L12.4992 17.845C12.185 18.16 11.7667 18.3333 11.3208 18.3333H10.8325V17.845C10.8325 17.4 11.0058 16.9817 11.3208 16.6667L16.9108 11.0775C17.225 10.7625 17.7742 10.7625 18.0892 11.0775C18.2467 11.2342 18.3333 11.4433 18.3333 11.6667C18.3333 11.89 18.2467 12.0983 18.0892 12.2558Z" fill="#8A94AD"/></g>
                                                <clipPath id="clip0_329_10365"><rect width="20" height="20" fill="white"/></clipPath>
                                            </svg>
                                        </button>
                                    </a>
                                    <div class="btn_delete_row">
                                        <form action="agents/${role.id}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este item?');" style="margin: 0px;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit">
                                                <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5.70786 1.62227L5.45714 2.125H2.11429C1.49795 2.125 1 2.62773 1 3.25C1 3.87227 1.49795 4.375 2.11429 4.375H15.4857C16.1021 4.375 16.6 3.87227 16.6 3.25C16.6 2.62773 16.1021 2.125 15.4857 2.125H12.1429L11.8921 1.62227C11.7041 1.23906 11.3176 1 10.8963 1H6.70375C6.28241 1 5.89589 1.23906 5.70786 1.62227ZM15.4857 5.5H2.11429L2.8525 17.418C2.90821 18.3074 3.63946 19 4.52045 19H13.0796C13.9605 19 14.6918 18.3074 14.7475 17.418L15.4857 5.5Z" fill="#C8C8C8"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                </td>
                            </tr>
                        `;
                    });

                    $('#userTable .body_table').html(rows);

                    // Atualizar links de paginação
                    let pagination = '';
                    if (response.links) {
                        pagination = response.links
                             .filter(link => !["&laquo; Anterior", "Próximo &raquo;", "&laquo; Previous" , "Next &raquo;"].includes(link.label)) // Remove "Anterior" e "Próximo"
                            .map(link =>
                                `<a href="${link.url}" class="pagination-link ${link.active ? 'active' : ''}">${link.label}</a>`
                            ).join('');
                    }
                    $('#paginationLinks').html(pagination);
                }
            });
        }

        // Submeter filtros
        $('#filterFormList select').on('change', function () {
            fetchUsers();
        });

        // Navegar na paginação
        $(document).on('click', '#paginationLinks a', function (e) {
            e.preventDefault();
            const url = $(this).attr('href');
            if (url) {
                fetchUsers(url);
                const div = document.getElementById("contentBody");
                div.scrollTop = 0; 
            }
        });

        // Carregar lista inicial
        fetchUsers();
    });
    </script>
</body>
</html>
