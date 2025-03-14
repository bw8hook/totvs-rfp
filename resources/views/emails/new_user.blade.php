<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bem-vindo à nossa plataforma</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 20px;">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <tr>
                        <td align="center" style="padding: 30px 20px;">
                            <img src="https://bw8-dev-totvs-rfp.s3.us-east-1.amazonaws.com/cdn/storage/Logo.png" alt="Logo" style="width: 150px;">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 40px;">
                            <h1 style="color: #333333; font-size: 24px; margin-bottom: 20px;">Bem-vindo à nossa plataforma!</h1>

                            <p style="color: #333333; margin-bottom: 20px;">Olá {{ $data['name'] }},</p>

                            <p style="color: #333333; margin-bottom: 20px;">Estamos felizes em informar que sua conta foi criada com sucesso. Aqui estão os detalhes do seu acesso:</p>

                            <table width="100%" style="margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 8px 0; color: #666666;">Data de criação:</td>
                                    <td style="padding: 8px 0; color: #333333;">{{ $data['data'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #666666;">Email:</td>
                                    <td style="padding: 8px 0; color: #333333;">{{ $data['email'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #666666;">Senha de Acesso:</td>
                                    <td style="padding: 8px 0; color: #333333;">{{ $data['senha'] }}</td>
                                </tr>
                            </table>

                            <p style="color: #333333; margin-bottom: 20px;">Para começar a usar a plataforma, clique no botão abaixo e insira seu email e senha:</p>

                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <a href="{{ $data['url'] }}" style="display: inline-block; padding: 12px 24px; background-color: #5570F1; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold;">Acesse a Plataforma</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #666666; margin-top: 30px;">Por segurança, recomendamos que você altere a senha:</p>
                            <ul style="color: #666666; margin-bottom: 30px;">
                                <li>Use uma senha forte e única.</li>
                                <li>Ative a autenticação de dois fatores após o primeiro login.</li>
                                <li>Mantenha suas informações de login em segurança.</li>
                            </ul>

                            <p style="color: #333333;">Se tiver dúvidas, nossa equipe de suporte está à disposição para ajudar.</p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; border-top: 1px solid #eeeeee;">
                            <p style="color: #999999; font-size: 12px; margin: 0;">Este email foi gerado automaticamente. Por favor, não responda.</p>
                            <p style="color: #999999; font-size: 12px; margin: 10px 0 0 0;">Se precisar de ajuda adicional, entre em contato conosco <a href="#" style="color: #2196f3; text-decoration: none;">aqui</a>.</p>
                            <p style="color: #999999; font-size: 12px; margin: 20px 0 0 0;">© 2025 Bw8 | <a href="#" style="color: #2196f3; text-decoration: none;">Política de Privacidade</a></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
