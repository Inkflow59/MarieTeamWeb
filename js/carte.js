  function isNumberKey(event) {
    const charCode = event.which || event.keyCode;
    return charCode >= 48 && charCode <= 57 || charCode === 8;
  }

  function formatInput(input) {
    let value = input.value.replace(/\s+/g, '');

    value = value.substring(0, 16);

    input.value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
  }

    function isNumberKey(event) {
        const charCode = event.which || event.keyCode;
        return charCode >= 48 && charCode <= 57 || charCode === 8;
      }
    
      function formatDateInput(input) {
        let value = input.value.replace(/\D/g, '');
    
        value = value.substring(0, 4);
    
        if (value.length > 2) {
          value = value.substring(0, 2) + '/' + value.substring(2);
        }
    
        input.value = value;
      }

      function isNumberKey(event) {
        const charCode = event.which || event.keyCode;
        // Autorise les chiffres (0-9) et les touches spéciales comme retour arrière
        return charCode >= 48 && charCode <= 57 || charCode === 8;
      }