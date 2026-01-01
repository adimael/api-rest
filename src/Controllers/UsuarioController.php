<?php

namespace src\Controllers;

use src\Services\UsuarioService;
use src\Enum\NivelAcesso;
use src\Exceptions\EmailInvalidoException;
use src\Exceptions\UsernameInvalidoException;
use src\Http\Request;

class UsuarioController extends BaseController
{
    private UsuarioService $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }

    /**
     * Cria um novo usuário diretamente com dados em array
     * Útil para chamadas programáticas sem HTTP
     */
    public function criarDireto(string $nome, string $username, string $email, string $senha, string $nivelAcesso = 'comum', bool $ativo = true): array
    {
        try {
            $nivel = NivelAcesso::from($nivelAcesso);

            // Registra o usuário (Service cuida da criptografia)
            $usuario = $this->usuarioService->registrarUsuario(
                $nome,
                $username,
                $email,
                $senha,
                $nivel,
                $ativo
            );

            return [
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
            ];

        } catch (EmailInvalidoException | UsernameInvalidoException $e) {
            return [
                'sucesso' => false,
                'erro' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'sucesso' => false,
                'erro' => 'Erro ao criar usuário: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cria um novo usuário
     * POST /usuarios
     */
    public function criar(): void
    {
        try {
            // Obtém e valida os dados da requisição
            $dados = $this->obterDadosRequisicao();
            $request = new Request($dados);
            $request->validarCamposObrigatorios(['nome', 'username', 'email', 'senha']);

            // Define valores padrão
            $nivelAcesso = $request->has('nivel_acesso') 
                ? NivelAcesso::from($request->get('nivel_acesso')) 
                : NivelAcesso::USUARIO;
            
            $ativo = $request->get('ativo', true);

            // Registra o usuário (Service cuida da criptografia)
            $usuario = $this->usuarioService->registrarUsuario(
                $request->get('nome'),
                $request->get('username'),
                $request->get('email'),
                $request->get('senha'),
                $nivelAcesso,
                $ativo
            );

            // Retorna sucesso
            $this->responderSucesso([
                'mensagem' => 'Usuário criado com sucesso!',
                'usuario' => [
                    'uuid' => $usuario->getUuid(),
                    'nome' => $usuario->getNome(),
                    'username' => $usuario->getUsername(),
                    'email' => $usuario->getEmail(),
                    'nivel_acesso' => $usuario->getNivelAcesso()->value,
                    'ativo' => $usuario->isAtivo()
                ]
            ], 201);

        } catch (EmailInvalidoException | UsernameInvalidoException $e) {
            $this->responderErro($e->getMessage(), 400);
        } catch (\InvalidArgumentException $e) {
            $this->responderErro($e->getMessage(), 400);
        } catch (\Exception $e) {
            $this->responderErro('Erro ao criar usuário: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Lista todos os usuários
     * GET /api/usuarios
     */
    public function listar(): void
    {
        try {
            // Injeta o repositório diretamente via Container
            $usuarioRepository = \src\Core\Container::get(\src\Repositories\UsuarioRepository::class);
            $usuarios = $usuarioRepository->listar();

            $usuariosFormatados = [];
            foreach ($usuarios as $usuario) {
                $usuariosFormatados[] = [
                    'uuid' => $usuario->getUuid(),
                    'nome' => $usuario->getNome(),
                    'username' => $usuario->getUsername(),
                    'email' => $usuario->getEmail(),
                    'nivel_acesso' => $usuario->getNivelAcesso()->value,
                    'ativo' => $usuario->isAtivo(),
                    'criado_em' => $usuario->getCriadoEm()->format('Y-m-d H:i:s'),
                    'atualizado_em' => $usuario->getAtualizadoEm() ? $usuario->getAtualizadoEm()->format('Y-m-d H:i:s') : null
                ];
            }

            $this->responderSucesso([
                'total' => count($usuariosFormatados),
                'usuarios' => $usuariosFormatados
            ]);

        } catch (\Exception $e) {
            $this->responderErro('Erro ao listar usuários: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Busca um usuário por UUID
     * GET /api/usuarios/{uuid}
     */
    public function buscar(string $uuid): void
    {
        try {
            $usuarioRepository = \src\Core\Container::get(\src\Repositories\UsuarioRepository::class);
            $usuario = $usuarioRepository->buscarPorUuid($uuid);

            if (!$usuario) {
                $this->responderErro('Usuário não encontrado', 404);
                return;
            }

            $this->responderSucesso([
                'usuario' => [
                    'uuid' => $usuario->getUuid(),
                    'nome' => $usuario->getNome(),
                    'username' => $usuario->getUsername(),
                    'email' => $usuario->getEmail(),
                    'nivel_acesso' => $usuario->getNivelAcesso()->value,
                    'ativo' => $usuario->isAtivo(),
                    'criado_em' => $usuario->getCriadoEm()->format('Y-m-d H:i:s'),
                    'atualizado_em' => $usuario->getAtualizadoEm() ? $usuario->getAtualizadoEm()->format('Y-m-d H:i:s') : null
                ]
            ]);

        } catch (\Exception $e) {
            $this->responderErro('Erro ao buscar usuário: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Atualiza um usuário
     * PUT /api/usuarios/{uuid}
     */
    public function atualizar(string $uuid): void
    {
        try {
            $usuarioRepository = \src\Core\Container::get(\src\Repositories\UsuarioRepository::class);
            $usuario = $usuarioRepository->buscarPorUuid($uuid);

            if (!$usuario) {
                $this->responderErro('Usuário não encontrado', 404);
                return;
            }

            // Obtém e valida os dados da requisição
            $dados = $this->obterDadosRequisicao();
            $request = new Request($dados);

            // Atualiza apenas os campos fornecidos
            if ($request->has('nome')) {
                $usuario->setNome($request->get('nome'));
            }

            if ($request->has('email')) {
                $usuario->setEmail($request->get('email'));
            }

            if ($request->has('username')) {
                $usuario->setUsername($request->get('username'));
            }

            if ($request->has('nivel_acesso')) {
                $usuario->setNivelAcesso(NivelAcesso::from($request->get('nivel_acesso')));
            }

            if ($request->has('ativo')) {
                $usuario->setAtivo($request->get('ativo'));
            }

            // Define data de atualização
            $usuario->setAtualizadoEm(new \DateTimeImmutable());

            // Salva as alterações
            $usuarioRepository->atualizar($usuario);

            $this->responderSucesso([
                'mensagem' => 'Usuário atualizado com sucesso!',
                'usuario' => [
                    'uuid' => $usuario->getUuid(),
                    'nome' => $usuario->getNome(),
                    'username' => $usuario->getUsername(),
                    'email' => $usuario->getEmail(),
                    'nivel_acesso' => $usuario->getNivelAcesso()->value,
                    'ativo' => $usuario->isAtivo(),
                    'criado_em' => $usuario->getCriadoEm()->format('Y-m-d H:i:s'),
                    'atualizado_em' => $usuario->getAtualizadoEm()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (EmailInvalidoException | UsernameInvalidoException $e) {
            $this->responderErro($e->getMessage(), 400);
        } catch (\InvalidArgumentException $e) {
            $this->responderErro($e->getMessage(), 400);
        } catch (\Exception $e) {
            $this->responderErro('Erro ao atualizar usuário: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Deleta/remove um usuário
     * DELETE /api/usuarios/{uuid}
     */
    public function deletar(string $uuid): void
    {
        try {
            $usuarioRepository = \src\Core\Container::get(\src\Repositories\UsuarioRepository::class);
            $usuario = $usuarioRepository->buscarPorUuid($uuid);

            if (!$usuario) {
                $this->responderErro('Usuário não encontrado', 404);
                return;
            }

            $usuarioRepository->deletar($uuid);

            $this->responderSucesso([
                'mensagem' => 'Usuário deletado com sucesso!'
            ]);

        } catch (\Exception $e) {
            $this->responderErro('Erro ao deletar usuário: ' . $e->getMessage(), 500);
        }
    }
}
