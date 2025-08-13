document.addEventListener('DOMContentLoaded', function () {
  console.log('[CEP] Script carregado.');

  const paisInput = document.getElementById('pais');
  const cepInput = document.getElementById('cep');
  const loader = document.getElementById('cep-loader');

  const logradouroInput = document.getElementById('logradouro');
  const bairroInput = document.getElementById('bairro');
  const cidadeSelect = document.getElementById('cidade');
  const estadoSelect = document.getElementById('estado');
  const numeroInput = document.getElementById('numero');
  const complementoInput = document.getElementById('complemento');

  let timeout = null;

  if (!paisInput || !cepInput) {
    console.warn('[CEP] Campos "pais" ou "cep" não encontrados.');
    return;
  }

  cepInput.addEventListener('input', function () {
    const rawCep = this.value;

    clearTimeout(timeout);
    timeout = setTimeout(() => {
      const cep = rawCep.replace(/\D/g, ''); // remove tudo que não é número
      console.log(`[CEP] Apenas números: ${cep}`);

      if (paisInput.value.toLowerCase() === 'brazil' && cep.length === 8) {
        buscarCep(cep);
      } else {
        console.log('[CEP] País diferente de Brasil ou CEP incompleto.');
      }
    }, 500);
  });

  function bloquearCampos(bloquear = true) {
    console.log(`[CEP] ${bloquear ? 'Bloqueando' : 'Desbloqueando'} campos de endereço.`);
    [logradouroInput, bairroInput, cidadeSelect, estadoSelect, numeroInput, complementoInput].forEach(input => {
      if (input) input.disabled = bloquear;
    });
  }

  function buscarCep(cep) {

    if (loader) loader.classList.remove('d-none');
    bloquearCampos(true);

    fetch(`/buscar-cep/${cep}`)
      .then(async (response) => {
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
          console.warn('[CEP] A resposta não é JSON. Verifique a rota.');
          throw new Error("Resposta não é JSON");
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          cepInput.classList.remove('is-invalid');
          logradouroInput.value = data.logradouro;
          bairroInput.value = data.bairro;
          cidadeSelect.innerHTML = `<option selected>${data.cidade}</option>`;
          estadoSelect.innerHTML = `<option selected>${data.estado}</option>`;
        } else {
          console.warn('[CEP] CEP não encontrado.');
          cepInput.classList.add('is-invalid');
          limparCamposEndereco();
        }
      })
      .catch(error => {
        console.error('[CEP] Erro ao buscar CEP:', error);
        cepInput.classList.add('is-invalid');
        limparCamposEndereco();
      })
      .finally(() => {
        if (loader) loader.classList.add('d-none');
        bloquearCampos(false);
      });
  }

  function limparCamposEndereco() {
    logradouroInput.value = '';
    bairroInput.value = '';
    cidadeSelect.innerHTML = `<option>Selecionar</option>`;
    estadoSelect.innerHTML = `<option>Selecionar</option>`;
  }
});
