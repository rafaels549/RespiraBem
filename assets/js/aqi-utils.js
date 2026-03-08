//Constantes físicas
const PESO_MOLECULAR_CO = 28.01;
const VOLUME_MOLAR = 24.465;

/**
 * Converte CO de µg/m³ para ppm
 * Fórmula:
 * ppm = (µg/m³ × Vm) / (Peso Molecular × 1000)
 * Vm = 24,465 L/mol (25°C e 1 atm)
 */
function converterCOParaPPM(valorUgM3){ 
    return (valorUgM3 * VOLUME_MOLAR) / (PESO_MOLECULAR_CO * 1000); 
}

 //Normaliza valor antes do cálculo do índice
 //CO: converte para ppm e arredonda 1 casa decimal
 //Outros: arredonda para inteiro (µg/m³)
 
function normalizarValorParaCalculo(poluente, valorUgM3) {

  if (poluente === "co") {
    const ppm = converterCOParaPPM(valorUgM3);
    const ppm1Casa = Math.round(ppm * 10) / 10;
    return { valor: ppm1Casa, unidade: "ppm" };
  }

  return {
    valor: Math.round(valorUgM3),
    unidade: "µg/m³"
  };
}