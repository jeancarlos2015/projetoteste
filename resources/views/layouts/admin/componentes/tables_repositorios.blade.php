@if(!empty($repositorios))
    <tbody>
    @foreach($repositorios as $repositorio1)
        <tr>
            {{--<td>{!! $repositorio1->cod_repositorio !!}</td>--}}
            {{--<td>{!! $repositorio1->nome !!}</td>--}}
            {{--<td>{!! $repositorio1->descricao !!}</td>--}}
            <td>
                <a href="{!! route($rota_exibicao,[$repositorio1->cod_repositorio]) !!}">
                    <div class="media">
                        <img class="d-flex mr-3 rounded-circle" src="{{ Gravatar::src('public/img/processo.png') }}"
                             alt="" width="100">
                        <div class="media-body">
                            <strong>{!!  $repositorio1->nome !!}</strong>
                            <div class="text-muted smaller">Repositório: {!! $repositorio1->nome !!}</div>
                            <div class="text-muted smaller">Usuários: {!! count($repositorio1->usuarios) !!}</div>
                            <div class="text-muted smaller">Projetos: {!! count($repositorio1->projetos) !!}</div>
                            <div class="text-muted smaller">Modelos: {!! count($repositorio1->modelos_diagramaticos) + count($repositorio1->modelos_declarativos) !!}</div>
                        </div>
                    </div>
                </a>
            </td>
            <td>
                @if (Auth::user()->email === 'jeancarlospenas25@gmail.com' || Auth::user()->tipo==='Administrador')

                    @if(!empty($rota_edicao))
                        @include('componentes.link',['id' => $repositorio1->cod_repositorio, 'rota' => $rota_edicao])
                    @endif
                    @if(!empty($rota_exclusao))
                        @include('componentes.form_delete',['id' => $repositorio1->cod_repositorio, 'rota' => $rota_exclusao])
                    @endif
                    @if(!empty($rota_exibicao))
                        @include('componentes.link',['id' => $repositorio1->cod_repositorio, 'rota' => $rota_exibicao,'nomebotao' => 'Visualizar'])
                    @endif
                @endif

            </td>
        </tr>
    @endforeach
    </tbody>
@endif