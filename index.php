<?php

require_once __DIR__ . '/bootstrap.php';

use src\Config\Database\PostgreSQL\Conexao;
use src\Models\Usuario;
use src\Enum\NivelAcesso;
use src\Services\UsuarioService;
use src\Repositories\UsuarioRepositoryPgsql;

try {
    // Testa a conexão com o banco
    $pdo = Conexao::getInstance();

    // Cria as instâncias necessárias
    $usuarioRepository = new UsuarioRepositoryPgsql();
    $usuarioService = new UsuarioService($usuarioRepository);

    // Dados do novo usuário
    $nome = "Adimael S.";
    $username = "adim@el.silva";
    $email = "adimael@gmail.com";
    $senha = "123456";
    $senhaHash = password_hash($senha, PASSWORD_BCRYPT); // Criptografa a senha
    $nivelAcesso = NivelAcesso::USUARIO; // Pode ser: SUPER_ADMIN, ADMIN ou USUARIO
    $ativo = true;

    // Registra o usuário (UUID gerado automaticamente)
    $usuario = $usuarioService->registrarUsuario(
        $nome,
        $username,
        $email,
        $senhaHash,
        $nivelAcesso,
        $ativo
    );

    // Exibe o resultado
    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Usuário criado com sucesso!',
        'usuario' => [
            'uuid' => $usuario->getUuid(),
            'nome' => $usuario->getNome(),
            'username' => $usuario->getUsername(),
            'email' => $usuario->getEmail(),
            'nivel_acesso' => $usuario->getNivelAcesso()->value,
            'ativo' => $usuario->isAtivo()
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (\src\Exceptions\EmailInvalidoException $e) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'erro' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (\src\Exceptions\UsernameInvalidoException $e) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'erro' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro ao criar usuário: ' . $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
