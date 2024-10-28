<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lista de Agentes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="col-12">

                        <div class="card ">
                            <div class="card-body">

                            <table class="table">
                                <tbody>
                                    @foreach($listaAgents as $Agent)
                                        <tr class="linha_agente">
                                            <td class="linha_agente_nome">{{$Agent->name}}</td>
                                            @if ($Agent->status == "online")
                                                <td> <div class="online">{{$Agent->status}}</div></td>
                                            @elseif ($Agent->status == "draft")
                                            <td> <div class="draft"> RASCUNHO </div></td>
                                            @endif
                                           
                                            <td>
                                               

                                                <a href="/agente/{{$Agent->agent_id}}" style="margin-left:8px">
                                                    <x-primary-button class="ms-4"> {{ __('Editar') }} </x-primary-button>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>




                                
                            </div>
                        </div>
                        </div>



            </div>
        </div>
    </div>
</x-app-layout>
