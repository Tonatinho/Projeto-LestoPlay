<?php

class WhatsAppNotification {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function enviarConfirmacaoReserva($telefone, $nomeCliente, $nomeQuadra, $data, $horario, $preco) {
        $mensagem = $this->formatarMensagemConfirmacao($nomeCliente, $nomeQuadra, $data, $horario, $preco);
        return $this->enviarMensagem($telefone, $mensagem);
    }

    public function enviarLembrete($telefone, $nomeCliente, $nomeQuadra, $data, $horario) {
        $mensagem = $this->formatarMensagemLembrete($nomeCliente, $nomeQuadra, $data, $horario);
        return $this->enviarMensagem($telefone, $mensagem);
    }

    public function enviarCancelamento($telefone, $nomeCliente, $nomeQuadra, $data, $horario) {
        $mensagem = $this->formatarMensagemCancelamento($nomeCliente, $nomeQuadra, $data, $horario);
        return $this->enviarMensagem($telefone, $mensagem);
    }

    private function formatarMensagemConfirmacao($nomeCliente, $nomeQuadra, $data, $horario, $preco) {
        $dataFormatada = date('d/m/Y', strtotime($data));
        $horarioFormatado = date('H:i', strtotime($horario));
        $precoFormatado = number_format($preco, 2, ',', '.');

        return "🏐 *LestoPlay Arena* 🏐\n\n" .
               "✅ *Reserva Confirmada!*\n\n" .
               "Olá, {$nomeCliente}!\n\n" .
               "Sua reserva foi confirmada com sucesso:\n\n" .
               "📍 *Quadra:* {$nomeQuadra}\n" .
               "📅 *Data:* {$dataFormatada}\n" .
               "⏰ *Horário:* {$horarioFormatado}\n" .
               "💰 *Valor:* R$ {$precoFormatado}\n\n" .
               "Chegue com 15 minutos de antecedência.\n\n" .
               "Qualquer dúvida, entre em contato conosco!\n\n" .
               "_Mensagem automática do sistema LestoPlay_";
    }

    private function formatarMensagemLembrete($nomeCliente, $nomeQuadra, $data, $horario) {
        $dataFormatada = date('d/m/Y', strtotime($data));
        $horarioFormatado = date('H:i', strtotime($horario));

        return "🏐 *LestoPlay Arena* 🏐\n\n" .
               "⏰ *Lembrete de Reserva*\n\n" .
               "Olá, {$nomeCliente}!\n\n" .
               "Lembramos que você tem uma reserva hoje:\n\n" .
               "📍 *Quadra:* {$nomeQuadra}\n" .
               "📅 *Data:* {$dataFormatada}\n" .
               "⏰ *Horário:* {$horarioFormatado}\n\n" .
               "Não se esqueça! Chegue com 15 minutos de antecedência.\n\n" .
               "Nos vemos em breve! 🎾\n\n" .
               "_Mensagem automática do sistema LestoPlay_";
    }

    private function formatarMensagemCancelamento($nomeCliente, $nomeQuadra, $data, $horario) {
        $dataFormatada = date('d/m/Y', strtotime($data));
        $horarioFormatado = date('H:i', strtotime($horario));

        return "🏐 *LestoPlay Arena* 🏐\n\n" .
               "❌ *Reserva Cancelada*\n\n" .
               "Olá, {$nomeCliente}!\n\n" .
               "Sua reserva foi cancelada:\n\n" .
               "📍 *Quadra:* {$nomeQuadra}\n" .
               "📅 *Data:* {$dataFormatada}\n" .
               "⏰ *Horário:* {$horarioFormatado}\n\n" .
               "Se precisar de uma nova reserva, acesse nosso site.\n\n" .
               "Obrigado pela compreensão!\n\n" .
               "_Mensagem automática do sistema LestoPlay_";
    }

    private function enviarMensagem($telefone, $mensagem) {
        // Em um ambiente real, aqui seria integrado com uma API do WhatsApp
        // Como WhatsApp Business API, Twilio, ou similar
        
        // Por enquanto, vamos simular o envio e logar no banco de dados
        try {
            $stmt = $this->db->prepare("
                INSERT INTO LOG_WHATSAPP (telefone, mensagem, status, data_envio) 
                VALUES (:telefone, :mensagem, 'enviado', NOW())
            ");
            
            $stmt->execute([
                ':telefone' => $telefone,
                ':mensagem' => $mensagem
            ]);
            
            return true;
        } catch (Exception $e) {
            // Em caso de erro, logar como falha
            try {
                $stmt = $this->db->prepare("
                    INSERT INTO LOG_WHATSAPP (telefone, mensagem, status, erro, data_envio) 
                    VALUES (:telefone, :mensagem, 'erro', :erro, NOW())
                ");
                
                $stmt->execute([
                    ':telefone' => $telefone,
                    ':mensagem' => $mensagem,
                    ':erro' => $e->getMessage()
                ]);
            } catch (Exception $logError) {
                // Se nem o log funcionar, pelo menos não quebra o sistema
            }
            
            return false;
        }
    }

    public function getHistoricoMensagens($limite = 50) {
        $stmt = $this->db->prepare("
            SELECT * FROM LOG_WHATSAPP 
            ORDER BY data_envio DESC 
            LIMIT :limite
        ");
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

