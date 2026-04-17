<?php

namespace Source\Support;

// Classe para enviar SMS via API Okulanda
// Atualizado para refletir a estrutura correta e o nome da classe  
// conforme o exemplo fornecido.

class Sms
{
    private $apiUrl = 'https://appsms.okulanda.ao'; // Atualizado conforme Postman
    private $authnif;
    private $remetekey;

    public function __construct($authnif, $remetekey)
    {
        $this->authnif = $authnif;
        $this->remetekey = $remetekey;
    }

    // Método para enviar SMS
    public function enviar($numero, $mensagem, $CEspecial = 'S')
    {
        $data = [
            'ReciverNumbers' => $numero,
            'TextSms'        => $mensagem,
            'CEspecial'      => $CEspecial
        ];
        // Verifica se o número é um array e converte para string
        if (is_array($numero)) {
            $data['ReciverNumbers'] = implode(',', $numero);
        }
        return $this->request('/sms/send', $data, 'POST', true);
    }

    public function saldo()
    {
        return $this->request('/account/balance', [], 'GET');
    }

    // Função interna para fazer requisições cURL
    private function request($endpoint, $params = [], $method = 'GET', $isMultipart = false)
    {
        $url = $this->apiUrl . $endpoint;
        $headers = [
            'Authnif: ' . $this->authnif,
            'Remetekey: ' . $this->remetekey
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($isMultipart) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            } else {
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['erro' => $error];
        }

        return json_decode($response, true);
    }
}
