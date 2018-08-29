<form action="{!! route('controle_projetos.update',['id' => $projeto->cod_projeto]) !!}" method="post">
    @method('PUT')
    @csrf
    @includeIf('controle_projetos.form',
                                    [
                                    'acao' => 'Salvar e Proseguir',
                                    'dados' => $dados,
                                    'MAX' => 2,
                                    'cod_repositorio' => $repositorio->cod_repositorio
                                    ]
                                    )
</form>