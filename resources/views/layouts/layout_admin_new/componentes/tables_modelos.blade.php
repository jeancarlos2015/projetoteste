@if(!empty($modelos))
    <tbody>
    @foreach($modelos as $modelo1)
        <tr>
            {{--<td>{!! $modelo1->codmodelo !!}</td>--}}
            {{--<td>{!! $modelo1->nome !!}</td>--}}
            {{--<td>{!! $modelo1->descricao !!}</td>--}}
            {{--<td>{!! $modelo1->tipo !!}</td>--}}
            <td>
                <div class="media">
                    <img class="d-flex mr-3 rounded-circle" src="{{ Gravatar::src($modelo1->usuario->email) }}" alt="" width="100">
                    <div class="media-body">
                        <strong>{!!  $modelo1->nome !!}</strong> - 
                        <strong>{!!  $modelo1->tipo !!}</strong>
                        <div class="text-muted smaller">Código do Modelo: {!! $modelo1->codmodelo !!}</div>
                        <div class="text-muted smaller">Responsável: {!! $modelo1->usuario->name !!}</div>
                        <div class="text-muted smaller">Descrição do Modelo: {!! $modelo1->descricao !!}</div>
                    </div>
                </div>
            </td>
            <td>
                @if(Auth::user()->email===$modelo1->usuario->email)
                    @if(!empty($rota_edicao))
                        @if($modelo1->tipo==='diagramatico')

                            {{--@include('componentes.link',['id' => $modelo1->codmodelo, 'rota' => 'edicao_modelo_diagramatico'])--}}
                            @include('componentes.link',['id' => $modelo1->codmodelo, 'rota' => $rota_edicao])
                        @else
                            @include('componentes.link',['id' => $modelo1->codmodelo, 'rota' => $rota_edicao])
                        @endif

                    @endif
                    @if(!empty($rota_exclusao))
                        @include('componentes.form_delete',['id' => $modelo1->codmodelo, 'rota' => $rota_exclusao])
                    @endif
                    @if(!empty($rota_exibicao))
                        @include('componentes.link',['id' => $modelo1->codmodelo, 'rota' => $rota_exibicao,'nomebotao' => 'Visualizar'])
                    @endif
                @else
                    @if(!empty($rota_exibicao))
                        @include('componentes.link',['id' => $modelo1->codmodelo, 'rota' => $rota_exibicao,'nomebotao' => 'Visualizar'])
                    @endif
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
@endif