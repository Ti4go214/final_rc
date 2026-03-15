<!--
/**
 * @brief Formulário popup para inserção de um novo local.
 *
 * Este formulário permite ao utilizador inserir os dados de um novo local,
 * incluindo informação básica, categoria, localização geográfica, contactos e foto.
 *
 * O formulário é normalmente apresentado num popup e enviado via JavaScript.
 */
-->
<div id="form-popup">

    <!--
    /**
     * @brief Formulário principal de criação de um local.
     *
     * O envio é tratado externamente (ex.: JavaScript com AJAX FormData).
     */
    -->
    <form id="formLocal" enctype="multipart/form-data">

        <!-- Título do formulário -->
        <h2>Novo Local</h2>

        <div class="input-wrapper">
            <!--
            /**
             * @brief Campo para o nome do local.
             */
            -->
            <label>Nome do Local</label>
            <input type="text" name="nome" placeholder="Ex: Torre Eiffel" required>
        </div>

        <div class="input-wrapper">
            <!--
            /**
             * @brief Seleção da categoria do local.
             */
            -->
            <label>Categoria</label>
            <select name="categoria" required>
                <?php foreach($categorias as $c): ?>
                    <option value="<?= htmlspecialchars($c['nome']) ?>"><?= htmlspecialchars($c['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; gap: 10px;">
            <div class="input-wrapper" style="flex: 1;">
                <!--
                /**
                 * @brief Campo do país (readonly, preenchido pelo Nominatim).
                 */
                -->
                <label>País</label>
                <input type="text" name="pais" id="paisInput" readonly placeholder="Auto">
            </div>

            <div class="input-wrapper" style="flex: 1;">
                <!--
                /**
                 * @brief Campo da cidade (readonly, preenchido pelo Nominatim).
                 */
                -->
                <label>Cidade</label>
                <input type="text" name="cidade" id="cidadeInput" readonly placeholder="Auto">
            </div>
        </div>

        <div class="input-wrapper">
            <!--
            /**
             * @brief Campo para a morada do local (preenchida pelo Nominatim).
             */
            -->
            <label>Morada Completa</label>
            <input type="text" name="morada" id="moradaInput" placeholder="Morada ou Arruamento">
        </div>
        
        <div style="display: flex; gap: 10px;">
            <div class="input-wrapper" style="flex: 1;">
                <!--
                /**
                 * @brief Campo para o telefone de contacto.
                 */
                -->
                <label>Telefone</label>
                <input type="text" name="telefone" placeholder="+351...">
            </div>

            <div class="input-wrapper" style="flex: 1;">
                <!--
                /**
                 * @brief Campo para o email de contacto.
                 */
                -->
                <label>Email</label>
                <input type="email" name="email" placeholder="contacto@">
            </div>
        </div>

        <div class="input-wrapper">
            <!--
            /**
             * @brief Upload de Foto do Local.
             */
            -->
            <label>Foto do Local (Opcional)</label>
            <input type="file" name="foto" accept="image/*">
        </div>

        <div class="input-wrapper">
            <!--
            /**
             * @brief Campo para uma descrição adicional do local.
             */
            -->
            <label>Descrição</label>
            <textarea name="descricao" rows="2" placeholder="Detalhes sobre este local..."></textarea>
        </div>

        <!--
        /**
         * @brief Campos ocultos para coordenadas geográficas.
         */
        -->
        <input type="hidden" name="latitude" id="latInput">
        <input type="hidden" name="longitude" id="lngInput">

        <!-- Botões de ação -->
        <div class="form-actions">
            <button type="submit" class="btn-save">Guardar Registo</button>
            <button type="button" class="btn-cancel" onclick="fecharFormulario()">Cancelar</button>
        </div>
    </form>
</div>
