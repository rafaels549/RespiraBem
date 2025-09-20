<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <title>Mapa com Leaflet - Respira Bem</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
    }

    header {
      background-color: #4CAF50;
      color: white;
      padding: 15px 30px;
      font-size: 24px;
      font-weight: bold;
    }

    h1 {
      text-align: center;
      margin: 20px 0;
      color: #333;
    }

    #container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 40px;
      padding: 20px;
    }

    #qualidade {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      min-width: 200px;
    }

    #qualidade h2 {
      margin-top: 0;
      margin-bottom: 15px;
      font-size: 20px;
      color: #4CAF50;
      border-bottom: 2px solid #4CAF50;
      padding-bottom: 5px;
    }

    .qualidade-item {
      display: flex;
      align-items: center;
      margin: 8px 0;
      font-size: 16px;
      color: #333;
    }

    .circle {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      margin-right: 10px;
      border: 1px solid #ccc;
    }

    .Boa { background-color: green; }
    .Moderada { background-color: yellow; }
    .Ruim { background-color: orange; }
    .MuitoRuim { background-color: red; }
    .Perigosa { background-color: darkred; }

    #map {
      height: 500px;
      min-width: 300px;
      width: 60%;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    @media (max-width: 768px) {
      #container {
        flex-direction: column;
        align-items: center;
      }

      #map {
        width: 90%;
      }

      #qualidade {
        width: 90%;
      }
    }

    /* Estilo da tabela */
    #tabela-container {
      padding: 20px;
    }

    #tabela-container h2 {
      text-align: center;
      color: #4CAF50;
    }

    table {
      width: 100%;
      max-width: 800px;
      margin: 0 auto;
      border-collapse: collapse;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      background: white;
      border-radius: 10px;
      overflow: hidden;
    }

    thead {
      background-color: #4CAF50;
      color: white;
    }

    th, td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
      text-align: center;
    }

    tbody td:last-child {
      font-weight: bold;
    }
  </style>
</head>
<body>
  <header>
    Respira Bem
  </header>

  <h1>Aqui está sua localização atual</h1>

  <div id="container">
    <div id="qualidade">
      <h2>Qualidade do ar</h2>
      <!-- Qualidade será inserida via JS -->
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
      <tbody id="tabela-poluentes">
        <!-- Linhas adicionadas via JS -->
      </tbody>
    </table>
  </div>

  <script>
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

    // Inicializa o mapa com localização atual
    navigator.geolocation.getCurrentPosition(function(position) {
      const lat = position.coords.latitude;
      const lon = position.coords.longitude;

      const map = L.map('map').setView([lat, lon], 13);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map);

      L.marker([lat, lon]).addTo(map)
        .bindPopup('Você está aqui!')
        .openPopup();

      const response =  fetch(`http://localhost:8081/get_pollutitions?lat=${lat}&lon=${lon}`)
          .then(response => response.json())
          .then(data => {
            const components = data.data.list[0].components;
            const matrizPoluentes = Object.entries(components).map(([poluente, valor]) => {
            return {
              poluente: poluente.toUpperCase().replace('_', '.'), 
              valor: valor,
              qualidade: getQualidadePoluente(poluente, valor)
            };
          });
            const tbody = document.getElementById('tabela-poluentes');

            matrizPoluentes.forEach(item => {
              const tr = document.createElement('tr');
              tr.innerHTML = `
                <td>${item.poluente}</td>
                <td>${item.valor}</td>
                <td style="color: ${getColor(item.qualidade)}">${item.qualidade}</td>
              `;
              tbody.appendChild(tr);
            });
            console.log('Dados dos poluentes:', data);
          })
          .catch(error => {
    console.error('Erro ao buscar poluentes:', error);
  });
    }, function(error) {
      alert('Não foi possível obter sua localização: ' + error.message);
    });
  

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

function getColor(qualidade) {
  switch (qualidade) {
    case "Boa":
      return "green";
    case "Moderada":
      return "yellow";
    case "Ruim":
      return "orange";
    case "Muito Ruim":
      return "red";
    case "Perigosa":
      return "darkred";
    default:
      return "gray";
  }
} 
  </script>
</body>
</html>
