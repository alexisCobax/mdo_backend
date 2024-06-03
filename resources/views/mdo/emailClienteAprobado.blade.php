<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="https://www.w3.org/1999/xhtml">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <title>Mayoristas De Opticas</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <style type="text/css">
        p.MsoNormal {
            margin: 0cm;
            font-size: 11.0pt;
            font-family: "Calibri", sans-serif;
        }

        a:link {
            color: blue;
            text-decoration: underline;
            text-underline: single;
        }
    </style>

</head>

<body style="margin: 0; padding: 0;">

    <p class="MsoNormal">
        <span style="font-size:18.0pt;font-family:&quot;Georgia&quot;,serif">Hola {{ $informacion['nombre'] }}!<o:p></o:p></span>
    </p>
    <p class="MsoNormal">
        <span style="font-size:18.0pt;font-family:&quot;Georgia&quot;,serif">
            <o:p>&nbsp;</o:p>
        </span>
    </p>
    <p class="MsoNormal">
        <span style="font-size:18.0pt;font-family:&quot;Georgia&quot;,serif"><strong>Tu Solicitud de Cuenta fue
                Aprobada.</strong>
            <o:p></o:p>
        </span>
    </p>
    <br />
    <br />
    <br />
    <p class="MsoNormal">
        <span style="font-size:18.0pt;font-family:&quot;Georgia&quot;,serif">Nos complace informarte que tu solicitud de
            cuenta ha
            sido aprobada y ahora eres parte de nuestra comunidad en línea, porque ya podras entrar en la tienda virtual
            B2B las
            24 horas del dia, los 7 dias de la semana.</span>
    </p>
    <br />
    <p class="MsoNormal">
        <span style="font-size:18.0pt;font-family:&quot;Georgia&quot;,serif">Para que puedas comenzar a explorar todo lo
            que Mayoristas
            de Ópticas tiene para ofrecer, hemos creado <strong>temporalmente una contraseña para ti</strong>. A
            continuación, encontrarás
            los detalles de inicio de sesión que te permitirán acceder a tu cuenta:
            <o:p></o:p>
        </span>
    </p>
    <br />
    <p class="MsoNormal">
        <span style="font-size:18.0pt;font-family:&quot;Georgia&quot;,serif">Nombre de usuario:
            {{ $informacion['usuario'] }}<o:p></o:p></span>
    </p>
    <br />
    <p class="MsoNormal">
        <span style="font-size:18.0pt;font-family:&quot;Georgia&quot;,serif">Contraseña: {{ $informacion['clave'] }}
            <o:p></o:p></span>
    </p>
    <br />

    <p class="MsoNormal">
        <span style="font-size:18.0pt;font-family:&quot;Georgia&quot;,serif">Ten en cuenta que estos detalles son
            temporales
            y te recomendamos cambiar tu contraseña una vez que inicies sesión por primera vez. Para hacerlo,
            simplemente sigue estos pasos:
        </span>
    </p>
    <br />

    <ul style="font-size:18.0pt;font-family:&quot;Georgia&quot;,serif">
        <li>Inicia sesión en tu cuenta con los detalles proporcionados.</li>
        <li>Dirígete a la sección de "Configuración" o "Perfil" en tu cuenta.</li>
        <li>Busca la opción de "Cambiar contraseña" y sigue las instrucciones para establecer una contraseña
            personalizada y segura.</li>
        <li>Recuerda que estamos aquí para ayudarte en cada paso del camino. Si tienes alguna pregunta o necesitas
            asistencia, no dudes en
            ponerte en contacto con nuestro equipo de soporte en clientes@mayoristasdeopticas.com.</li>
    </ul>
    <br />

    <p class="MsoNormal">
        <span style="font-size:18.0pt;font-family:&quot;Georgia&quot;,serif">¡Gracias por elegir Mayoristas de Ópticas!
            Esperamos que disfrutes de tus compras y que tu experiencia sea excepcional.
        </span>
    </p>

    <br />
    <p class="MsoNormal">
        <span style="font-size:18.0pt;font-family:&quot;Georgia&quot;,serif">Atentamente.
        </span>
    </p>
    <br />
    <p class="MsoNormal">
        <span style="font-size:18.0pt;font-family:&quot;Georgia&quot;,serif">Mayoristas de Ópticas
        </span>
    </p>
    <br />
    <br />
    @include('mdo.layouts.pie')
</body>

</html>
