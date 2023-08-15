<!DOCTYPE html>
<html>
<head>
   <style>
       /* Estilos del encabezado y pie de página */
       header {
           position: fixed;
           top: 0;
           left: 0;
           right: 0;
           height: 50px;
           background-color: #ccc;
           padding: 10px;
       }

       footer {
           position: fixed;
           bottom: 0;
           left: 0;
           right: 0;
           height: 50px; /* Ajusta la altura según sea necesario */
           background-color: #ccc;
           padding: 10px;
           font-size: 10px; /* Tamaño de fuente para la leyenda */
           line-height: 12px; /* Espaciado entre líneas para la leyenda */
           text-align: center; /* Alineación del texto en el centro del pie de página */
       }
   </style>
</head>
<body>
   <header>
       <!-- Contenido del encabezado -->
       <h1>Encabezado</h1>
   </header>

   <!-- Contenido del documento -->
   <main style="margin-top: 70px;"> <!-- Ajusta el margen superior según sea necesario -->
       @yield('content')
   </main>

   <footer>
       <!-- Contenido del pie de página -->
       <div>
           Monto expresado en US Dolar. Precios y disponibilidad pueden variar sin previo aviso. De ocurrir algún faltante, el cliente podrá seleccionar
           un producto de similar valor para su reemplazo. De no encontrar nada de su agrado, podrá dejar el crédito en su cuenta para ser utilizado en
           su próxima compra. Los paños adicionales a los estuches no se garantizan en ningún caso. Cualquier paño que acompañe un estuche es considerado una cortesía extra.
           No se aceptan devoluciones, solo cambios dentro de las 24hrs posteriores a la facturación.
       </div>
   </footer>
</body>
</html>
