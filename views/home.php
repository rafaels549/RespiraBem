<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <title>Mapa com Leaflet - Respira Bem</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <style>
  /* ===== RESET & BASE ===== */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: "Segoe UI", Arial, sans-serif;
  background-color: #f7f9fa;
  color: #333;
  line-height: 1.5;
  min-height: 100vh;
}

/* ===== CABEÇALHO ===== */
header {
  background: linear-gradient(90deg, #43a047, #66bb6a);
  color: #fff;
  padding: 18px 32px;
  font-size: 1.6rem;
  font-weight: bold;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

/* ===== TÍTULOS ===== */
h1 {
  text-align: center;
  margin: 30px 0 20px;
  color: #2e7d32;
  font-size: 1.8rem;
}

h2 {
  color: #388e3c;
  margin-bottom: 15px;
  text-align: center;
}

/* ===== CONTAINER PRINCIPAL ===== */
#main-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 15px;
}

#container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 30px;
  padding: 20px 0;
}

/* ===== BLOCOS ===== */
#qualidade, 
#qualidade-geral {
  background: #fff;
  padding: 20px 25px;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  min-width: 220px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

#qualidade:hover, 
#qualidade-geral:hover {
  transform: translateY(-3px);
  box-shadow: 0 3px 14px rgba(0, 0, 0, 0.1);
}

/* ===== ITENS DE QUALIDADE ===== */
.qualidade-item {
  display: flex;
  align-items: center;
  margin: 10px 0;
  font-size: 16px;
}

.circle {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  margin-right: 10px;
  border: 2px solid #eee;
  flex-shrink: 0;
}

/* Cores padronizadas com melhor contraste */
.Boa { background-color: #43a047; }
.Moderada { background-color: #fdd835; }
.Ruim { background-color: #fb8c00; }
.MuitoRuim { background-color: #e53935; }
.Perigosa { background-color: #6a1b9a; }

/* ===== MAPA ===== */
#map {
  height: 500px;
  min-width: 320px;
  width: 60%;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 3px 12px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
}

#map:hover {
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
}

/* ===== TABELA ===== */
#tabela-container {
  padding: 40px 10px;
}

#tabela-container h2 {
  color: #2e7d32;
  margin-bottom: 20px;
}

table {
  width: 100%;
  max-width: 850px;
  margin: 0 auto;
  border-collapse: collapse;
  background: white;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  font-size: 0.95rem;
}

thead {
  background: #4CAF50;
  color: #fff;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

th, td {
  padding: 12px 15px;
  text-align: center;
  border-bottom: 1px solid #eee;
}

tbody tr:hover {
  background: #f5f5f5;
}

/* Destacar última coluna (qualidade) */
tbody td:last-child {
  font-weight: bold;
}

#botao-trocar {
  margin-top: 16px;
  background-color: #1976d2;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 10px 18px;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s ease;
  font-weight: 500;
}

#botao-trocar:hover {
  background-color: #1565c0;
  transform: translateY(-2px);
}

#botao-trocar:focus {
  outline: 3px solid rgba(25, 118, 210, 0.4);
}

#botao-trocar:active {
  transform: translateY(0);
}

/* ===== MENSAGEM DE RECOMENDAÇÃO ===== */
#recomendacao-container {
  margin-top: 25px;
  text-align: center;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
}

#recomendacao-mensagem {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  border-radius: 10px;
  padding: 14px 20px;
  font-size: 1rem;
  color: #fff;
  font-weight: 500;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
  animation: fadeIn 0.4s ease;
}

#recomendacao-icone {
  font-size: 1.4rem;
}

#recomendacao-mensagem.Boa { background-color: #43a047; }        
#recomendacao-mensagem.Moderada { background-color: #fdd835; color: #333; }
#recomendacao-mensagem.Ruim { background-color: #fb8c00; }
#recomendacao-mensagem.MuitoRuim { background-color: #e53935; }
#recomendacao-mensagem.Perigosa { background-color: #6a1b9a; }

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ===== FORMULÁRIOS ===== */
.form-group {
  display: flex;
  flex-direction: column;
  align-items: center;
  max-width: 420px;
  margin: 50px auto;
  font-family: "Inter", sans-serif;
  text-align: center;
}

.form-label {
  margin-bottom: 10px;
  color: #333;
  font-size: 1.1rem;
  font-weight: 500;
}

.form-control {
  padding: 12px 14px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 1rem;
  width: 100%;
  max-width: 320px;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-control:focus {
  border-color: #1976d2;
  box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.2);
  outline: none;
}

#botao-salvar {
  margin-top: 20px;
  background-color: #1976d2;
  color: #fff;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

#botao-salvar:hover {
  background-color: #125ea8;
  transform: translateY(-2px);
  box-shadow: 0 5px 12px rgba(0,0,0,0.2);
}

#botao-salvar:active {
  transform: translateY(0);
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

/* ===== RESPONSIVIDADE ===== */
@media (max-width: 900px) {
  #container {
    flex-direction: column;
    align-items: center;
    gap: 20px;
  }

  #map, #qualidade, #qualidade-geral {
    width: 90%;
  }

  h1 {
    font-size: 1.5rem;
  }
}
  </style>
</head>
<body>
  <header>
    Respira Bem
  </header>
   <?php
        use Rafael\RespiraBem\services\ViewRender;
        echo ViewRender::renderComponent('__selectDoencaRespiratoria');
    ?>
<div id="recomendacao-container" class="hidden">
  <div id="recomendacao-mensagem">
    <span id="recomendacao-texto"></span>
  </div>
  <div>
  <button  id="botao-trocar">Trocar doença respiratória</button>
  </div>
</div>
 
 <div id ="main-content">  
  <h1>Aqui está sua localização atual</h1>

  <div id="container">
     
    <div id="qualidade">
      <h2>Qualidade do ar</h2>
    </div>

    <div id="qualidade-geral">
      <h2>Qualidade Geral</h2>
      
    </div>

    <div id="map">Carregando mapa...</div>
  </div>


  <div id="tabela-container">
    <h2>Matriz de Poluentes</h2>
    <table>
      <thead>
        <tr>
          <th>Poluente</th>
          <th>Valor (µg/m³)</th>
          <th>Qualidade</th>
        </tr>
      </thead>
      <tbody id="tabela-poluentes"></tbody>
    </table>
  </div>
  </div> 

  <script>

    document.addEventListener('DOMContentLoaded', function() {
      if(!localStorage.getItem('doenca_respiratoria')){
        document.getElementById('doenca_respiratoria_container').style.display = 'block';
       document.getElementById('main-content').style.display = 'none';
       const recomendacaoContainer = document.getElementById('recomendacao-container');
      recomendacaoContainer.style.display = 'none';
      } else {
        document.getElementById('doenca_respiratoria_container').style.display = 'none';
        carregarMapaELocalizacao();
      }
       
    });

    document.getElementById('botao-trocar').addEventListener('click', function() {
      localStorage.removeItem('doenca_respiratoria');
      document.getElementById('doenca_respiratoria_container').style.display = 'block';
      document.getElementById('main-content').style.display = 'none';
      const recomendacaoContainer = document.getElementById('recomendacao-container');
      recomendacaoContainer.style.display = 'none';
    });
    function salvarDoencaRespiratoria() {
      document.getElementById('main-content').style.display = 'block';
      const selectDoenca = document.querySelector('select[name="doenca_respiratoria"]');
      const selectedDisease = selectDoenca.value;
      localStorage.setItem('doenca_respiratoria', selectedDisease);
      carregarMapaELocalizacao();
      document.getElementById('doenca_respiratoria_container').style.display = 'none';
      const recomendacaoContainer = document.getElementById('recomendacao-container');
      recomendacaoContainer.style.display = 'block';
    }

    // Adiciona os círculos de qualidade
    const arrayDeQualidade = ["Boa", "Moderada", "Ruim", "Muito Ruim", "Perigosa"];
    arrayDeQualidade.forEach(function(qualidade) {
      const item = document.createElement('div');
      item.className = 'qualidade-item';
      const circle = document.createElement('div');
      circle.className = 'circle ' + qualidade.replace(/\s/g, '');
      const label = document.createElement('span');
      label.textContent = qualidade;
      item.appendChild(circle);
      item.appendChild(label);
      document.getElementById('qualidade').appendChild(item);
    });

  const tabelas = {
  pm10: [
    { C0: 0,   C1: 45,   I0: 0,   I1: 40 },   
    { C0: 45,  C1: 100,  I0: 41,  I1: 80 },   
    { C0: 100, C1: 150,  I0: 81,  I1: 120 },  
    { C0: 150, C1: 250,  I0: 121, I1: 200 },  
    { C0: 250, C1: 600,  I0: 201, I1: 400 },  
  ],

  pm2_5: [
    { C0: 0,   C1: 15,   I0: 0,   I1: 40 },   
    { C0: 15,  C1: 50,   I0: 41,  I1: 80 },   
    { C0: 50,  C1: 75,   I0: 81,  I1: 120 },  
    { C0: 75,  C1: 125,  I0: 121, I1: 200 },  
    { C0: 125, C1: 300,  I0: 201, I1: 400 },  
  ],

  o3: [
    { C0: 0,    C1: 100,  I0: 0,   I1: 40 },   
    { C0: 100,  C1: 130,  I0: 41,  I1: 80 },   
    { C0: 130,  C1: 160,  I0: 81,  I1: 120 },  
    { C0: 160,  C1: 200,  I0: 121, I1: 200 },  
    { C0: 200,  C1: 800,  I0: 201, I1: 400 },  
  ],

  co: [
    { C0: 0,  C1: 9,   I0: 0,   I1: 40 },   
    { C0: 9,  C1: 11,  I0: 41,  I1: 80 },   
    { C0: 11, C1: 13,  I0: 81,  I1: 120 },  
    { C0: 13, C1: 15,  I0: 121, I1: 200 },  
    { C0: 15, C1: 50,  I0: 201, I1: 400 },  
  ],

  no2: [
    { C0: 0,    C1: 200,   I0: 0,   I1: 40 },   
    { C0: 200,  C1: 240,   I0: 41,  I1: 80 },   
    { C0: 240,  C1: 320,   I0: 81,  I1: 120 },  
    { C0: 320,  C1: 1130,  I0: 121, I1: 200 },  
    { C0: 1130, C1: 3750,  I0: 201, I1: 400 },  
  ],

  so2: [
    { C0: 0,    C1: 40,    I0: 0,   I1: 40 },   
    { C0: 40,   C1: 50,    I0: 41,  I1: 80 },   
    { C0: 50,   C1: 125,   I0: 81,  I1: 120 },  
    { C0: 125,  C1: 800,   I0: 121, I1: 200 },  
    { C0: 800,  C1: 2620,  I0: 201, I1: 400 },  
  ]
};
    function calcularIndice(poluente, C) {
      const faixas = tabelas[poluente];
      if (!faixas) return null;
      for (let faixa of faixas) {
        if (C >= faixa.C0 && C <= faixa.C1) {
          return ((faixa.I1 - faixa.I0) / (faixa.C1 - faixa.C0)) * (C - faixa.C0) + faixa.I0;
        }
      }
      return null;
    }

    function calcularIQAr(components) {
      let indices = [];
      for (let poluente in components) {
        if (tabelas[poluente]) {
           let valor = components[poluente];
      if (poluente === 'co') {
        valor = Math.round(valor * 10) / 10; 
      } else {
        valor = Math.round(valor); 
      }
          const indice = calcularIndice(poluente, valor);
          if (indice !== null) indices.push(indice);
        }
      }
      return Math.max(...indices);
    }
function carregarMapaELocalizacao() {
    // --- Mapa + API ---
    navigator.geolocation.getCurrentPosition(function(position) {
      const lat = position.coords.latitude;
      const lon = position.coords.longitude;

      const map = L.map('map').setView([lat, lon], 13);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map);

      L.marker([lat, lon]).addTo(map).bindPopup('Você está aqui!').openPopup();
    
      fetch(`http://localhost:8080/get_pollutitions?lat=${lat}&lon=${lon}`)
        .then(response => response.json())
        .then(data => {
          const components = data.data.list[0].components;
          const apiAQI = data.data.list[0].main.aqi;

          const matrizPoluentes = Object.entries(components).map(([poluente, valor]) => {
            return {
              poluente: poluente.toUpperCase().replace('_', '.'),
              valor: valor,
              qualidade: getQualidadePoluente(poluente, valor)
            };
          });

          const tbody = document.getElementById('tabela-poluentes');
          matrizPoluentes.forEach(item => {
            if(item.poluente === 'NH3' || item.poluente === 'NO') return;

            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${item.poluente}</td>
              <td>${item.valor.toFixed(2)}</td>
              <td style="color: ${getColor(item.qualidade)}">${item.qualidade}</td>
            `;
            tbody.appendChild(tr);
          });

          
          const iqAr = calcularIQAr(components);
               
          const qualidadeDoArCalculo = getQualidadeDoAr(iqAr);

          generateCircleSVG(qualidadeDoArCalculo);
           
          console.log('IQAr (cálculo):', iqAr, qualidadeDoArCalculo);
          console.log('IQAr (API):', apiAQI, getQualidadeDoAPI(apiAQI));
          if(localStorage.getItem('doenca_respiratoria')){
            const doencaSelecionada = localStorage.getItem('doenca_respiratoria');
            const recomendacao = getRecomendacao(doencaSelecionada, qualidadeDoArCalculo);

            const recomendacaoContainer = document.getElementById('recomendacao-container');
            const recomendacaoMensagem = document.getElementById('recomendacao-mensagem');
            recomendacaoMensagem.textContent = recomendacao;
            recomendacaoMensagem.className = 'qualidade-item ' + qualidadeDoArCalculo.replace(/\s/g, '');
            recomendacaoContainer.style.display = 'block';
            
          }
        })
        .catch(error => {
          console.error('Erro ao buscar poluentes:', error);
        });
    }, function(error) {
      alert('Não foi possível obter sua localização: ' + error.message);
    });
  }

    // Classificação simplificada
    function getQualidadePoluente(poluente, valor) {
      switch (poluente) {
        case 'pm2_5':
          if (valor <= 12) return "Boa";
          if (valor <= 35.4) return "Moderada";
          if (valor <= 55.4) return "Ruim";
          if (valor <= 150.4) return "Muito Ruim";
          return "Perigosa";
        case 'pm10':
          if (valor <= 54) return "Boa";
          if (valor <= 154) return "Moderada";
          if (valor <= 254) return "Ruim";
          if (valor <= 354) return "Muito Ruim";
          return "Perigosa";
        case 'o3':
          if (valor <= 54) return "Boa";
          if (valor <= 70) return "Moderada";
          if (valor <= 85) return "Ruim";
          if (valor <= 105) return "Muito Ruim";
          return "Perigosa";
        case 'no2':
          if (valor <= 53) return "Boa";
          if (valor <= 100) return "Moderada";
          if (valor <= 360) return "Ruim";
          if (valor <= 649) return "Muito Ruim";
          return "Perigosa";
        case 'so2':
          if (valor <= 35) return "Boa";
          if (valor <= 75) return "Moderada";
          if (valor <= 185) return "Ruim";
          if (valor <= 304) return "Muito Ruim";
          return "Perigosa";
        case 'co':
          if (valor <= 4.4) return "Boa";
          if (valor <= 9.4) return "Moderada";
          if (valor <= 12.4) return "Ruim";
          if (valor <= 15.4) return "Muito Ruim";
          return "Perigosa";
        default:
          return "Desconhecida";
      }
    }
     function getQualidadeDoAr(iqAr) {
      if (iqAr <= 50) return "Boa";
      if (iqAr <= 100) return "Moderada";
      if (iqAr <= 150) return "Ruim";
      if (iqAr <= 200) return "Muito Ruim";
      return "Perigosa";
    }
    function getColor(qualidade) {
      switch (qualidade) {
        case "Boa": return "green";
        case "Moderada": return "yellow";
        case "Ruim": return "orange";
        case "Muito Ruim": return "red";
        case "Perigosa": return "darkred";
        default: return "gray";
      }
    }

    function getQualidadeDoAPI(aqi) {
      switch (aqi) {
        case 1: return "Boa";
        case 2: return "Moderada";
        case 3: return "Ruim";
        case 4: return "Muito Ruim";
        case 5: return "Perigosa";
        default: return "Desconhecida";
      }
    }

function getRecomendacao(doenca, qualidade) {
  const mensagens = {
    geral: {
      Boa: "A qualidade do ar é considerada satisfatória, atividades ao ar livre podem ser realizadas normalmente.",
      Moderada: "A qualidade do ar é considerada moderada, atividades ao ar livre podem ser realizadas normalmente.",
      Ruim: "A qualidade do ar é considerada ruim. Atividades físicas intensas ao ar livre devem ser evitadas, e atividades leves podem ser realizadas, mas deve-se ficar atento ao surgimento de sintomas como tosse seca, cansaço, ardor nos olhos, nariz e garganta.",
      MuitoRuim: "A qualidade do ar é considerada muito ruim. Atividades físicas ao ar livre devem ser evitadas, e sempre que possível permaneça em ambientes fechados. Fique atento a sintomas como tosse seca, cansaço, ardor nos olhos, nariz e garganta, além de falta de ar ou respiração ofegante. Utilize máscaras com filtro quando estiver em contato com o ambiente externo.",
      Perigosa: "A qualidade do ar é considerada péssima. É fortemente recomendado evitar qualquer exposição ao ar livre. Mantenha portas e janelas fechadas, utilize purificadores de ar se possível e fique atento ao agravamento de sintomas como tosse seca, cansaço, ardor nos olhos, nariz e garganta, além de falta de ar ou respiração ofegante. Procure atendimento médico em caso de piora. O uso de máscaras com filtro é indicado sempre que estiver em contato com o ambiente externo."
    },
    grupo_risco: {
      Boa: "A qualidade do ar é considerada satisfatória, atividades ao ar livre podem ser realizadas normalmente.",
      Moderada: "A qualidade do ar é moderada. Recomenda-se reduzir o esforço físico intenso ao ar livre. Pessoas sensíveis podem apresentar sintomas leves, como tosse e cansaço.",
      Ruim: "A qualidade do ar é considerada ruim. Recomenda-se evitar qualquer esforço físico ao ar livre e priorizar a permanência em ambientes fechados. Utilize máscaras com filtro em áreas altamente poluídas.",
      MuitoRuim: "A qualidade do ar é considerada muito ruim. Recomenda-se evitar qualquer exposição ao ar livre e permanecer em ambientes fechados. Fique atento ao agravamento de sintomas como tosse seca, cansaço, ardor nos olhos, nariz e garganta, bem como episódios de falta de ar ou respiração ofegante. Em caso de piora, procure atendimento médico. Utilize máscaras com filtro quando estiver em contato com o ambiente externo.",
      Perigosa: "A qualidade do ar é considerada péssima. Evite totalmente a exposição ao ar livre. Mantenha portas e janelas fechadas, utilize purificadores de ar, e observe o agravamento de sintomas como tosse seca, cansaço, ardor nos olhos, nariz e garganta, além de falta de ar ou respiração ofegante. Procure atendimento médico em caso de piora. O uso de máscaras com filtro é indicado sempre que estiver em contato com o ambiente externo."
    }
  };

  const tipo = (doenca === "Nenhuma" || doenca === null) ? "geral" : "grupo_risco";
  
  return mensagens[tipo][qualidade];
}
function generateCircleSVG(qualidadeAqiAr) {
      const item = document.createElement('div');
      item.className = 'qualidade-item';
      const circle = document.createElement('div');
      circle.className = 'circle ' + qualidadeAqiAr.replace(/\s/g, '');
      const label = document.createElement('span');
      label.textContent = qualidadeAqiAr;
      item.appendChild(circle);
      item.appendChild(label);
      document.getElementById('qualidade-geral').appendChild(item);
}
</script>
</body>
</html>
