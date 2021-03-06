<ul class="navbar-nav ml-auto">
    <li class="nav-item">
        <a class="nav-link js-scroll-trigger" href="{{ route('limpar_cache') }}">Limpar Cache</a>
    </li>

    @yield('boltao_voltar')

    @yield('modo')

    @Auth

        @yield('grupo_usuarios_projeto')

        <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="{{ route('modelos_publicos') }}">Modelos Públicos</a>
        </li>

        @if(!empty(Auth::user()->repositorio) || Auth::user()->email==='jeancarlospenas25@gmail.com' || Auth::user()->tipo==='Administrador')

            @includeIf('layouts.admin.layouts.sub_componentes.li_nav',
                [
                    'link' => 'https://docs.google.com/spreadsheets/d/1knDrNAs0Ql-cSTd1dDfZK9QNlAT_xXlarwl5swHPocQ/edit?usp=sharing',
                    'nome' => 'Sugestões, Idéias e Andamento',
                    'ico' => 'fa fa-lightbulb-o'
                ])
            @if(!empty(Auth::user()->repositorio->nome))
                @includeIf('layouts.admin.layouts.sub_componentes.li_nav',
                   [
                       'nome' => 'Repositório:' .Auth::user()->repositorio->nome,
                       'ico' => 'fa fa-database'
                   ])
            @endif
        @endif



        @if(Auth::user()->email==='jeancarlospenas25@gmail.com' || Auth::user()->tipo==='Administrador')
            @includeIf('layouts.admin.layouts.sub_componentes.li_nav',
                [
                    'nome' => 'Administrador do Sistema',
                    'ico' => 'fa fa-user'
                ])
        @endif


        @if(!empty(Auth::user()->repositorio) || Auth::user()->email==='jeancarlospenas25@gmail.com' || Auth::user()->tipo==='Administrador')
            @includeIf('layouts.admin.layouts.sub_componentes.dropdown')
            @includeIf('layouts.admin.layouts.sub_componentes.status_github')
            @includeIf('layouts.admin.layouts.sub_componentes.dropdown_alerta')
        @endif

        @includeIf('layouts.admin.layouts.sub_componentes.dropdown_usuario')

    @endauth
</ul>
