<?php $encabezado = $pregunta['encabezado']; ?>

<div class="row align-items-center ">
    <div class="col-12 col-lg-5 text-center text-lg-start fw-bold">
        <?= $encabezado['label'] ?>
    </div>

    <div class="col-12 col-md-6 col-lg-4 mx-lg-5">
        <div class="d-flex justify-content-lg-evenly justify-content-md-center justify-content-between">
            <?php
            // Función para evitar la repetición de código
            function mostrarElemento2($key, $valor, $alineacion) {
                if (isset($valor) && !empty($valor)) {
                    echo '<div class="text-body-secondary pb-2 pt-lg-0 ' . $alineacion . '">'; // Usa la clase pasada como parámetro
                    echo "<span>[$key]</span>";
                    echo "<p class='g-5'>$valor</p>";
                    echo '</div>';
                }
            }

            // Asumimos que las keys siempre existen y están definidas.
            $keyUno = array_key_first($encabezado['uno']);
            $keyDos = array_key_first($encabezado['dos']);

            mostrarElemento2($keyUno, $encabezado['uno'][$keyUno], 'text-wrap col-lg-5 ps-lg-3'); // Alineación a la izquierda para el key uno
            if (!empty($encabezado['uno'][$keyUno]) && !empty($encabezado['dos'][$keyDos])) {
                echo '<div></div>';
            }
            mostrarElemento2($keyDos, $encabezado['dos'][$keyDos], 'text-end text-wrap col-lg-5 pe-lg-3'); // Alineación a la derecha para el key dos
            ?>
        </div>
    </div>

    <div class="col-12 text-muted col-lg-1 text-center">
        <?php if (isset($encabezado['tres']) && !empty($encabezado['tres'])): ?>
            <?= $encabezado['tres'] ?>
        <?php endif; ?>
    </div>
</div>
