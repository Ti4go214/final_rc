# GeoDados - Sistema de Gestão Geográfica Interativo 🌍

## 📝 Descrição do Projeto
O **GeoDados** é uma plataforma web avançada de Sistemas de Informação Geográfica (SIG) desenvolvida em PHP. O projeto permite a gestão completa de Pontos de Interesse (POIs) num mapa interativo, oferecendo ferramentas potentes para visualização, análise e interação com dados geográficos.

## 🚀 Funcionalidades Principais

### 🗺️ Mapa e Visualização Avançada
- **Marcadores Inteligentes**: Ícones SVG personalizados com cores e símbolos dinâmicos baseados na categoria.
- **Marker Clustering**: Agrupamento automático de marcadores para manter a clareza visual em áreas com alta densidade de pontos.
- **Popups Detalhados**: Janelas informativas com fotografias (thumbnails), moradas formatadas e botões de ação rápida.
- **Temas Visuais (Dark Mode)**: Alternância instantânea entre modo claro (OpenStreetMap) e modo escuro (CartoDB Dark) com ajuste automático da interface.

### 📍 GPS e Geolocalização
- **Minha Localização**: Botão de posicionamento em tempo real que mostra o utilizador no mapa com indicador de precisão.
- **Cálculo de Rotas**: Integração com *Leaflet Routing Machine* para traçar percursos GPS da localização atual do utilizador até qualquer ponto de interesse.
- **Geocodificação Inversa**: Ao clicar em qualquer local do mapa, o sistema consome a API **Nominatim** para detetar automaticamente a morada, cidade e país, facilitando o registo de novos locais.

### ⭐ Sistema de Avaliações e Social
- **Classificação por Estrelas**: Sistema interativo de 1 a 5 estrelas para avaliar locais.
- **Média Dinâmica**: Cálculo automático da média de classificações e contagem de votos exibida em tempo real nos popups.
- **Segurança de Voto**: Lógica de backend que permite apenas uma avaliação por utilizador para cada local (com opção de atualizar a nota).
- **Partilha por Email**: Possibilidade de enviar os detalhes de um local (Nome e Morada) diretamente para qualquer endereço de email.

### 🛠️ Gestão e Administração (CRUD)
- **Gestão de Locais**: Inserção de novos pontos com upload de fotografias e dados geográficos precisos.
- **Controlo de Acessos (RBAC)**: Diferenciação entre Admins e Utilizadores. Permissões específicas para eliminar conteúdos (apenas o criador ou admins).
- **Painel Administrativo**:
  - **Dashboard Analítico**: Gráficos estatísticos interativos (Chart.js) sobre a distribuição de locais por categoria e país.
  - **Gestão de Utilizadores**: CRUD completo para controlo de contas.
  - **Gestão de Categorias**: Personalização de nomes, cores e ícones para as categorias do mapa.

### 🔍 Pesquisa e Filtros
- **Filtros em Tempo Real**: Filtragem instantânea por Nome, País ou Categoria sem necessidade de recarregar a página (AJAX/JS).
- **Estatísticas na Sidebar**: Contadores dinâmicos de locais e categorias ativos na vista atual do mapa.

## 🛠️ Tecnologias Utilizadas
- **Core**: PHP 8.x, MySQL (MariaDB)
- **Frontend**: JavaScript (Vanilla ES6), HTML5, CSS3 (com Glassmorphism e Flexbox/Grid)
- **Bibliotecas de Mapas**: Leaflet.js, Leaflet.markercluster, Leaflet Routing Machine
- **APIs Externas**: Nominatim (OpenStreetMap) para Geocoding
- **Gráficos**: Chart.js
- **Design**: Google Fonts (Poppins), FontAwesome-like SVG icons

## 💻 Instruções para Executar Localmente

### Pré-requisitos
- Servidor local (WAMP, XAMPP ou Laragon)
- PHP 7.4 ou superior
- MySQL/MariaDB

### Passos de Instalação
1. **Preparação dos Ficheiros**:
   - Copie a pasta do projeto para `c:\wamp64\www\` (ou o diretório raiz do seu servidor).
2. **Configuração da Base de Dados**:
   - No `phpMyAdmin`, crie uma base de dados chamada `geo_dados`.
   - Importe o ficheiro `geo_dados.sql` (disponível na raiz do projeto).
3. **Ajuste de Credenciais**:
   - Edite o ficheiro `db.php` se as suas credenciais de base de dados forem diferentes das padrão:
     ```php
     $host = "localhost";
     $db   = "geo_dados";
     $user = "root";
     $pass = "123";
     ```
4. **Execução**:
   - Aceda a `http://localhost/final_rc/` no seu browser.

---
*Desenvolvido no âmbito da Unidade Curricular de Redes de Computadores / Desenvolvimento Web - 2026*

---
*Desenvolvido por Tiago Guerra - 2026*
