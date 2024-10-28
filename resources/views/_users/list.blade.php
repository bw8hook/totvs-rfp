<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Usuários') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                
                <div class="col-12">
                    <div class="card ">
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Usuário</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Agente</th>
                                        <th scope="col">Base de Conhecimento</th>
                                        <th scope="col">Perfil</th>
                                        <th scope="col">Excluir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ListUsers as $User)
                                        <tr>
                                            <th scope="row">{{$User['id']}}</th>
                                            <td>{{$User['nome']}}</td>
                                            <td>{{$User['email']}}</td>
                                            <td>{{$User['agente']}} - {{$User['agente_status']}} </td>
                                            <td>{{$User['base']}} - {{$User['base_status']}} </td>
                                            <td>{{$User['perfil']}}</td>
                                            
                                            <td>
                                                <a href="/delete-file/{{$User['email']}}" style="margin-left:8px">
                                                    <button type="button" class="btn-close" aria-label="Close"></button>
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
