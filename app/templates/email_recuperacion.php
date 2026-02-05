<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Recuperación de contraseña</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>

    body {
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
      font-family: Arial, Helvetica, sans-serif;
    }
    .container {
      width: 100%;
      padding: 20px 0;
    }
    .email-wrapper {
      max-width: 600px;
      margin: 0 auto;
      background-color: #ffffff;
      border-radius: 6px;
      overflow: hidden;
    
    }
    .header {
      background-color: #000000;
      color: #ffffff;
      padding: 16px 24px;
      font-size: 18px;
      font-weight: bold;
      text-align: center;
    }
    .content {
      padding: 24px;
      color: #333333;
      font-size: 14px;
      line-height: 1.6;
    }
    .btn-wrapper {
      text-align: center;
      margin: 24px 0;
    }
    .btn {
      display: inline-block;
      padding: 12px 24px;
      background-color: #000000;
      color: #ffffff !important;
      text-decoration: none;
      border-radius: 4px;
      font-size: 14px;
      font-weight: bold;
    }
    .footer {
      font-size: 12px;
      color: #777777;
      text-align: center;
      padding: 16px 24px 20px;
      background-color: #fafafa;
      border-top: 1px solid #e0e0e0;
    }
    .small {
      font-size: 12px;
      color: #777777;
    }
    @media (max-width: 600px) {
      .email-wrapper {
        border-radius: 0;
      }
      .content, .header, .footer {
        padding-left: 16px;
        padding-right: 16px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <table class="email-wrapper" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
      <tr>
        <td class="header">
          Recuperación de contraseña
        </td>
      </tr>
      <tr>
        <td class="content">
          <p>Hola <strong>{{NOMBRE_USUARIO}}</strong>,</p>

          <p>
            Hemos recibido una solicitud para restablecer la contraseña de tu cuenta.
            Si fuiste tú, haz clic en el siguiente botón para crear una nueva contraseña:
          </p>

          <div class="btn-wrapper">
            <a href="{{URL_RECUPERACION}}" class="btn" target="_blank">
              Restablecer contraseña
            </a>
          </div>

          <p class="small">
            Este enlace será válido durante los próximos <strong>30 minutos</strong>.
            Si no solicitaste este cambio, puedes ignorar este correo; tu cuenta seguirá siendo segura.
          </p>

          <p>Gracias por confiar en nosotros.</p>
          <p>Un saludo,<br><strong>El equipo de soporte</strong></p>
        </td>
      </tr>
      <tr>
        <td class="footer">
          Este mensaje se ha enviado automáticamente. Por favor, no respondas a este correo.
        </td>
      </tr>
    </table>
  </div>
</body>
</html>
