<form action="{!! route('controle_github.store') !!}" method="post" class="form-group">
    @csrf
    <div class="form-group">
        <label>Usuário Github</label>
        <input type="text" class="form-control" name="usuario_github" required>
    </div>

    <div class="form-group">
        <label>Email Github</label>
        <input type="email" class="form-control" name="email_github" required>
    </div>


    <div class="form-group">
        <label>Senha Github</label>
        <input type="password" class="form-control" name="senha_github" required>
    </div>

    <input type="hidden" class="form-control" name="codusuario" value="{!! Auth::user()->codusuario !!}">

    <input type="submit" class="btn btn-dark form-control" value="Salvar e Atualizar">

</form>