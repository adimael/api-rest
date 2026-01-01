<?php

namespace src\Controllers;

class HealthController extends BaseController
{
    /**
     * Verifica o status da API
     * GET /health
     */
    public function check(): void
    {
        $this->responderSucesso([
            'mensagem' => 'API está funcionando!',
            'timestamp' => date('Y-m-d H:i:s'),
            'versao' => '1.0.0'
        ]);
    }
}
