<x-app-layout>
    <div class="flex min-w-full flex-col">
        <div style="display: flex; padding-top: 10px; align-self: end;">
            <x-profile-bar>
            </x-profile-bar>
        </div>
    </div>
    <div class="flex flex-col">

        <div class="w-full max-w-md mx-auto justify-center items-center flex flex-column" style="width: 85vw; min-height: 73vh; max-width: 90%;">

                <x-title-component :showButton="false"> Dados da RFP Respondida </x-title-component>

                <div style="padding: 2px;"></div>

                <script>
                    let fileListGlobal = [];
                </script>

                <meta name="csrf-token" content="{{ csrf_token() }}">

                <div class="bg-white rounded-lg shadow-md p-8 w-full" style=" margin-bottom: 100px; position:relative;">
                
                <h3 style=" font-size: 26px; font-weight: 500; line-height: 35.46px; text-align: center; text-underline-position: from-font; text-decoration-skip-ink: none; color:#30344D;">Confira aqui as respostas geradas pela IA</h3>

                <div style="width: 666px; margin: auto;  margin-top:30px;">

                    <div class="bloco_importacao_topo" style="height: 110px;">
                        <div>Total de Requisitos</div>
                        <h6 style="color:#05CD99; font-size:40px; line-height: 40px; "> {{$answered + $unanswered}}</h6>
                    </div>

                    <div class="bloco_importacao_topo" style="height: 110px;">
                        <div>Respondidas pela IA</div>
                        <h6 style="font-size:40px; line-height: 40px; "> {{$answered}} </h6>
                    </div>

                    <div class="bloco_importacao_topo" style="height: 110px;">
                        <div>Não Respondidas</div>
                        <h6 style="font-size:40px; line-height: 40px; ">{{$unanswered}} </h6>
                    </div>
                </div>

                
                <div style="display: inline-block; margin-left: 380px; margin-top: 40px;">
                    <div style="float:left;">
                        <h2 style="font-size: 70px; font-weight: 500; line-height: 95.48px; color:#9A9AA9 ">{{$PercentageAnswered}}%</h2>
                        <h5 style="font-size: 20px; font-weight: 500; line-height: 15.48px; color:#5E6470 ">RESPOSTAS IA</h5>
                    </div>

                    <a href="{{$filepath}}" id="downloadButton" target="_blank" class="px-4 py-2" style="display: block; background-color: #5570F1; width: 327px; height: 58px; border-radius: 10px; color: white; box-shadow: 0px 19px 34px -20px #43BBED; float: left; margin-top: 30px; text-align: center; line-height: 0px; margin-left: 100px;">
                        <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-top:10px;">
                            <path fill="#FFF" clip-rule="evenodd" d="M8 10C8 7.79086 9.79086 6 12 6C14.2091 6 16 7.79086 16 10V11H17C18.933 11 20.5 12.567 20.5 14.5C20.5 16.433 18.933 18 17 18H16.9C16.3477 18 15.9 18.4477 15.9 19C15.9 19.5523 16.3477 20 16.9 20H17C20.0376 20 22.5 17.5376 22.5 14.5C22.5 11.7793 20.5245 9.51997 17.9296 9.07824C17.4862 6.20213 15.0003 4 12 4C8.99974 4 6.51381 6.20213 6.07036 9.07824C3.47551 9.51997 1.5 11.7793 1.5 14.5C1.5 17.5376 3.96243 20 7 20H7.1C7.65228 20 8.1 19.5523 8.1 19C8.1 18.4477 7.65228 18 7.1 18H7C5.067 18 3.5 16.433 3.5 14.5C3.5 12.567 5.067 11 7 11H8V10ZM13 11C13 10.4477 12.5523 10 12 10C11.4477 10 11 10.4477 11 11V16.5858L9.70711 15.2929C9.31658 14.9024 8.68342 14.9024 8.29289 15.2929C7.90237 15.6834 7.90237 16.3166 8.29289 16.7071L11.2929 19.7071C11.6834 20.0976 12.3166 20.0976 12.7071 19.7071L15.7071 16.7071C16.0976 16.3166 16.0976 15.6834 15.7071 15.2929C15.3166 14.9024 14.6834 14.9024 14.2929 15.2929L13 16.5858V11Z" fill="#000000"/>
                        </svg>
                        <span style="margin-top: -11px; float: left; margin-left: 39px;">Download Arquivo Respondido</span> 
                    </a>
                </div>


                <div style="display: inline-block; margin-left: 380px; margin-top: 60px; margin-bottom:50px;">
                    <div style="float:left;">
                        <h2 style="font-size: 70px; font-weight: 500; line-height: 95.48px; color:#f99992 ">{{$PercentageUnAnswered}}%</h2>
                        <h5 style="font-size: 20px; font-weight: 500; line-height: 15.48px; color:#5E6470 ">SEM RESPOSTAS</h5>
                    </div>

                    <a disabled id="downloadButton" target="_blank" class="px-4 py-2" style="opacity:0.2;  display: block; background-color: #9A9AA9; width: 198px; height: 58px; border-radius: 10px; color: white; box-shadow: 0px 19px 34px -20px #43BBED; float: left; margin-top: 30px; text-align: center; line-height: 0px; margin-left: 88px;">
                        <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-top:7px">
                            <path d="M20 4L3 9.31372L10.5 13.5M20 4L14.5 21L10.5 13.5M20 4L10.5 13.5" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span style="margin-top: -11px; float: left; margin-left: 39px;">Abrir Chamado</span> 
                    </a>
                </div>


               

                <!-- <div style="width: 250px; margin: auto; margin-top: 36px;">
                    <canvas id="pieChart"></canvas>
                </div> -->


                    
                </div>





        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>
        // Dados vindos do Laravel
        const answered = {{ $answered }};
        const unanswered = {{ $unanswered }};

        const ctx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Respondidas', 'Não Respondidas'],
                datasets: [{
                    data: [answered, unanswered],
                    backgroundColor: ['#4CAF50', '#f15555'], // Verde e vermelho
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>

