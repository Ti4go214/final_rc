/**
 * @file mapa.js
 * @brief Script para criar mapa interativo integrado com a Sidebar e Filtros.
 * @details Este ficheiro gere o mapa Leaflet, integra a API Nominatim para
 *          geocodificação inversa e faz os envios AJAX para o backend PHP.
 * @author Tiago Guerra
 * @date 2026
 */

let map;
/** @var {L.MarkerClusterGroup|L.LayerGroup} markers Grupo de marcadores Leaflet para gestão de clusters. */
let markers;
/** @var {Array} todosOsLocais Cache local dos dados obtidos da API para permitir filtragem instantânea no cliente. */
let todosOsLocais = [];

/** 
 * @brief Definição dos Tiles (Camadas de Mapa) para suporte a Dark Mode.
 */
const tilesClaro = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", { attribution: "&copy; OpenStreetMap" });
const tilesEscuro = L.tileLayer("https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png", { attribution: "&copy; CartoDB" });
/** @var {boolean} modoEscuroAtivo Estado atual do esquema visual do mapa. */
let modoEscuroAtivo = false;

/** 
 * @var {number} ultimaRequisicaoNominatim Timestamp do último pedido à API Nominatim para controlo de Rate Limiting.
 */
let ultimaRequisicaoNominatim = 0;

document.addEventListener("DOMContentLoaded", init);

/**
 * @brief Função inicial executada ao carregar a página.
 */
function init() {
    map = criarMapa();
    markers = L.markerClusterGroup ? L.markerClusterGroup() : L.layerGroup();
    map.addLayer(markers);

    /** 
     * @brief Consumo inicial de dados.
     * @details Popula o array global 'todosOsLocais' via fetch API.
     */
    carregarLocais();

    /** 
     * @brief Ativação de listeners.
     * @details Configura as interações de clique no mapa e submissão do formulário lateral.
     */
    configurarEventosMapa(map);
    configurarFormulario();
}

/**
 * @brief Instancia e configura o mapa Leaflet básico.
 * @return {L.Map} Objeto map do Leaflet.
 */
function criarMapa() {
    const osm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap contributors"
    });

    const m = L.map("map", {
        center: [38.444, -9.101],
        zoom: 13,
        layers: [osm],
        zoomControl: false
    });

    L.control.zoom({ position: "topright" }).addTo(m);
    return m;
}

/**
 * @brief Faz par com api_locais.php para obter a cache inicial.
 */
function carregarLocais() {
    fetch("api_locais.php")
        .then(r => r.json())
        .then(locais => {
            /** Verifica se o PHP devolveu o erro de DB (em formato Objeto) em vez de Array */
            if (!Array.isArray(locais) && locais.status === 'erro') {
                console.error("Erro do Backend:", locais.mensagem);
                alert("Falha ao carregar locais do mapa:\n" + locais.mensagem);
                todosOsLocais = [];
                return;
            }

            todosOsLocais = locais;

            /** 
             * @brief UI: Atualização de indicadores na Sidebar.
             * @details Calcula totais de locais e categorias com base na cache carregada.
             */
            document.getElementById('totalLocais').innerText = todosOsLocais.length;
            const catsUnicas = [...new Set(todosOsLocais.map(l => l.categoria))];
            document.getElementById('totalCategorias').innerText = catsUnicas.length;

            /** Inicia a renderização visual dos pins no mapa. */
            renderizarMarcadores();
        })
        .catch(err => console.error("Erro ao carregar locais:", err));
}

function filtrarMapa() {
    const searchVal = document.getElementById('searchNome').value.toLowerCase();
    const paisSel = document.getElementById('filtroPais').value;
    const catSel = document.getElementById('filtroCategoria').value;
    renderizarMarcadores(paisSel, catSel, searchVal);
}

/**
 * @brief Adiciona os marcadores filtrados ao mapa dinamicamente.
 * @param {string} filtroPais País escolhido no filtro.
 * @param {string} filtroCat Categoria escolhida no filtro.
 * @param {string} buscaTexto Termo de busca por nome.
 */
function renderizarMarcadores(filtroPais = "", filtroCat = "", buscaTexto = "") {
    markers.clearLayers();

    if (!Array.isArray(todosOsLocais)) {
        console.warn("todosOsLocais não é um array válido para renderização.");
        return;
    }

    todosOsLocais.forEach(l => {
        const matchPais = filtroPais === "" || l.pais === filtroPais;
        const matchCat = filtroCat === "" || l.categoria === filtroCat;
        const matchTexto = buscaTexto === "" || l.nome.toLowerCase().includes(buscaTexto);

        if (matchPais && matchCat && matchTexto) {
            /** Imagem de thumbnail, se existir. (Pode ser estendido no PHP para retornar foto_capa) */
            const imagemHtml = l.foto ? `<img src="uploads/${l.foto}" class="popup-image-preview" alt="Foto Local">` : '';

            const marker = L.marker([l.latitude, l.longitude], {
                icon: iconPorCategoria(l.cor, l.letras)
            }).bindPopup(`
                <div style="min-width:180px; padding: 5px;">
                    ${imagemHtml}
                    <b>${l.nome}</b><br>
                    <small style="color:${l.cor}; font-weight:600;">${l.categoria}</small><br>
                    <p style="font-size:12px; margin:8px 0; color:#475569;">${l.morada || 'Sem morada'}</p>
                    
                    ${gerarHtmlAvaliacao(l)}

                    <hr style="border:0; border-top:1px solid #e2e8f0; margin:10px 0;">
                    <button onclick="tracarRota(${l.latitude}, ${l.longitude})" style="width:100%; margin-bottom: 5px; padding:8px; background:#10b981; color:white; border:none; border-radius:4px; font-weight:600; cursor:pointer;">🗺️ Como Chegar (10s)</button>
                    <button onclick="enviarLocalPorEmail(this, '${l.nome}')" style="width:100%; margin-bottom: 5px; padding:5px; background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; border-radius:4px; cursor:pointer;">📧 Partilhar Email</button>
                    ${gerarBotoesAcao(l)}
                </div>
            `);
            markers.addLayer(marker);
        }
    });
}

/**
 * @brief Gera o bloco HTML do sistema de avaliação em estrelas para o popup do Leaflet.
 * @param {Object} local Objeto contendo os dados do local (id, media_classificacao, etc.).
 * @return {string} Fragmento HTML com as estrelas e metadados de votos.
 */
function gerarHtmlAvaliacao(local) {
    const media = parseFloat(local.media_classificacao || 0).toFixed(1);
    const votos = local.total_votos || 0;

    /** Gerar 5 estrelas interativas */
    let starsHtml = '<div class="stars-container">';
    for (let i = 1; i <= 5; i++) {
        const char = i <= media ? '★' : '☆';
        const activeClass = i <= media ? 'active' : '';
        /** Passa o id do local e a classificacao */
        starsHtml += `<span class="star ${activeClass}" onclick="avaliarLocal(${local.id}, ${i})" title="Dar ${i} estrelas">${char}</span>`;
    }
    starsHtml += '</div>';

    return `
        <div class="rating-info">
            Média: ${media} / 5 (${votos} votos)
        </div>
        ${starsHtml}
    `;
}

/**
 * @brief Envia a avaliação do local para o backend via AJAX.
 * @param {number} localId ID único do ponto de interesse.
 * @param {number} classificacao Nota atribuída (1 a 5).
 */
function avaliarLocal(localId, classificacao) {
    if (typeof USER_ID === 'undefined' || !USER_ID) {
        alert("Precisa de iniciar sessão para avaliar locais.");
        return;
    }

    const fd = new FormData();
    fd.append('local_id', localId);
    fd.append('classificacao', classificacao);

    fetch('avaliar_local.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'ok') {
                alert(res.mensagem);
                /** Recarrega os locais do mapa para atualizar a média visualmente */
                carregarLocais();
                map.closePopup();
            } else {
                alert("Erro: " + res.mensagem);
            }
        })
        .catch(err => alert("Erro ao contactar o servidor: " + err));
}

/**
 * @brief Gera botão de eliminar consoante permissões (regra de negócios).
 * @param {Object} l Dados do local.
 * @return {string} HTML do botão ou aviso de leitura apenas.
 */
function gerarBotoesAcao(l) {
    if (typeof USER_TIPO !== 'undefined' && (USER_TIPO === 'admin' || USER_ID == l.criado_por)) {
        return `<button onclick="eliminarLocal(${l.id})" 
                style="width:100%; padding:5px; background:#fee2e2; color:#b91c1c; border:1px solid #f87171; border-radius:4px; cursor:pointer; font-weight:600;">
                🗑️ Eliminar
                </button>`;
    }
    return `<small style="display:block; text-align:center; color:#94a3b8; font-style:italic;">Apenas leitura</small>`;
}

/**
 * @brief Elimina um local com confirmação e reload da página.
 * @param {number} id ID do local a remover.
 */
function eliminarLocal(id) {
    if (confirm("Tem a certeza que deseja eliminar este local?")) {
        fetch(`eliminar_local.php?id=${id}`)
            .then(r => r.json())
            .then(res => {
                if (res.status === 'ok') location.reload();
                else alert("Erro ao eliminar: " + res.mensagem);
            });
    }
}

/**
 * @brief Trata a lógica de design do pin do marcador SVG Leaflet.
 * @param {string} cor Código hexadecimal ou nome da cor.
 * @param {string} letras Iniciais ou símbolo a exibir no centro do pin.
 * @return {L.DivIcon} Ícone personalizado para o marcador.
 */
function iconPorCategoria(cor, letras) {
    return L.divIcon({
        className: "",
        html: `
            <div class="leaflet-marker-drop" style="position:relative;width:32px;height:44px;">
                <svg width="32" height="44" viewBox="0 0 30 42" preserveAspectRatio="none">
                    <path d="M15 0 C23 0 30 7 30 15 C30 23 15 42 15 42 C15 42 0 23 0 15 C0 7 7 0 15 0 Z" fill="${cor}"/>
                    <circle cx="15" cy="15" r="10" fill="white"/>
                </svg>
                <div style="position:absolute; top:7px; left:0; width:32px; text-align:center; font-size:11px; font-weight:bold; color:${cor}; font-family:sans-serif;">
                    ${letras}
                </div>
            </div>`,
        iconSize: [32, 44],
        iconAnchor: [16, 44]
    });
}

/**
 * @brief Configura eventos de clique no mapa para captura de coordenadas.
 * @param {L.Map} map Instância do mapa Leaflet.
 */
function configurarEventosMapa(map) {
    map.on("click", function (e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        /** 
         * @brief Rate Limiting: Respeita o ToS da API Nominatim.
         * @details Garante um intervalo mínimo de 1 segundo entre pedidos de geolocalização.
         */
        const agora = Date.now();
        if (agora - ultimaRequisicaoNominatim < 1000) {
            console.warn("Ratethrottling: Por favor aguarde 1 segundo antes de clicar novamente.");
            return;
        }
        ultimaRequisicaoNominatim = agora;

        /** Inicia processo de Reverse Geocoding. */
        obterMorada(lat, lng);
    });
}

/**
 * @brief Usa o Nominatim para fazer Reverse Geocoding.
 * @param {number} lat Latitude
 * @param {number} lng Longitude
 */
function obterMorada(lat, lng) {
    const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`;

    fetch(url, { headers: { 'Accept-Language': 'pt-PT' } })
        .then(response => response.json())
        .then(data => {
            const adress = data.address;
            const moradaComp = data.display_name || "";
            const cidade = adress.city || adress.town || adress.village || adress.municipality || "";
            const pais = adress.country || "";

            abrirFormularioCheio(lat, lng, pais, cidade, moradaComp);
        })
        .catch(err => {
            console.error("Erro no Nominatim", err);
            abrirFormularioCheio(lat, lng, "", "", ""); /** Abre na mesma mas vazio */
        });
}

/**
 * @brief Abre popup com formulário parcialmente preenchido pelos dados da API Nominatim.
 * @param {number} lat Latitude clicada.
 * @param {number} lng Longitude clicada.
 * @param {string} pais País detetado.
 * @param {string} cidade Cidade/Localidade detetada.
 * @param {string} morada Morada completa formatada.
 */
function abrirFormularioCheio(lat, lng, pais, cidade, morada) {
    document.getElementById("formLocal").reset();
    document.getElementById("latInput").value = lat;
    document.getElementById("lngInput").value = lng;
    document.getElementById("paisInput").value = pais;
    document.getElementById("cidadeInput").value = cidade;
    document.getElementById("moradaInput").value = morada;

    document.getElementById("form-popup").classList.add("show");
}

/**
 * @brief Chamada pelo botão na Sidebar.
 */
function abrirFormularioVazio() {
    document.getElementById("formLocal").reset();
    document.getElementById("latInput").value = "";
    document.getElementById("lngInput").value = "";
    document.getElementById("form-popup").classList.add("show");
}

/**
 * @brief Fecha o formulário interativo de inserção
 */
function fecharFormulario() {
    document.getElementById("form-popup").classList.remove("show");
}

/**
 * @brief Atrasa e sobrepõe o submit do form e converte os dados em FormData para suportar foto.
 */
function configurarFormulario() {
    const form = document.getElementById("formLocal");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        /** Recolher os inputs garantindo que foto é anexada corretamente */
        const formData = new FormData(form);

        fetch("inserir_local.php", {
            method: "POST",
            body: formData
        })
            .then(response => response.json())
            .then(result => {
                if (result.status === "ok") {
                    fecharFormulario();
                    location.reload(); /** Recarregar para mostrar pino */
                } else {
                    alert("Erro ao inserir: " + (result.mensagem || "Desconhecido"));
                }
            })
            .catch(error => {
                console.error("Erro na request:", error);
                alert("Erro de comunicação com o servidor.");
            });
    });
}

/**
 * @brief Envia os dados de um local para um destinatário via email.
 * @details Abre um prompt para o email e faz o pedido à API 'enviar_email.php'.
 * @param {HTMLElement} btnElement O elemento do botão que disparou a ação (para feedback visual).
 * @param {string} nomeLocal O nome do local a ser partilhado.
 */
function enviarLocalPorEmail(btnElement, nomeLocal) {
    const emailDest = prompt(`Enviar detalhes de '${nomeLocal}' para que email?`);
    if (emailDest && emailDest.includes('@')) {
        btnElement.innerText = "⏳ Enviando...";

        const fd = new FormData();
        fd.append('email', emailDest);
        fd.append('nome_local', nomeLocal);

        fetch('enviar_email.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'ok') {
                    btnElement.innerText = "✅ E-mail Enviado!";
                    btnElement.style.background = "#dcfce7";
                    btnElement.style.color = "#166534";
                } else {
                    alert("Erro ao enviar: " + res.mensagem);
                    btnElement.innerText = "📧 Partilhar Email";
                }
            })
            .catch(err => {
                alert("Falha na rede.");
                btnElement.innerText = "📧 Partilhar Email";
            });
    } else if (emailDest) {
        alert("E-mail inválido.");
    }
}

/**
 * @brief Ativa geolocalização GPS e move o mapa.
 */
function minhaLocalizacao() {
    map.locate({ setView: true, maxZoom: 16 });
    map.on('locationfound', function (e) {
        const precisaoArredondada = Math.round(e.accuracy);
        L.circle(e.latlng, e.accuracy).addTo(map)
            .bindPopup("Estás num raio de " + precisaoArredondada + " metros deste ponto").openPopup();
    });
    map.on('locationerror', function () {
        alert("Não foi possível aceder à tua localização.");
    });
}

/**
 * @brief Alterna entre modo claro (OSM) e modo escuro (CartoDB).
 */
function toggleDarkMode() {
    modoEscuroAtivo = !modoEscuroAtivo;
    const btn = document.getElementById('btnDarkMode');

    if (modoEscuroAtivo) {
        map.removeLayer(tilesClaro);
        tilesEscuro.addTo(map);
        btn.innerText = "☀️";
        btn.style.background = "#f1f5f9";
        btn.style.color = "#475569";
    } else {
        map.removeLayer(tilesEscuro);
        tilesClaro.addTo(map);
        btn.innerText = "🌙";
        btn.style.background = "#1e293b";
        btn.style.color = "white";
    }
}

/**
 * @brief Cria a instância inicial do mapa.
 * @return {L.Map} Objeto de mapa configurado.
 */
function criarMapa() {
    const m = L.map("map", {
        center: [38.444, -9.101],
        zoom: 13,
        zoomControl: false
    });
    tilesClaro.addTo(m);
    L.control.zoom({ position: "topright" }).addTo(m);
    return m;
}

let controleRota = null;

/**
 * @brief Traça uma rota GPS desde a localização atual do utilizador até ao destino.
 * @param {number} latDest Latitude do destino
 * @param {number} lngDest Longitude do destino
 * @author Tiago Guerra
 */
function tracarRota(latDest, lngDest) {
    if (!navigator.geolocation) {
        alert("O seu navegador não suporta geolocalização.");
        return;
    }

    /** Tentar obter a posição atual do dispositivo */
    navigator.geolocation.getCurrentPosition(
        function (position) {
            const latOrigem = position.coords.latitude;
            const lngOrigem = position.coords.longitude;

            /** Limpar rota anterior se existir */
            if (controleRota) {
                map.removeControl(controleRota);
            }

            /** Fechar popup atual e avisar */
            map.closePopup();

            /** Criar e adicionar controlo de rota */
            controleRota = L.Routing.control({
                waypoints: [
                    L.latLng(latOrigem, lngOrigem),
                    L.latLng(latDest, lngDest)
                ],
                routeWhileDragging: false,
                language: 'en', /** 'pt' requer ficheiro extra de localizacao nao incluido no unpkg base */
                showAlternatives: true,
                fitSelectedRoutes: true,
                lineOptions: {
                    styles: [{ color: '#10b981', opacity: 0.8, weight: 6 }]
                },
                createMarker: function () { return null; } /** Nao criar marcadores extras nos extremos */
            }).on('routingerror', function (e) {
                alert("O servidor público de cálculo de rotas não está disponível de momento (possível limite técnico ou restrição local). Tente novamente mais tarde.");
                console.error("Leaflet Routing Error:", e.error);
                if (controleRota) {
                    map.removeControl(controleRota);
                    controleRota = null;
                }
            }).addTo(map);

        },
        function (error) {
            alert("Não foi possível obter a sua localização atual. Verifique as permissões de GPS.");
            console.error("Erro GPS:", error);
        },
        { enableHighAccuracy: true }
    );
}
