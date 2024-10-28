<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lista de Usuários') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="col-12">

                        <div class="card ">
                            <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 30%; text-align:left; padding-left:20px; height:60px;">Usuário</th>
                                        <th scope="col" style="width: 40%; text-align:left; height:60px;">Email</th>
                                        <th scope="col" style="width: 20%; text-align:left; height:60px;">Perfil</th>
                                        <th scope="col"  style="width: 220px; height:60px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ListUsers as $User)
                                        <tr class="linha_agente" style="height:60px; border-top:1px solid #f3f4f6;">
                                            <!-- <th scope="row" style="padding-left: 25px;">{{$User['id']}}</th> -->
                                            <td class="linha_agente_nome" style="width: 20%; padding-left: 25px;">{{$User['nome']}}</td>

                                            <td style="text-align:left;">{{$User['email']}}</td>
                                                
                                            <td class="icon_perfil">{{$User['perfil']}}</td>

                                            <td>
                                                <a href="/user/{{$User['id']}}" style="margin-left:8px">
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
